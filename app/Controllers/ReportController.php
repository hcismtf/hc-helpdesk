<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\TicketDetailReportModel;
use App\Models\SlaReportModel;

class ReportController extends BaseController
{
    protected $reportModel;
    protected $ticketDetailReportModel;
    protected $slaReportModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->ticketDetailReportModel = new TicketDetailReportModel();
        $this->slaReportModel = new SlaReportModel();
    }

    /**
     * Tampilkan halaman report dengan form filter
     */
    public function index()
    {
        $data = [
            'title' => 'Report',
        ];
        return view('report/index', $data);
    }

    /**
     * Generate Ticket Detail Report
     */
    public function ticketDetail()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $requestType = $this->request->getGet('request_type');
        $priority = $this->request->getGet('priority');

        $data = $this->ticketDetailReportModel->getTicketDetail($startDate, $endDate, $requestType, $priority);

        $encrypter = \Config\Services::encrypter();

        $formattedData = [];
        foreach ($data as $row) {
            $nip = '-';
            if (!empty($row['nip_encrypted'])) {
                try {
                    $nip = $encrypter->decrypt(hex2bin($row['nip_encrypted']));
                } catch (\Exception $e) {
                    $nip = '[Invalid]';
                }
            }
            
            $formattedData[] = [
                'id' => $row['id'],
                'emp_name' => $row['emp_name'],
                'email' => $row['email'],
                'wa_no' => $row['wa_no'],
                'req_type' => $row['req_type'],
                'subject' => $row['subject'],
                'message' => $row['message'],
                'ticket_status' => $row['ticket_status'],
                'ticket_priority' => $row['ticket_priority'],
                'created_by' => $row['created_by'],
                'created_date' => $row['created_date'],
                'modified_date' => $row['modified_date'],
                'due_date' => $row['due_date'],
                'first_response_at' => $row['first_response_at'],
                'finish_date' => $row['finish_date'],
                'nip' => $nip
            ];
        }

        return view('report/ticket_detail', ['data' => $formattedData]);
    }

    /**
     * Generate SLA Report
     */
    public function slaDetail()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $priority = $this->request->getGet('priority');

        $data = $this->slaReportModel->getSlaDetail($startDate, $endDate, $priority);

        return view('report/sla_detail', ['data' => $data]);
    }

    /**
     * Generate SLA Response Time Comparison Report
     */
    public function slaResponseComparison()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $data = $this->slaReportModel->getResponseTimeComparison($startDate, $endDate);

        return view('report/sla_response_comparison', ['data' => $data]);
    }

    /**
     * Generate SLA Resolution Time Comparison Report
     */
    public function slaResolutionComparison()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $data = $this->slaReportModel->getResolutionTimeComparison($startDate, $endDate);

        return view('report/sla_resolution_comparison', ['data' => $data]);
    }
}
