<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportExportService
{
    /**
     * Get API Key from file or .env
     *
     * @return string
     */
    public function getApiKey(): string
    {
        $keyFilePath = getenv('report.keyPath') ?: 'D:/helpdeskkey/key';

        if (file_exists($keyFilePath)) {
            $key = trim(file_get_contents($keyFilePath));
            if (!empty($key)) {
                return $key;
            }
        }

        return '';
    }

    /**
     * Apply orange header styling to Excel sheet
     *
     * @param object $sheet
     * @param int $columnCount
     * @return void
     */
    public function applyHeaderStyling($sheet, int $columnCount): void
    {
        $headerRange = 'A1:' . chr(64 + $columnCount) . '1';

        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF9933']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);
    }

    /**
     * Generate Ticket Detail Excel file
     *
     * @param array $filterParams
     * @param string $filePath
     * @return void
     */
    public function exportTicketDetail(array $filterParams, string $filePath): void
    {
        $client = \Config\Services::curlrequest();
        $reportBaseURL = getenv('report.baseURL') ?: 'http://localhost/Report-HC_Helpdesk/public/';
        $apiKey = $this->getApiKey();

        $response = $client->get($reportBaseURL . 'report/ticket-detail', [
            'headers' => [
                'X-API-KEY' => $apiKey
            ],
            'query' => [
                'start_date'   => $filterParams['start_date_ticket'] ?? '',
                'end_date'     => $filterParams['end_date_ticket'] ?? '',
                'request_type' => $filterParams['request_type'] ?? '',
                'priority'     => $filterParams['priority_ticket'] ?? ''
            ]
        ]);

        $data = json_decode($response->getBody(), true) ?? [];
        $headers = [
            'ID', 'Nama', 'Email', 'WA', 'Request Type', 'Subject', 'Status', 'Prioritas',
            'Dibuat Oleh', 'Tanggal Dibuat', 'Tanggal Diubah', 'Due Date', 'First Response', 'Finish Date'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');

        $this->applyHeaderStyling($sheet, count($headers));

        $rowNum = 2;
        foreach ($data as $row) {
            $excelRow = [
                $row['id'] ?? '',
                $row['emp_name'] ?? '',
                $row['email'] ?? '',
                $row['wa_no'] ?? '',
                $row['req_type'] ?? '',
                $row['subject'] ?? '',
                $row['ticket_status'] ?? '',
                $row['ticket_priority'] ?? '',
                $row['created_by'] ?? '',
                $row['created_date'] ?? '',
                $row['modified_date'] ?? '',
                $row['due_date'] ?? '',
                $row['first_response_at'] ?? '',
                $row['finish_date'] ?? ''
            ];
            $sheet->fromArray($excelRow, null, 'A' . $rowNum);
            $rowNum++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    /**
     * Generate SLA Report Excel file
     *
     * @param array $filterParams
     * @param string $filePath
     * @return void
     */
    public function exportSlaReport(array $filterParams, string $filePath): void
    {
        $client = \Config\Services::curlrequest();
        $reportBaseURL = getenv('report.baseURL') ?: 'http://localhost/Report-HC_Helpdesk/public/';
        $apiKey = $this->getApiKey();

        // 1. SLA details
        $slaDetailRes = $client->get($reportBaseURL . 'report/sla-detail', [
            'headers' => ['X-API-KEY' => $apiKey],
            'query' => [
                'start_date' => $filterParams['start_date_sla'] ?? '',
                'end_date'   => $filterParams['end_date_sla'] ?? '',
                'priority'   => $filterParams['priority_sla'] ?? ''
            ]
        ]);
        $slaDetails = json_decode($slaDetailRes->getBody(), true) ?? [];

        // 2. SLA response comparison
        $responseCompRes = $client->get($reportBaseURL . 'report/sla-response-comparison', [
            'headers' => ['X-API-KEY' => $apiKey],
            'query' => [
                'start_date' => $filterParams['start_date_sla'] ?? '',
                'end_date'   => $filterParams['end_date_sla'] ?? ''
            ]
        ]);
        $responseComp = json_decode($responseCompRes->getBody(), true) ?? [];

        // 3. SLA resolution comparison
        $resolutionCompRes = $client->get($reportBaseURL . 'report/sla-resolution-comparison', [
            'headers' => ['X-API-KEY' => $apiKey],
            'query' => [
                'start_date' => $filterParams['start_date_sla'] ?? '',
                'end_date'   => $filterParams['end_date_sla'] ?? ''
            ]
        ]);
        $resolutionComp = json_decode($resolutionCompRes->getBody(), true) ?? [];

        // Build SLA map
        $slaMap = [];
        foreach ($slaDetails as $sla) {
            $priority = strtolower($sla['priority'] ?? '');
            $slaMap[$priority] = [
                'Priority' => ucfirst($priority),
                'Target Response Time (jam)' => $sla['response_time'] ?? '',
                'Actual Response Time (jam)' => '-',
                'Target Resolution Time (jam)' => $sla['resolution_time'] ?? '',
                'Actual Resolution Time (jam)' => '-',
                'Created By' => $sla['created_by'] ?? '',
                'Created Date' => $sla['created_date'] ?? '',
                'Modified By' => $sla['modified_by'] ?? '',
                'Modified Date' => $sla['modified_date'] ?? ''
            ];
        }

        foreach ($responseComp as $rc) {
            $priority = strtolower($rc['priority'] ?? '');
            if (isset($slaMap[$priority])) {
                $slaMap[$priority]['Actual Response Time (jam)'] = isset($rc['avg_actual_response_time']) ? round((float)$rc['avg_actual_response_time'], 2) : '-';
            }
        }

        foreach ($resolutionComp as $rs) {
            $priority = strtolower($rs['priority'] ?? '');
            if (isset($slaMap[$priority])) {
                $slaMap[$priority]['Actual Resolution Time (jam)'] = isset($rs['avg_actual_resolution_time']) ? round((float)$rs['avg_actual_resolution_time'], 2) : '-';
            }
        }

        $priorityFilter = strtolower($filterParams['priority_sla'] ?? '');
        $excelRows = [];
        foreach ($slaMap as $priority => $row) {
            if ($priorityFilter && $priority != $priorityFilter) {
                continue;
            }
            $excelRows[] = $row;
        }

        $headers = [
            'Priority',
            'Target Response Time (jam)',
            'Actual Response Time (jam)',
            'Target Resolution Time (jam)',
            'Actual Resolution Time (jam)',
            'Created By',
            'Created Date',
            'Modified By',
            'Modified Date'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');

        $this->applyHeaderStyling($sheet, count($headers));

        $rowNum = 2;
        foreach ($excelRows as $row) {
            $sheet->fromArray(array_values($row), null, 'A' . $rowNum);
            $rowNum++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}
