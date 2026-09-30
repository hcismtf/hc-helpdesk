<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\ReportJobModel;
use App\Services\ReportExportService;

class ReportUserController extends BaseController
{
    protected $reportExportService;

    public function __construct()
    {
        $this->reportExportService = new ReportExportService();
    }

    /**
     * Report generation and job history view
     */
    public function report_user()
    {
        $username = session('username') ?? '';
        $role = session('role') ?? '';
        $userId = session('user_id');

        $reportType = $this->request->getGet('report_type') ?? 'Report Ticket Detail';
        $requestType = $this->request->getGet('request_type') ?? '';
        $priority_ticket = $this->request->getGet('priority_ticket') ?? '';
        $priority_sla = $this->request->getGet('priority_sla') ?? '';
        $start_date_ticket = $this->request->getGet('start_date_ticket') ?? '';
        $end_date_ticket = $this->request->getGet('end_date_ticket') ?? '';
        $start_date_sla = $this->request->getGet('start_date_sla') ?? '';
        $end_date_sla = $this->request->getGet('end_date_sla') ?? '';

        $ticketModel = new TicketModel();
        $requestTypes = $ticketModel->select('req_type')->distinct()->where('req_type IS NOT NULL')->where('req_type !=', '')->findAll();
        $priorities = $ticketModel->select('ticket_priority')->distinct()->where('ticket_priority IS NOT NULL')->where('ticket_priority !=', '')->findAll();

        $jobModel = new ReportJobModel();
        $reportJobs = $jobModel->where('created_by', $userId)->orderBy('created_at', 'desc')->findAll(10);

        return view('admin/report_user', [
            'username'          => $username,
            'role'              => $role,
            'reportType'        => $reportType,
            'requestTypes'      => $requestTypes,
            'priorities'        => $priorities,
            'requestType'       => $requestType,
            'priority_ticket'   => $priority_ticket,
            'priority_sla'      => $priority_sla,
            'start_date_ticket' => $start_date_ticket,
            'end_date_ticket'   => $end_date_ticket,
            'start_date_sla'    => $start_date_sla,
            'end_date_sla'      => $end_date_sla,
            'reportJobs'        => $reportJobs,
            'active'            => 'reports'
        ]);
    }

    /**
     * Submit export report job and generate Excel file
     */
    public function submit_report_job()
    {
        $userId = session('user_id');
        $reportType = $this->request->getPost('report_type');
        $filterParams = $this->request->getPost();

        $fileName = 'report_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $reportType) . '_' . date('Ymd_His') . '_' . uniqid() . '.xlsx';
        $filePath = 'D:/uploads/' . $fileName;

        if ($reportType === 'Report Ticket Detail') {
            $this->reportExportService->exportTicketDetail($filterParams, $filePath);
        } elseif ($reportType === 'Report SLA') {
            $this->reportExportService->exportSlaReport($filterParams, $filePath);
        }

        // Encrypt filter parameters and file path for DB storage
        $encrypter = \Config\Services::encrypter();
        $filterParamsEnc = bin2hex($encrypter->encrypt(json_encode($filterParams)));
        $filePathEnc = bin2hex($encrypter->encrypt($filePath));

        $jobModel = new ReportJobModel();
        $jobId = $jobModel->insert([
            'report_type'   => $reportType,
            'filter_params' => $filterParamsEnc,
            'file_path'     => $filePathEnc,
            'status'        => 'done',
            'action'        => 'export',
            'created_by'    => $userId
        ]);

        return $this->response->setJSON(['success' => true, 'job_id' => $jobId]);
    }

    /**
     * Download generated report file
     *
     * @param int $id
     */
    public function download_report($id)
    {
        $jobModel = new ReportJobModel();
        $job = $jobModel->find($id);

        if (!$job || $job['status'] !== 'done' || empty($job['file_path'])) {
            return 'File belum tersedia';
        }

        $encrypter = \Config\Services::encrypter();
        try {
            $filePath = $encrypter->decrypt(hex2bin($job['file_path']));
        } catch (\Exception $e) {
            return 'File path tidak valid';
        }

        $filePath = realpath($filePath);
        if (!$filePath || !is_file($filePath)) {
            return 'File tidak ditemukan';
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Delete report job and associated physical file
     *
     * @param int $id
     */
    public function delete_report_job($id)
    {
        $jobModel = new ReportJobModel();
        $job = $jobModel->find($id);

        if ($job) {
            if (!empty($job['file_path'])) {
                $encrypter = \Config\Services::encrypter();
                try {
                    $filePath = $encrypter->decrypt(hex2bin($job['file_path']));
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                } catch (\Exception $e) {
                    // Skip if invalid
                }
            }
            $jobModel->delete($id);
        }

        return redirect()->to(base_url('admin/report_user'));
    }
}
