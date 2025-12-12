<?php
namespace App\Controllers;
use App\Models\ReportModel;
use App\Models\SlaReportModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\TicketDetailReportModel; 

class ReportApiController extends ResourceController
{
    protected $reportModel;
    protected $slaReportModel;

    public function __construct()
    {
        $this->reportModel = new TicketDetailReportModel(); 
        $this->slaReportModel = new SlaReportModel();
    }

    // Endpoint: /report/ticket-detail
    public function ticketDetail()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $requestType = $this->request->getGet('request_type');
        $priority = $this->request->getGet('priority');
        $data = $this->reportModel->getTicketDetail($startDate, $endDate, $requestType, $priority);

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
        return $this->respond($formattedData);
    }

    // Endpoint: /report/sla-detail
    public function slaDetail()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $priority = $this->request->getGet('priority');
        $data = $this->slaReportModel->getSlaDetail($startDate, $endDate, $priority);
        return $this->respond($data);
    }

    // Endpoint: /report/sla-response-comparison
    public function slaResponseComparison()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $data = $this->slaReportModel->getResponseTimeComparison($startDate, $endDate);
        return $this->respond($data);
    }

    // Endpoint: /report/sla-resolution-comparison
    public function slaResolutionComparison()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $data = $this->slaReportModel->getResolutionTimeComparison($startDate, $endDate);
        return $this->respond($data);
    }
}
