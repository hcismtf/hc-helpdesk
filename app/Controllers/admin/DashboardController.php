<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Services\AdminDashboardService;

/**
 * DashboardController
 *
 * Mengikuti arsitektur Clean Controller & Anti-Monolith (rule.md).
 * Bertugas memvalidasi sesi/izin akses HTTP, mendelegasikan pemrosesan ke AdminDashboardService,
 * dan mengembalikan HTTP View response.
 */
class DashboardController extends BaseController
{
    protected AdminDashboardService $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new AdminDashboardService();
    }

    /**
     * Display the main admin dashboard with ticket metrics and trends
     */
    public function dashboard()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!AuthService::canAccessAdmin()) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Akun Anda tidak memiliki role administratif untuk mengakses halaman admin.');
        }

        // Ambil filter query parameter dari request HTTP
        $filters = [
            'type'     => $this->request->getGet('type'),
            'start'    => $this->request->getGet('start'),
            'end'      => $this->request->getGet('end'),
            'per_page' => (int) ($this->request->getGet('per_page') ?? 10),
            'page'     => (int) ($this->request->getGet('page_tickets') ?? $this->request->getGet('page') ?? 1),
        ];

        // Delegasikan pemrosesan bisnis ke Service
        $data = $this->dashboardService->getDashboardData($filters);

        // Siapkan pagination HTML
        $additionalParams = '&per_page=' . $data['perPage'];
        if ($data['type'])  $additionalParams .= '&type=' . urlencode($data['type']);
        if ($data['start']) $additionalParams .= '&start=' . urlencode($data['start']);
        if ($data['end'])   $additionalParams .= '&end=' . urlencode($data['end']);

        $data['paginationHTML'] = $this->generatePaginationHTML(
            $data['page'],
            $data['totalPages'],
            base_url('admin/dashboard'),
            $additionalParams
        );

        // Lengkapi data sesi pengguna
        $data['username'] = session('username');
        $data['role']     = session('role');

        return view('admin/dashboard', $data);
    }
}
