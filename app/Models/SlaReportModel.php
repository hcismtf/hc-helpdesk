<?php
namespace App\Models;
use CodeIgniter\Model;

class SlaReportModel extends Model
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'priority', 'response_time', 'resolution_time', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];

    // Ambil SLA sesuai filter priority dan tanggal
    public function getSlaDetail($startDate = null, $endDate = null, $priority = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, priority, response_time, resolution_time, created_by, created_date, modified_by, modified_date');
        if ($startDate && $endDate) {
            $builder->where("DATE(created_date) >=", $startDate);
            $builder->where("DATE(created_date) <=", $endDate);
        }
        if ($priority) {
            $builder->where("priority", $priority);
        }
        $builder->orderBy('created_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    // Target vs average actual response time per priority
    public function getResponseTimeComparison($startDate = null, $endDate = null)
    {
        $sql = "SELECT s.priority, s.response_time AS target_response_time, AVG(TIMESTAMPDIFF(HOUR, t.created_date, t.first_response_at)) AS avg_actual_response_time FROM sla_configuration s LEFT JOIN tiket_trx t ON s.priority = t.ticket_priority WHERE t.first_response_at IS NOT NULL";
        if ($startDate && $endDate) {
            $sql .= " AND DATE(t.created_date) >= '" . $startDate . "' AND DATE(t.created_date) <= '" . $endDate . "'";
        }
        $sql .= " GROUP BY s.priority, s.response_time";
        return $this->db->query($sql)->getResultArray();
    }

    // Target vs average actual resolution time per priority
    public function getResolutionTimeComparison($startDate = null, $endDate = null)
    {
        $sql = "SELECT s.priority, s.resolution_time AS target_resolution_time, AVG(TIMESTAMPDIFF(HOUR, t.created_date, t.finish_date)) AS avg_actual_resolution_time FROM sla_configuration s LEFT JOIN tiket_trx t ON s.priority = t.ticket_priority WHERE t.finish_date IS NOT NULL";
        if ($startDate && $endDate) {
            $sql .= " AND DATE(t.created_date) >= '" . $startDate . "' AND DATE(t.created_date) <= '" . $endDate . "'";
        }
        $sql .= " GROUP BY s.priority, s.resolution_time";
        return $this->db->query($sql)->getResultArray();
    }
}
