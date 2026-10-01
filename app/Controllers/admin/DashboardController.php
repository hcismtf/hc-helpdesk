<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\TicketTransactionModel;
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
            return redirect()->to('/admin/login');
        }

        $ticketModel = new TicketTransactionModel();
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
        $builder = $ticketModel->where('ticket_status !=', 'closed');
        if ($type) $builder->where('req_type', $type);
        if ($start) $builder->where('created_date >=', $start . ' 00:00:00');
        if ($end) $builder->where('created_date <=', $end . ' 23:59:59');

        $openTickets = $builder->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);

        // Status counts
        $openCount = (clone $ticketModel)->where('ticket_status', 'open')->countAllResults();
        $inProgressCount = (clone $ticketModel)->where('ticket_status', 'in_progress')->countAllResults();
        $doneCount = (clone $ticketModel)->where('ticket_status', 'closed')->countAllResults();
        $totalCount = (clone $ticketModel)->countAllResults();

        // SLA mapping
        $slaList = $slaModel->findAll();
        $slaMap = [];
        foreach ($slaList as $sla) {
            $slaMap[$sla['priority']] = $sla;
        }

        // Inject SLA & due dates
        $openTickets = $this->metricsService->injectSlaData($openTickets, $slaMap);

        // Request types for dropdown
        $types = $ticketModel->select('req_type')->distinct()->findAll();

        // Calculate performance metrics from closed tickets
        $closedTickets = (clone $ticketModel)->where('ticket_status', 'closed')->findAll();
        $metrics = $this->metricsService->calculatePerformanceMetrics($closedTickets);

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
