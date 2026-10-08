<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\SlaModel;
use App\Services\TicketMetricsService;

class DashboardController extends BaseController
{
    protected $metricsService;

    public function __construct()
    {
        $this->metricsService = new TicketMetricsService();
    }

    /**
     * Display the main admin dashboard with ticket metrics and trends
     */
    public function dashboard()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!\App\Services\AuthService::canAccessAdmin()) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Akun Anda tidak memiliki role administratif untuk mengakses halaman admin.');
        }

        $ticketModel = new TicketModel();
        $slaModel = new SlaModel();

        // Filters from GET
        $type = $this->request->getGet('type');
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page_tickets') ?? $this->request->getGet('page') ?? 1);

        if ($perPage <= 0) $perPage = 10;
        if ($page <= 0) $page = 1;

        // Query open/active tickets
        $builder = $ticketModel->whereNotIn('status', ['closed', 'done', 'resolved']);
        if ($type) $builder->where('request_type_id', $type);
        if ($start) $builder->where('created_date >=', $start . ' 00:00:00');
        if ($end) $builder->where('created_date <=', $end . ' 23:59:59');

        $openTicketsRaw = $builder->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);
        $openTickets = [];
        foreach ($openTicketsRaw as $ot) {
            $row = is_object($ot) ? $ot->toArray() : (array) $ot;
            $row['emp_name'] = $row['reporter_id'] ?? ($row['created_by'] ?? 'User');
            $row['req_type'] = 'HC Helpdesk';
            $row['due_date'] = $row['resolution_due_date'] ?? $row['response_due_date'] ?? null;
            $openTickets[] = $row;
        }

        // Status counts
        $openCount = (clone $ticketModel)->where('status', 'open')->countAllResults();
        $inProgressCount = (clone $ticketModel)->where('status', 'in_progress')->countAllResults();
        $doneCount = (clone $ticketModel)->whereIn('status', ['closed', 'done', 'resolved'])->countAllResults();
        $totalCount = (clone $ticketModel)->countAllResults();

        // SLA mapping
        $slaList = $slaModel->findAll();
        $slaMap = [];
        foreach ($slaList as $sla) {
            $slaMap[$sla['priority']] = $sla;
        }

        // Request types for dropdown
        $reqTypeModel = new \App\Models\RequestTypeModel();
        $types = $reqTypeModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        // Calculate performance metrics from closed tickets secara dinamis
        $closedTickets = (clone $ticketModel)->whereIn('status', ['closed', 'done', 'resolved'])->findAll();
        $closedTicketsArr = [];
        $db = \Config\Database::connect();

        foreach ($closedTickets as $ct) {
            $r = is_object($ct) ? $ct->toArray() : (array) $ct;
            $tId = $r['id'] ?? null;

            // First response dinamis
            $firstRep = $db->table('ticket_response')
                ->select('MIN(created_date) as first_res')
                ->where('ticket_id', $tId)
                ->get()
                ->getRowArray();

            // Resolution dinamis
            $lastResolved = $db->table('ticket_response')
                ->select('MIN(created_date) as resolved_at')
                ->where('ticket_id', $tId)
                ->whereIn('LOWER(status)', ['closed', 'done', 'resolved'])
                ->get()
                ->getRowArray();

            $r['first_response_at'] = $firstRep['first_res'] ?? null;
            $r['finish_date'] = $lastResolved['resolved_at'] ?? null;
            $r['due_date'] = $r['resolution_due_date'] ?? null;
            $closedTicketsArr[] = $r;
        }
        $metrics = $this->metricsService->calculatePerformanceMetrics($closedTicketsArr);

        // Pagination HTML
        $totalRecords = $builder->countAllResults(false);
        $totalPages = $perPage > 0 ? ceil($totalRecords / $perPage) : 1;
        $additionalParams = '&per_page=' . $perPage;
        if ($type) $additionalParams .= '&type=' . urlencode($type);
        if ($start) $additionalParams .= '&start=' . urlencode($start);
        if ($end) $additionalParams .= '&end=' . urlencode($end);

        $paginationHTML = $this->generatePaginationHTML($page, $totalPages, base_url('admin/dashboard'), $additionalParams);

        // 7-day trend metrics
        $trendData = $this->metricsService->get7DaysTrend($ticketModel);

        return view('admin/dashboard', [
            'openCount'        => $openCount,
            'inProgressCount'  => $inProgressCount,
            'doneCount'        => $doneCount,
            'totalCount'       => $totalCount,
            'openTickets'      => $openTickets,
            'paginationHTML'   => $paginationHTML,
            'types'            => $types,
            'perPage'          => $perPage,
            'page'             => $page,
            'type'             => $type,
            'start'            => $start,
            'end'              => $end,
            'username'         => session('username'),
            'role'             => session('role'),
            'avgResponseStr'   => $metrics['avgResponseStr'],
            'avgResolutionStr' => $metrics['avgResolutionStr'],
            'slaRate'          => $metrics['slaRate'],
            'trendDates'       => $trendData['trendDates'],
            'trendIncoming'    => $trendData['trendIncoming'],
            'trendResolved'    => $trendData['trendResolved'],
            'trendSla'         => $trendData['trendSla']
        ]);
    }
}
