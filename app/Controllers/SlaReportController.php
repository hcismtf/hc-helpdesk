<?php
namespace App\Controllers;
use App\Models\TiketTrxModel;
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
        $model = new TiketTrxModel();
        $slaModel = new SlaConfigurationModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('ticket_priority, COUNT(*) as total')
                ->groupBy('ticket_priority');
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
        $model = new TiketTrxModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('id, TIMESTAMPDIFF(MINUTE, created_date, first_response_at) as response_time')
                ->where('first_response_at IS NOT NULL');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Hitung actual resolution time tiap tiket
    public function actualResolutionTime()
    {
        $request = service('request');
        $model = new TiketTrxModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('id, TIMESTAMPDIFF(MINUTE, created_date, finish_date) as resolution_time')
                ->where('finish_date IS NOT NULL');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Persentase tiket sesuai SLA vs tidak sesuai SLA (response time)
    public function compliancePercentage()
    {
        $request = service('request');
        $model = new TiketTrxModel();
        $slaModel = new SlaConfigurationModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $sla = $slaModel->findAll();
        $builder = $model->builder();
        $builder->select('id, ticket_priority, TIMESTAMPDIFF(MINUTE, created_date, first_response_at) as response_time')
                ->where('first_response_at IS NOT NULL');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $tickets = $builder->get()->getResultArray();
        $compliant = 0;
        $nonCompliant = 0;
        $slaMap = [];
        foreach ($sla as $row) {
            $slaMap[$row['priority']] = $row['response_time'];
        }
        foreach ($tickets as $ticket) {
            $priority = $ticket['ticket_priority'];
            $target = isset($slaMap[$priority]) ? $slaMap[$priority] : null;
            if ($target !== null && $ticket['response_time'] <= $target) {
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
        $model = new TiketTrxModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('ticket_priority, AVG(TIMESTAMPDIFF(MINUTE, created_date, first_response_at)) as avg_response, AVG(TIMESTAMPDIFF(MINUTE, created_date, finish_date)) as avg_resolution')
                ->groupBy('ticket_priority');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Tambahkan endpoint lain sesuai kebutuhan breakdown/trend
}
