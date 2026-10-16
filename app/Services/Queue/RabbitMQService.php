<?php

namespace App\Services\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * Service RabbitMQ untuk Pengiriman & Penerimaan Antrean Pesan Asinkron
 * Menerapkan prinsip Clean Architecture, Anti-Monolith, dan Defensive Resilience (Graceful Fallback).
 */
class RabbitMQService
{
    protected string $host;
    protected int $port;
    protected string $user;
    protected string $password;
    protected string $vhost;
    protected string $queueName;

    protected ?AMQPStreamConnection $connection = null;

    public function __construct(
        ?string $host = null,
        ?int $port = null,
        ?string $user = null,
        ?string $password = null,
        string $queueName = 'hcHelpdesk'
    ) {
        $this->host = $host ?? env('RABBITMQ_HOST', 'localhost');
        $this->port = $port ?? (int) env('RABBITMQ_PORT', 5672);
        $this->user = $user ?? env('RABBITMQ_USER', 'guest');
        $this->password = $password ?? env('RABBITMQ_PASSWORD', 'guest');
        $this->vhost = env('RABBITMQ_VHOST', '/');
        $this->queueName = $queueName;
    }

    /**
     * Memeriksa ketersediaan koneksi broker RabbitMQ
     */
    public function isAvailable(): bool
    {
        try {
            $conn = $this->getConnection();
            return $conn !== null && $conn->isConnected();
        } catch (Throwable $e) {
            log_message('warning', '[RabbitMQService::isAvailable] Koneksi gagal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan koneksi stream aktif ke RabbitMQ
     */
    protected function getConnection(): ?AMQPStreamConnection
    {
        if ($this->connection !== null && $this->connection->isConnected()) {
            return $this->connection;
        }

        try {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost,
                false, // insist
                'AMQPLAIN',
                null,
                'en_US',
                0.5, // connection timeout (detik)
                0.5  // read/write timeout (detik)
            );
            return $this->connection;
        } catch (Throwable $e) {
            log_message('warning', '[RabbitMQService::getConnection] ' . $e->getMessage());
            $this->connection = null;
            return null;
        }
    }

    /**
     * Mengirimkan job payload kompresi & pemrosesan attachment ke antrean
     *
     * @param array $payload Data attachment job
     * @return bool True jika berhasil dipublish, false jika gagal (fallback)
     */
    public function publishAttachmentJob(array $payload): bool
    {
        try {
            $connection = $this->getConnection();
            if (!$connection) {
                return false;
            }

            $channel = $connection->channel();

            // Deklarasikan antrean (durable: true agar tidak hilang saat broker restart)
            $channel->queue_declare(
                $this->queueName,
                false, // passive
                true,  // durable
                false, // exclusive
                false  // auto_delete
            );

            $messageBody = json_encode($payload, JSON_THROW_ON_ERROR);
            $msg = new AMQPMessage($messageBody, [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'content_type' => 'application/json',
            ]);

            $channel->basic_publish($msg, '', $this->queueName);
            $channel->close();

            log_message('info', "[RabbitMQService] Job attachment untuk ticket {$payload['ticket_id']} berhasil dipublish ke {$this->queueName}");
            return true;
        } catch (Throwable $e) {
            log_message('error', '[RabbitMQService::publishAttachmentJob] Gagal publish: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Menutup koneksi RabbitMQ saat siklus hidup selesai
     */
    public function __destruct()
    {
        if ($this->connection !== null && $this->connection->isConnected()) {
            try {
                $this->connection->close();
            } catch (Throwable $e) {
                // Abaikan exception saat shutdown
            }
        }
    }
}
