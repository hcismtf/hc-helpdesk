<?php

namespace App\Services;

use App\Models\TicketModel;
use App\Models\SlaModel;
use App\Models\UserModel;
use App\Models\RequestTypeModel;
use Config\Database;

/**
 * AdminDashboardService
 *
 * Mengisolasi query, agregasi data, resolusi entitas, kalkulasi SLA & tren performa
 * untuk Admin Dashboard sesuai prinsip Clean Architecture & Anti-Fat-Controller (rule.md).
 */
class AdminDashboardService
{
    protected TicketModel $ticketModel;
    protected SlaModel $slaModel;
    protected UserModel $userModel;
    protected RequestTypeModel $reqTypeModel;
    protected TicketMetricsService $metricsService;

    public function __construct()
    {
        $this->ticketModel    = new TicketModel();
        $this->slaModel       = new SlaModel();
        $this->userModel      = new UserModel();
        $this->reqTypeModel   = new RequestTypeModel();
        $this->metricsService = new TicketMetricsService();
    }

    /**
     * Mengambil dan memproses seluruh data analitik untuk Admin Dashboard
     *
     * @param array $filters ['type' => string, 'start' => string, 'end' => string, 'per_page' => int, 'page' => int]
     * @return array
     */
    public function getDashboardData(array $filters): array
    {
        $type    = $filters['type'] ?? null;
        $start   = $filters['start'] ?? null;
        $end     = $filters['end'] ?? null;
        $perPage = max(1, (int) ($filters['per_page'] ?? 10));
        $page    = max(1, (int) ($filters['page'] ?? 1));

        // 1. Query Tiket Terbuka / Aktif dengan Pagination
        $builder = $this->ticketModel->whereNotIn('status', ['closed', 'done', 'resolved']);
        if ($type)  $builder->where('request_type_id', $type);
        if ($start) $builder->where('created_date >=', $start . ' 00:00:00');
        if ($end)   $builder->where('created_date <=', $end . ' 23:59:59');

        $openTicketsRaw = $builder->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);
        $totalRecords   = $builder->countAllResults(false);
        $totalPages     = $perPage > 0 ? (int) ceil($totalRecords / $perPage) : 1;

        // 2. Resolve Relasi Karyawan & Sub-kategori (Mencegah N+1 Query)
        $openTickets = $this->resolveTicketDetails($openTicketsRaw);

        // 3. Ringkasan Status Tiket
        $statusCounts = $this->getStatusCounts();

        // 4. Kalkulasi Metrik SLA (Response, Resolution, Compliance)
        $metrics = $this->calculateDynamicPerformanceMetrics();

        // 5. Tren 7 Hari Terakhir
        $trendData = $this->metricsService->get7DaysTrend($this->ticketModel);

