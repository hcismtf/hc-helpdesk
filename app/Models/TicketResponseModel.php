<?php

namespace App\Models;

use App\Entities\TicketResponseEntity;

/**
 * Model Reply / Response Message Tiket (1-to-Many dari ticket_transactions)
 * Tabel: ticket_response
 */
class TicketResponseModel extends BaseModel
{
    protected $table = 'ticket_response';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = TicketResponseEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'author_name',
        'reply',
        'is_internal',
        'status',
        'priority',
        'assigned_to',
    ];

    protected $afterInsert = ['syncTicketStatusAndDates'];

    /**
     * Ambil seluruh balasan/pesan berdasarkan Ticket ID
     *
     * @param string $ticketId
     * @param string $order
     * @return array<TicketResponseEntity>
     */
    public function getResponsesByTicketId(string $ticketId, string $order = 'ASC'): array
    {
        return $this->where('ticket_id', $ticketId)
                    ->orderBy('created_date', $order)
                    ->findAll();
    }

    /**
     * Ambil timestamp first response dinamis untuk suatu tiket
     */
    public function getFirstResponseDate(string $ticketId): ?string
    {
        $first = $this->select('created_date')
                      ->where('ticket_id', $ticketId)
                      ->orderBy('created_date', 'ASC')
                      ->first();

        if (!$first) {
            return null;
        }

        return is_object($first)
            ? (string) ($first->getCreatedDate() ?? $first->created_date ?? null)
            : (string) ($first['created_date'] ?? null);
    }

    /**
     * Ambil timestamp resolusi / completion dinamis untuk suatu tiket
     */
    public function getCompletedDate(string $ticketId): ?string
    {
        $comp = $this->select('created_date')
                     ->where('ticket_id', $ticketId)
                     ->whereIn('LOWER(status)', ['resolved', 'done', 'closed'])
                     ->orderBy('created_date', 'DESC')
                     ->first();

        if (!$comp) {
            return null;
        }

        return is_object($comp)
            ? (string) ($comp->getCreatedDate() ?? $comp->created_date ?? null)
            : (string) ($comp['created_date'] ?? null);
    }

    /**
     * Sinkronisasi status tiket & tanggal SLA secara dinamis setelah balasan/respon dibuat.
     * Jika balasan membuka kembali tiket (reopen), status tiket otomatis berubah menjadi 'reopened'.
     */
    protected function syncTicketStatusAndDates(array $data): array
    {
        if (empty($data['id'])) {
            return $data;
        }

        $row = is_array($data['data']) ? $data['data'] : [];
        $ticketId = $row['ticket_id'] ?? null;
        $responseStatus = $row['status'] ?? null;

        if (!$ticketId) {
            return $data;
        }

        try {
            $ticketModel = new TicketModel();
            $ticket = $ticketModel->find($ticketId);
            if (!$ticket) {
                return $data;
            }

            $currentStatus = \App\Entities\TicketStatus::normalize(
                is_object($ticket)
                    ? ($ticket->getStatus() ?? $ticket->status ?? '')
                    : ($ticket['status'] ?? $ticket['ticket_status'] ?? '')
            );

            // Jika response memberikan status baru
            if (!empty($responseStatus)) {
                $normalizedStatus = \App\Entities\TicketStatus::normalize($responseStatus);

                // Jika status response meminta reopen atau tiket sebelumnya sudah selesai dan dibuka lagi
                if (\App\Entities\TicketStatus::isReopenStatus($responseStatus) ||
                    (\App\Entities\TicketStatus::isClosedOrResolved($currentStatus) && in_array($normalizedStatus, [\App\Entities\TicketStatus::OPEN, \App\Entities\TicketStatus::REOPENED, \App\Entities\TicketStatus::IN_PROGRESS], true))) {
                    $ticketModel->updateStatus($ticketId, \App\Entities\TicketStatus::REOPENED);
                } else {
                    $ticketModel->updateStatus($ticketId, $normalizedStatus);
                }
            } elseif (\App\Entities\TicketStatus::isClosedOrResolved($currentStatus)) {
                // Jika tiket sebelumnya sudah selesai/ditutup, dan ada balasan baru tanpa deklarasi status
                // tiket otomatis dibuka kembali menjadi 'reopened'
                $ticketModel->updateStatus($ticketId, \App\Entities\TicketStatus::REOPENED);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal syncTicketStatusAndDates pada TicketResponse: ' . $e->getMessage());
        }

        return $data;
    }
}
