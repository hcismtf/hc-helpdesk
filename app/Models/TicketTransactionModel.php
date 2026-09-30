<?php

namespace App\Models;

/**
 * Model utama transaksi tiket (Core Ticket Lifecycle & SLA)
 * Tabel: ticket_transactions
 */
class TicketTransactionModel extends BaseModel
{
    protected $table = 'ticket_transactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id', 'emp_id', 'nip_encrypted', 'emp_name', 'email', 'wa_no', 
        'req_type', 'subject', 'message', 'monitoring_url',
        'ticket_status', 'ticket_priority', 'created_by', 'created_date', 
        'due_date', 'first_response_at', 'finish_date', 'assigned_to', 
        'modified_by', 'modified_date'
    ];

    /**
     * Hitung actual response time tiap tiket
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getActualResponseTime(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();
        $builder->select('id, ticket_priority, TIMESTAMPDIFF(MINUTE, created_date, first_response_at) as response_time')
                ->where('first_response_at IS NOT NULL');

        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate)
                    ->where('DATE(created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Hitung actual resolution time tiap tiket
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getActualResolutionTime(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();
        $builder->select('id, ticket_priority, TIMESTAMPDIFF(MINUTE, created_date, finish_date) as resolution_time')
                ->where('finish_date IS NOT NULL');

        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate)
                    ->where('DATE(created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Rata-rata response time & resolution time per priority
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAverageTimes(?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();
        $builder->select('ticket_priority, AVG(TIMESTAMPDIFF(MINUTE, created_date, first_response_at)) as avg_response, AVG(TIMESTAMPDIFF(MINUTE, created_date, finish_date)) as avg_resolution')
                ->groupBy('ticket_priority');

        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate)
                    ->where('DATE(created_date) <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }
}
