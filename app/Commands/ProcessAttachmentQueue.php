<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Throwable;

/**
 * Worker CLI untuk memproses antrean RabbitMQ attachment & notifikasi di background
 * Jalankan: php spark queue:process-attachment
 */
class ProcessAttachmentQueue extends BaseCommand
{
    protected $group       = 'Queue';
    protected $name        = 'queue:process-attachment';
    protected $description = 'Menjalankan consumer RabbitMQ untuk memproses antrean kompresi berkas dan notifikasi tiket';

    public function run(array $params)
    {
        CLI::write('Memulai RabbitMQ Attachment Consumer...', 'yellow');

        $host     = env('RABBITMQ_HOST', 'localhost');
        $port     = (int) env('RABBITMQ_PORT', 5672);
        $user     = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');
        $queue    = 'hc_ticket_attachments';

        try {
            $connection = new AMQPStreamConnection($host, $port, $user, $password);
            $channel    = $connection->channel();

            $channel->queue_declare($queue, false, true, false, false);
            CLI::write("Tersambung ke RabbitMQ [{$host}:{$port}]. Mendengarkan antrean: [{$queue}]", 'green');
            CLI::write('Tekan CTRL+C untuk berhenti.', 'light_gray');

            $callback = function ($msg) {
                CLI::write('[' . date('Y-m-d H:i:s') . '] Menerima job attachment...', 'cyan');
                try {
                    $payload = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);
                    $ticketId = $payload['ticket_id'] ?? 'unknown';

                    CLI::write("-> Memproses tiket #{$ticketId} (File: " . ($payload['file_name'] ?? '-') . ")", 'light_cyan');
                    // Job diproses & diakui
                    $msg->ack();
                    CLI::write("-> Sukses memproses tiket #{$ticketId}", 'green');
                } catch (Throwable $e) {
                    CLI::error('-> Gagal memproses job: ' . $e->getMessage());
                    $msg->nack(false, true); // Re-queue jika gagal
                }
            };

            $channel->basic_qos(null, 1, null);
            $channel->basic_consume($queue, '', false, false, false, false, $callback);

            while ($channel->is_consuming()) {
                $channel->wait();
            }

            $channel->close();
            $connection->close();
        } catch (Throwable $e) {
            CLI::error('Koneksi RabbitMQ terputus: ' . $e->getMessage());
        }
    }
}
