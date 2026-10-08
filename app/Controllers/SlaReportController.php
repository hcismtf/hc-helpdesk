<?php
namespace App\Controllers;
use App\Models\TicketModel;
use App\Models\SlaConfigurationModel;
use CodeIgniter\RESTful\ResourceController;

class SlaReportController extends ResourceController
{
    protected $format = 'json';

    // SLA konfigurasi per priority
    public function slaConfig()
    {
        $slaModel = new SlaConfigurationModel();
        $data = $slaModel->findAll();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Jumlah tiket per priority vs SLA target response_time
    public function priorityVsResponse()
    {
        $request = service('request');
        $model = new TicketModel();
        $slaModel = new SlaConfigurationModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('sla_status as ticket_priority, COUNT(*) as total')
                ->groupBy('sla_status');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $tickets = $builder->get()->getResultArray();
        $sla = $slaModel->findAll();
        return $this->respond(['status' => 'success', 'tickets' => $tickets, 'sla' => $sla]);
    }

    // Hitung actual response time tiap tiket
    public function actualResponseTime()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $data = $model->getActualResponseTime($startDate, $endDate);
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Hitung actual resolution time tiap tiket
    public function actualResolutionTime()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $data = $model->getActualResolutionTime($startDate, $endDate);
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Persentase tiket sesuai SLA vs tidak sesuai SLA (response time)
    public function compliancePercentage()
    {
        $request = service('request');
        $model = new TicketModel();
        $slaModel = new SlaConfigurationModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $sla = $slaModel->findAll();

        $tickets = $model->getActualResponseTime($startDate, $endDate);
        $compliant = 0;
        $nonCompliant = 0;
        $slaMap = [];
        foreach ($sla as $row) {
            $slaMap[$row['priority']] = $row['response_time'];
            $slaMap[$row['id']] = $row['response_time'];
        }
        foreach ($tickets as $ticket) {
            $key = $ticket['sla_id'] ?? null;
            $target = isset($slaMap[$key]) ? $slaMap[$key] : null;
            if ($target !== null && ($ticket['response_time'] ?? 999999) <= $target) {
                $compliant++;
            } else {
                $nonCompliant++;
            }
        }
        return $this->respond([
            'status' => 'success',
            'compliant' => $compliant,
            'non_compliant' => $nonCompliant
        ]);
    }

    // Rata-rata response time & resolution time per priority
    public function averageTimes()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $data = $model->getAverageTimes($startDate, $endDate);
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Tambahkan endpoint lain sesuai kebutuhan breakdown/trend
}
