<?php
namespace App\Controllers;
use App\Models\TicketModel;
use CodeIgniter\RESTful\ResourceController;

class TicketDetailReportController extends ResourceController
{
    protected $format = 'json';

    // Jumlah tiket per request type
    public function perRequestType()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('request_type_id as req_type, COUNT(*) as total')
                ->groupBy('request_type_id');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Jumlah tiket per priority
    public function perPriority()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('sla_status as ticket_priority, COUNT(*) as total')
                ->groupBy('sla_status');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Jumlah tiket per status
    public function perStatus()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('status as ticket_status, COUNT(*) as total')
                ->groupBy('status');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Jumlah tiket per tanggal (harian)
    public function perDate()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('DATE(created_date) as date, COUNT(*) as total')
                ->groupBy('DATE(created_date)');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Jumlah tiket per user
    public function perUser()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('created_by, COUNT(*) as total')
                ->groupBy('created_by');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();
        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // Detail tiket per periode waktu tertentu
    public function detailByDate()
    {
        $request = service('request');
        $model = new TicketModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');

        $builder = $model->builder();
        $builder->select('
            ticket.id,
            ticket.ticket_no,
            ticket.title as subject,
            ticket.description as message,
            ticket.status as ticket_status,
            ticket.sla_status as ticket_priority,
            ticket.reporter_email as email,
            ticket.reporter_phone as wa_no,
            ticket.created_by,
            ticket.created_date,
            ticket.resolution_due_date as due_date,
            (SELECT MIN(tr1.created_date) FROM ticket_response tr1 WHERE tr1.ticket_id = ticket.id) as first_response_at,
            (SELECT MIN(tr2.created_date) FROM ticket_response tr2 WHERE tr2.ticket_id = ticket.id AND LOWER(tr2.status) IN ("resolved", "done", "closed")) as finish_date
        ');
        if ($startDate && $endDate) {
            $builder->where('DATE(ticket.created_date) >=', $startDate);
            $builder->where('DATE(ticket.created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();

        foreach ($data as &$row) {
            $row['req_type'] = 'HC Helpdesk';
            $row['emp_name'] = $row['created_by'] ?? 'User';
            $row['nip'] = $row['created_by'] ?? '-';
        }

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    public function ticketDetail()
    {
        $request = service('request');
        $model = new \App\Models\TicketDetailReportModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $requestType = $request->getGet('request_type');
        $priority = $request->getGet('priority');
        $encrypter = \Config\Services::encrypter();

        $data = $model->getTicketDetail($startDate, $endDate, $requestType, $priority);

        foreach ($data as &$row) {
            $row['nip'] = '-';
            if (!empty($row['nip_encrypted'])) {
                try {
                    $row['nip'] = $encrypter->decrypt(hex2bin($row['nip_encrypted']));
                } catch (\Exception $e) {
                    $row['nip'] = '[Invalid]';
                }
            }
        }
        // Debug: cek isi $data
        log_message('debug', 'API Response: ' . json_encode($data));

            return $this->respond(['status' => 'success', 'data' => $data]);
        }

    // Tambahkan endpoint lain sesuai kebutuhan kombinasi
}
