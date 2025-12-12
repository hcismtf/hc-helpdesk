<?php
namespace App\Models;
use CodeIgniter\Model;

class TiketTrxModel extends Model
{
    protected $table = 'tiket_trx';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'emp_id', 'nip_encrypted', 'emp_name', 'email', 'wa_no', 'req_type', 'subject', 'message',
        'ticket_status', 'ticket_priority', 'created_by', 'created_date', 'modified_by', 'modified_date',
        'due_date', 'first_response_at', 'finish_date'
    ];
    // Hitung actual response time tiap tiket
    public function getActualResponseTime($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('id, ticket_priority, TIMESTAMPDIFF(MINUTE, created_date, first_response_at) as response_time')
                ->where('first_response_at IS NOT NULL');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        return $builder->get()->getResultArray();
    }

    // Hitung actual resolution time tiap tiket
    public function getActualResolutionTime($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('id, ticket_priority, TIMESTAMPDIFF(MINUTE, created_date, finish_date) as resolution_time')
                ->where('finish_date IS NOT NULL');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        return $builder->get()->getResultArray();
    }

    // Rata-rata response time & resolution time per priority
    public function getAverageTimes($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('ticket_priority, AVG(TIMESTAMPDIFF(MINUTE, created_date, first_response_at)) as avg_response, AVG(TIMESTAMPDIFF(MINUTE, created_date, finish_date)) as avg_resolution')
                ->groupBy('ticket_priority');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        return $builder->get()->getResultArray();
    }
}