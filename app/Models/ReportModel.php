<?php
namespace App\Models;
class ReportModel extends BaseModel
{
    protected $table = 'ticket';

    // Ambil detail tiket sesuai filter
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
        $builder->orderBy('created_date', 'DESC');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$r) {
            $r['req_type'] = 'HC Helpdesk';
            $r['emp_name'] = $r['created_by'] ?? 'User';
        }

        return $rows;
    }

    // Hitung jumlah tiket per request type
    public function countByRequestType($startDate = null, $endDate = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('request_type_id as req_type, COUNT(*) as total_tickets');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($priority) {
            $builder->where("sla_status", $priority);
        }
        $builder->groupBy('request_type_id');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per priority
    public function countByPriority($startDate = null, $endDate = null, $requestType = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('sla_status as ticket_priority, COUNT(*) as total_tickets');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($requestType) {
            $builder->where("request_type_id", $requestType);
        }
        $builder->groupBy('sla_status');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per status
    public function countByStatus($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('status as ticket_status, COUNT(*) as total_tickets');
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
        $builder->groupBy('status');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per tanggal (harian)
    public function countByDate($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('DATE(created_date) as tanggal, COUNT(*) as total_tickets');
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
        $builder->groupBy('tanggal');
        $builder->orderBy('tanggal', 'ASC');
        return $builder->get()->getResultArray();
    }
}
