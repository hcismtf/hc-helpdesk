<?php
namespace App\Models;

class SlaReportModel extends BaseModel
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'priority', 'response_time', 'resolution_time', 'created_by', 'created_date', 'modified_by', 'modified_date'
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
        $builder = $this->db->table($this->table);
        $builder->select('priority, response_time');
        $targets = $builder->get()->getResultArray();

        $trxModel = new TicketTransactionModel();
        $actuals = $trxModel->getAverageTimes($startDate, $endDate);

        $result = [];
        foreach ($targets as $t) {
            $priority = $t['priority'];
            $actual = 0;
            foreach ($actuals as $a) {
                if ($a['ticket_priority'] === $priority) {
                    $actual = $a['avg_response'];
                    break;
                }
            }
            $result[] = [
                'priority' => $priority,
                'target' => $t['response_time'],
                'actual' => $actual
            ];
        }
        return $result;
    }

    // Target vs average actual resolution time per priority
    public function getResolutionTimeComparison($startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('priority, resolution_time');
        $targets = $builder->get()->getResultArray();

        $trxModel = new TicketTransactionModel();
        $actuals = $trxModel->getAverageTimes($startDate, $endDate);

        $result = [];
        foreach ($targets as $t) {
            $priority = $t['priority'];
            $actual = 0;
            foreach ($actuals as $a) {
                if ($a['ticket_priority'] === $priority) {
                    $actual = $a['avg_resolution'];
                    break;
                }
            }
            $result[] = [
                'priority' => $priority,
                'target' => $t['resolution_time'],
                'actual' => $actual
            ];
        }
        return $result;
    }
}