        // 6. Master Kategori untuk Dropdown Filter
        $types = $this->reqTypeModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        return [
            'openCount'        => $statusCounts['open'],
            'inProgressCount'  => $statusCounts['in_progress'],
            'doneCount'        => $statusCounts['done'],
            'totalCount'       => $statusCounts['total'],
            'openTickets'      => $openTickets,
            'totalRecords'     => $totalRecords,
            'totalPages'       => $totalPages,
            'perPage'          => $perPage,
            'page'             => $page,
            'type'             => $type,
            'start'            => $start,
            'end'              => $end,
            'types'            => $types,
            'avgResponseStr'   => $metrics['avgResponseStr'],
            'avgResolutionStr' => $metrics['avgResolutionStr'],
            'slaRate'          => $metrics['slaRate'],
            'trendDates'       => $trendData['trendDates'],
            'trendIncoming'    => $trendData['trendIncoming'],
            'trendResolved'    => $trendData['trendResolved'],
            'trendSla'         => $trendData['trendSla'],
        ];
    }

    /**
     * Memetakan data tiket mentah ke entitas dengan Nama Pelapor & Kategori yang terbaca
     */
    protected function resolveTicketDetails(array $ticketsRaw): array
    {
        $allUsers = $this->userModel->findAll();
        $userMap = [];
        $nipMap = [];
        foreach ($allUsers as $u) {
            $uId = is_object($u) ? ($u->id ?? null) : ($u['id'] ?? null);
            $uName = is_object($u) ? ($u->name ?? '') : ($u['name'] ?? '');
            $uNip = is_object($u) ? ($u->employee_no ?? '') : ($u['employee_no'] ?? '');
            if ($uId) $userMap[$uId] = ['name' => $uName, 'nip' => $uNip];
            if ($uNip) $nipMap[$uNip] = ['name' => $uName, 'nip' => $uNip];
        }

        $allCategories = $this->slaModel->db->table('request_sub_category')->get()->getResultArray();
        $catMap = [];
        foreach ($allCategories as $cat) {
            $catMap[$cat['id']] = $cat['name'];
        }

        $resolvedTickets = [];
        foreach ($ticketsRaw as $ot) {
            $row = is_object($ot) ? $ot->toArray() : (array) $ot;

            $reporterId   = $row['reporter_id'] ?? null;
            $createdBy    = $row['created_by'] ?? null;
            $resolvedName = 'Karyawan';
            $resolvedNip  = $createdBy ?? '-';

            if (!empty($reporterId) && isset($userMap[$reporterId])) {
                $resolvedName = $userMap[$reporterId]['name'];
                $resolvedNip  = $userMap[$reporterId]['nip'] ?: $resolvedNip;
            } elseif (!empty($createdBy) && isset($nipMap[$createdBy])) {
                $resolvedName = $nipMap[$createdBy]['name'];
                $resolvedNip  = $createdBy;
            } elseif (!empty($createdBy)) {
                $resolvedName = $createdBy;
            }

            $reqTypeId = $row['request_type_id'] ?? null;
            $resolvedType = (!empty($reqTypeId) && isset($catMap[$reqTypeId])) ? $catMap[$reqTypeId] : 'HC Helpdesk';

            $row['emp_name'] = $resolvedName;
            $row['emp_nip']  = $resolvedNip;
            $row['req_type'] = $resolvedType;
            $row['due_date'] = $row['resolution_due_date'] ?? $row['response_due_date'] ?? null;

            $resolvedTickets[] = $row;
        }

        return $resolvedTickets;
    }

    /**
     * Menghitung total volume tiket berdasarkan status
     */
    protected function getStatusCounts(): array
    {
        return [
            'open'        => (clone $this->ticketModel)->where('status', 'open')->countAllResults(),
            'in_progress' => (clone $this->ticketModel)->where('status', 'in_progress')->countAllResults(),
            'done'        => (clone $this->ticketModel)->whereIn('status', ['closed', 'done', 'resolved'])->countAllResults(),
            'total'       => (clone $this->ticketModel)->countAllResults(),
        ];
    }

    /**
     * Menghitung rata-rata waktu respon, resolusi, dan kepatuhan SLA dari tiket yang telah selesai
     */
    protected function calculateDynamicPerformanceMetrics(): array
    {
        $closedTickets = (clone $this->ticketModel)->whereIn('status', ['closed', 'done', 'resolved'])->findAll();
        $closedTicketsArr = [];
        $db = Database::connect();

        foreach ($closedTickets as $ct) {
            $r = is_object($ct) ? $ct->toArray() : (array) $ct;
            $tId = $r['id'] ?? null;

            $firstRep = $db->table('ticket_response')
                ->select('MIN(created_date) as first_res')
                ->where('ticket_id', $tId)
                ->get()
                ->getRowArray();

            $lastResolved = $db->table('ticket_response')
                ->select('MIN(created_date) as resolved_at')
                ->where('ticket_id', $tId)
                ->whereIn('LOWER(status)', ['closed', 'done', 'resolved'])
                ->get()
                ->getRowArray();

            $r['first_response_at'] = $firstRep['first_res'] ?? null;
            $r['finish_date']       = $lastResolved['resolved_at'] ?? null;
            $r['due_date']          = $r['resolution_due_date'] ?? null;

            $closedTicketsArr[] = $r;
        }

        return $this->metricsService->calculatePerformanceMetrics($closedTicketsArr);
    }
}
