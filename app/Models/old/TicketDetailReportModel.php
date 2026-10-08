<?php
namespace App\Models;

use CodeIgniter\Model;

class TicketDetailReportModel extends BaseModel
{
    protected $table = 'ticket';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'ticket_no', 'reporter_id', 'pic_helpdesk_id', 'request_type_id', 'title', 'description',
        'status', 'sla_status',
        'resolution_due_date', 'response_date', 'completed_date'
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
            $builder->where("request_type_id", $requestType);
        }

        if ($priority) {
            $builder->where("sla_status", $priority);
        }

        return $builder;
    }

    // 1. Jumlah tiket per Request Type
    public function countByRequestType($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('request_type_id as req_type, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('request_type_id');
        return $builder->get()->getResultArray();
    }

    // 2. Jumlah tiket per Priority
    public function countByPriority($startDate = null, $endDate = null, $requestType = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('sla_status as ticket_priority, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, null);
        $builder->groupBy('sla_status');
        return $builder->get()->getResultArray();
    }

    // 3. Jumlah tiket per Status
    public function countByStatus($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('status as ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('status');
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
        $builder->select('created_by, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->groupBy('created_by');
        $builder->orderBy('total_tickets', 'DESC');
        return $builder->get()->getResultArray();
    }

    // 6. Kombinasi Request Type & Priority
    public function countByRequestTypeAndPriority($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('request_type_id as req_type, sla_status as ticket_priority, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['request_type_id', 'sla_status']);
        return $builder->get()->getResultArray();
    }

    // 7. Kombinasi Request Type & Status
    public function countByRequestTypeAndStatus($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('request_type_id as req_type, status as ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['request_type_id', 'status']);
        return $builder->get()->getResultArray();
    }

    // 8. Kombinasi Priority & Status
    public function countByPriorityAndStatus($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('sla_status as ticket_priority, status as ticket_status, COUNT(*) as total_tickets');
        $this->applyFilters($builder, $startDate, $endDate);
        $builder->groupBy(['sla_status', 'status']);
        return $builder->get()->getResultArray();
    }

    // 9. Detail tiket per periode waktu tertentu
    public function getTicketDetail($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            ticket.id,
            ticket.ticket_no as ticket_number,
            ticket.reporter_id,
            ticket.pic_helpdesk_id as assigned_to,
            ticket.request_type_id,
            ticket.title as subject,
            ticket.description as message,
            ticket.status as ticket_status,
            ticket.sla_status as ticket_priority,
            ticket.reporter_email as email,
            ticket.reporter_phone as wa_no,
            ticket.created_by,
            ticket.created_date,
            ticket.modified_by,
            ticket.modified_date,
            ticket.resolution_due_date as due_date,
            (SELECT MIN(tr1.created_date) FROM ticket_response tr1 WHERE tr1.ticket_id = ticket.id) as first_response_at,
            (SELECT MIN(tr2.created_date) FROM ticket_response tr2 WHERE tr2.ticket_id = ticket.id AND LOWER(tr2.status) IN ("resolved", "done", "closed")) as finish_date
        ');
        $this->applyFilters($builder, $startDate, $endDate, $requestType, $priority);
        $builder->orderBy('created_date', 'DESC');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$r) {
            $r['req_type'] = 'HC Helpdesk';
            $r['emp_name'] = $r['created_by'] ?? 'User';
        }

        return $rows;
    }
}
