<?php

namespace App\Models;

use App\Entities\TicketEntity;
use App\Entities\TicketStatus;

/**
 * Model utama tiket (Core Ticket Lifecycle & SLA)
 * Tabel: ticket
 */
class TicketModel extends BaseModel
{
    protected $table = 'ticket';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = TicketEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ticket_no',
        'request_type_id',
        'sla_id',
        'title',
        'description',
        'status',
        'reporter_id',
        'reporter_email',
        'reporter_phone',
        'pic_helpdesk_id',
        'pic_dev_ba_id',
        'rejection_notes',
        'total_hours',
        'sla_status',
        'sla_percentage',
        'response_due_date',
        'resolution_due_date',
    ];

    protected $beforeUpdate = ['prepareUpdateData', 'handleDynamicStatusDates'];

    /**
     * Callback sebelum update untuk menangani normalisasi status & transisi reopen secara dinamis
     */
    protected function handleDynamicStatusDates(array $data): array
    {
        if (empty($data['data']) || !is_array($data['data'])) {
            return $data;
        }

        // Cek apakah ada update status
        $rawStatus = $data['data']['status'] ?? null;
        if ($rawStatus === null) {
            return $data;
        }

        $newStatus = TicketStatus::normalize($rawStatus);

        // Cari data tiket saat ini jika primary key ada
        $id = $data['id'][0] ?? $data['id'] ?? null;
        $currentTicket = null;
        if ($id) {
            $currentTicket = $this->find($id);
        }

        $currentStatus = '';
        if ($currentTicket) {
            $currentStatus = TicketStatus::normalize(
                is_object($currentTicket)
                    ? ($currentTicket->getStatus() ?? $currentTicket->status ?? '')
                    : ($currentTicket['status'] ?? '')
            );
        }

        // Logika Reopened: jika tiket berstatus selesai/tuntas dan dibuka lagi, atau status menyatakan reopen
        if (TicketStatus::isReopenStatus($rawStatus) || (TicketStatus::isClosedOrResolved($currentStatus) && in_array($newStatus, [TicketStatus::OPEN, TicketStatus::REOPENED, TicketStatus::IN_PROGRESS], true))) {
            $newStatus = TicketStatus::REOPENED;
        }

        // Sinkronisasi field status
        $data['data']['status'] = $newStatus;

        return $data;
    }

    /**
     * Update status tiket secara dinamis beserta pencatatan timestamp SLA
     *
     * @param string $ticketId ID tiket yang akan diupdate
     * @param string $newStatus Status baru
     * @param array $extraData Data tambahan yang ingin diupdate
     * @return bool
     */
    public function updateStatus(string $ticketId, string $newStatus, array $extraData = []): bool
    {
        $extraData['status'] = $newStatus;
        return (bool) $this->update($ticketId, $extraData);
    }

    /**
     * Buka kembali tiket yang telah ditutup/selesai (Reopen Ticket)
     *
     * @param string $ticketId ID tiket
     * @param string|null $reason Alasan reopen
     * @return bool
     */
    public function reopenTicket(string $ticketId, ?string $reason = null): bool
    {
        $updateData = [
            'status' => TicketStatus::REOPENED,
        ];
        if (!empty($reason)) {
            $updateData['rejection_notes'] = $reason;
        }
        return $this->updateStatus($ticketId, TicketStatus::REOPENED, $updateData);
    }

    /**
     * Ambil tiket berdasarkan nomor tiket
     *
     * @param string $ticketNo
     * @return TicketEntity|null
     */
    public function getByTicketNo(string $ticketNo): ?TicketEntity
    {
        return $this->where('ticket_no', $ticketNo)->first();
    }

    /**
     * Hitung actual response time tiap tiket secara dinamis dari balasan pertama (ticket_response)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getActualResponseTime(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();
        $builder->select("
            ticket.id, 
            ticket.sla_id, 
            TIMESTAMPDIFF(MINUTE, ticket.created_date, MIN(tr.created_date)) as response_time
        ")
        ->join('ticket_response tr', 'tr.ticket_id = ticket.id', 'inner')
        ->groupBy('ticket.id, ticket.sla_id, ticket.created_date');

        if ($startDate && $endDate) {
            $builder->where('DATE(ticket.created_date) >=', $startDate)
                ->where('DATE(ticket.created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Hitung actual resolution time tiap tiket secara dinamis saat status pertama kali diselesaikan (ticket_response)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getActualResolutionTime(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();
        $builder->select("
            ticket.id, 
            ticket.sla_id, 
            TIMESTAMPDIFF(MINUTE, ticket.created_date, MIN(tr.created_date)) as resolution_time
        ")
        ->join('ticket_response tr', 'tr.ticket_id = ticket.id', 'inner')
        ->whereIn('LOWER(tr.status)', ['resolved', 'done', 'closed'])
        ->groupBy('ticket.id, ticket.sla_id, ticket.created_date');

        if ($startDate && $endDate) {
            $builder->where('DATE(ticket.created_date) >=', $startDate)
                ->where('DATE(ticket.created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Rata-rata response time & resolution time per priority/SLA secara dinamis dari event ticket_response
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAverageTimes(?string $startDate = null, ?string $endDate = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('ticket');
        $builder->select("
            COALESCE(s.priority, ticket.sla_status) as ticket_priority,
            AVG(TIMESTAMPDIFF(MINUTE, ticket.created_date, (
                SELECT MIN(tr1.created_date) 
                FROM ticket_response tr1 
                WHERE tr1.ticket_id = ticket.id
            ))) as avg_response,
            AVG(TIMESTAMPDIFF(MINUTE, ticket.created_date, (
                SELECT MIN(tr2.created_date) 
                FROM ticket_response tr2 
                WHERE tr2.ticket_id = ticket.id 
                  AND LOWER(tr2.status) IN ('resolved', 'done', 'closed')
            ))) as avg_resolution
        ")
        ->join('sla_configuration s', 's.id = ticket.sla_id', 'left')
        ->groupBy("COALESCE(s.priority, ticket.sla_status)");

        if ($startDate && $endDate) {
            $builder->where('DATE(ticket.created_date) >=', $startDate)
                ->where('DATE(ticket.created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }
}

// Alias agar kompatibel penuh dengan kode yang masih memanggil TicketTransactionModel
class_alias(TicketModel::class, TicketTransactionModel::class);
