<?php
namespace App\Models;

use CodeIgniter\Model;

class TicketDetailReportModel extends Model
{
    protected $table = 'tiket_trx';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'emp_id', 'nip_encrypted', 'emp_name', 'email', 'wa_no', 'req_type', 'subject', 'message',
        'ticket_status', 'ticket_priority', 'created_by', 'created_date', 'modified_by', 'modified_date',
        'due_date', 'first_response_at', 'finish_date'
    ];

    /**
     * Base filter untuk query (request_type, priority, date range)
     */
    private function applyFilters($builder, $startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }

        if ($requestType) {
            $builder->where("req_type", $requestType); // GANTI request_type jadi req_type
        }

        if ($priority) {
            $builder->where("ticket_priority", $priority); // GANTI priority jadi ticket_priority
        }

        return $builder;
    }

    // 1. Jumlah tiket per Request Type
    public function countByRequestType($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('req_type, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('req_type');
        return $builder->get()->getResultArray();
    }

    // 2. Jumlah tiket per Priority
    public function countByPriority($startDate = null, $endDate = null, $requestType = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ticket_priority, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, null);
        $builder->groupBy('ticket_priority');
        return $builder->get()->getResultArray();
    }

    // 3. Jumlah tiket per Status
    public function countByStatus($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('ticket_status');
        return $builder->get()->getResultArray();
    }

    // 4. Jumlah tiket per Tanggal (harian)
    public function countByDate($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('DATE(created_date) as tanggal, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('DATE(created_date)');
        $builder->orderBy('tanggal', 'ASC');
        return $builder->get()->getResultArray();
    }

    // 5. Jumlah tiket per User
    public function countByUser($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('created_by, emp_name, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('created_by');
        $builder->orderBy('total_tickets', 'DESC');
        return $builder->get()->getResultArray();
    }

    // 6. Kombinasi Request Type & Priority
    public function countByRequestTypeAndPriority($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('req_type, ticket_priority, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['req_type', 'ticket_priority']);
        return $builder->get()->getResultArray();
    }

    // 7. Kombinasi Request Type & Status
    public function countByRequestTypeAndStatus($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('req_type, ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['req_type', 'ticket_status']);
        return $builder->get()->getResultArray();
    }

    // 8. Kombinasi Priority & Status
    public function countByPriorityAndStatus($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ticket_priority, ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['ticket_priority', 'ticket_status']);
        return $builder->get()->getResultArray();
    }

    // 9. Detail tiket per periode waktu tertentu
    public function getTicketDetail($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, emp_id, nip_encrypted, emp_name, email, wa_no, req_type, subject, message, ticket_status, ticket_priority, created_by, created_date, modified_by, modified_date, due_date, first_response_at, finish_date');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->orderBy('created_date', 'DESC');
        return $builder->get()->getResultArray();
    }
}
