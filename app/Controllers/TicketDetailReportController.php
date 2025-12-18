<?php
namespace App\Controllers;
use App\Models\TiketTrxModel;
use CodeIgniter\RESTful\ResourceController;

class TicketDetailReportController extends ResourceController
{
    protected $format = 'json';

    // Jumlah tiket per request type
    public function perRequestType()
    {
    $request = service('request');
    $model = new TiketTrxModel();
    $startDate = $request->getGet('start_date');
    $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('req_type, COUNT(*) as total')
                ->groupBy('req_type');
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
    $model = new TiketTrxModel();
    $startDate = $request->getGet('start_date');
    $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('ticket_priority, COUNT(*) as total')
                ->groupBy('ticket_priority');
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
    $model = new TiketTrxModel();
    $startDate = $request->getGet('start_date');
    $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('ticket_status, COUNT(*) as total')
                ->groupBy('ticket_status');
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
    $model = new TiketTrxModel();
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
    $model = new TiketTrxModel();
    $startDate = $request->getGet('start_date');
    $endDate = $request->getGet('end_date');
        $builder = $model->builder();
        $builder->select('created_by, emp_name, COUNT(*) as total')
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
        $model = new TiketTrxModel();
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $encrypter = \Config\Services::encrypter();

        $builder = $model->builder();
        $builder->select('id, req_type, ticket_priority, ticket_status, created_by, created_date, nip_encrypted, emp_name, email, wa_no, subject, message, due_date, first_response_at, finish_date');
        if ($startDate && $endDate) {
            $builder->where('DATE(created_date) >=', $startDate);
            $builder->where('DATE(created_date) <=', $endDate);
        }
        $data = $builder->get()->getResultArray();

        // Dekripsi NIP
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
