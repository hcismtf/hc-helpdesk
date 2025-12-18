<?php
namespace App\Models;
use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'tiket_trx';

    // Ambil detail tiket sesuai filter
    public function getTicketDetail($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, emp_id, nip_encrypted, emp_name, email, wa_no, req_type, subject, message, ticket_status, ticket_priority, created_by, created_date, modified_by, modified_date, due_date, first_response_at, finish_date');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($requestType) {
            $builder->where("req_type", $requestType);
        }
        if ($priority) {
            $builder->where("ticket_priority", $priority);
        }
        $builder->orderBy('created_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per request type
    public function countByRequestType($startDate = null, $endDate = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('req_type, COUNT(*) as total_tickets');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($priority) {
            $builder->where("ticket_priority", $priority);
        }
        $builder->groupBy('req_type');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per priority
    public function countByPriority($startDate = null, $endDate = null, $requestType = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ticket_priority, COUNT(*) as total_tickets');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($requestType) {
            $builder->where("req_type", $requestType);
        }
        $builder->groupBy('ticket_priority');
        return $builder->get()->getResultArray();
    }

    // Hitung jumlah tiket per status
    public function countByStatus($startDate = null, $endDate = null, $requestType = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('ticket_status, COUNT(*) as total_tickets');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($requestType) {
            $builder->where("req_type", $requestType);
        }
        if ($priority) {
            $builder->where("ticket_priority", $priority);
        }
        $builder->groupBy('ticket_status');
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
            $builder->where("req_type", $requestType);
        }
        if ($priority) {
            $builder->where("ticket_priority", $priority);
        }
        $builder->groupBy('tanggal');
        $builder->orderBy('tanggal', 'ASC');
        return $builder->get()->getResultArray();
    }
}
