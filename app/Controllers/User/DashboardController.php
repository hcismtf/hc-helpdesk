<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\UserModel;
use Config\Database;

/**
 * DashboardController
 * 
 * Clean Architecture Controller for Employee/User Dashboard.
 * Enforces strict user isolation (no unauthenticated access, only owner tickets).
 */
class DashboardController extends BaseController
{
    protected TicketModel $ticketModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->userModel   = new UserModel();
    }

    /**
     * Menampilkan halaman utama Dashboard User (/dashboard)
     */
    public function index()
    {
        // 1. Verifikasi Autentikasi Pengguna (Keamanan Multi-Tenant)
        $isLoggedIn    = (bool) (session('isLoggedIn') || session('logged_in'));
        $currentUserId = session('user_id');
        $currentEmpNo  = session('employee_no') ?? session('username') ?? '';

        // JIKA BELUM LOGIN: Dilarang keras menampilkan tiket orang lain!
        if (!$isLoggedIn || (empty($currentUserId) && empty($currentEmpNo))) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk mengakses tiket Anda.');
        }

        // 2. Ambil data profil spesifik milik user yang sedang login
        $dbUser = null;
        if (!empty($currentUserId)) {
            $dbUser = $this->userModel->getUserWithRole($currentUserId);
        }
        if (!$dbUser && !empty($currentEmpNo)) {
            $found = $this->userModel->where('employee_no', $currentEmpNo)->first();
            if ($found) {
                $dbUser = $this->userModel->getUserWithRole($found['id'] ?? $found->id);
            }
        }

        if (!$dbUser) {
            session()->destroy();
            return redirect()->to(base_url('login'))->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        $division   = $dbUser['division'] ?? $dbUser['position'] ?? $dbUser['job_title'] ?? session('division') ?? 'Human Capital Services';
        $userName   = $dbUser['name'] ?? session('name') ?? 'Karyawan MTF';
        $employeeNo = $dbUser['employee_no'] ?? session('employee_no') ?? $currentEmpNo;

        // Data lengkap user yang disimpan ke localStorage (Terenkripsi)
        $userRefData = [
            'id'             => $dbUser['id'] ?? $currentUserId,
            'employee_no'    => $employeeNo,
            'name'           => $userName,
            'email'          => $dbUser['email'] ?? session('email') ?? '',
            'position'       => $dbUser['position'] ?? '',
            'position_level' => $dbUser['position_level'] ?? '',
            'job_title'      => $dbUser['job_title'] ?? '',
            'division'       => $division,
            'status'         => $dbUser['status'] ?? 'ACTIVE',
            'role_name'      => $dbUser['role_name'] ?? session('role_name') ?? 'USER',
        ];

        $currentDate = $this->formatIndonesianDate();

        // 3. Query Tiket Asli HANYA milik pengguna yang sedang login
        $db = Database::connect();
        $builder = $db->table('ticket')
            ->select('
                ticket.*,
                request_sub_category.name AS request_type_name,
                sla.priority AS sla_priority,
                sla.response_time AS sla_response_hours,
                sla.resolution_time AS sla_resolution_hours,
                pic_user.name AS pic_name,
                pic_user.position AS pic_role,
                pic_user.job_title AS pic_job_title
            ')
            ->join('request_sub_category', 'request_sub_category.id = ticket.request_type_id', 'left')
            ->join('sla', 'sla.id = ticket.sla_id', 'left')
            ->join('users AS pic_user', 'pic_user.id = ticket.pic_helpdesk_id OR pic_user.id = ticket.pic_dev_ba_id', 'left');

        // Filter ketat kepemilikan tiket
        $builder->groupStart();
        if (!empty($dbUser['id'])) {
            $builder->where('ticket.reporter_id', $dbUser['id']);
        }
        if (!empty($employeeNo)) {
            $builder->orWhere('ticket.created_by', $employeeNo);
        }
        if (!empty($dbUser['email'])) {
            $builder->orWhere('ticket.reporter_email', $dbUser['email']);
        }
        $builder->groupEnd();

        $builder->orderBy('ticket.created_date', 'DESC');
        $rawTickets = $builder->get()->getResultArray();

        // 4. Transformasi Data Tiket & Perhitungan Metrik Realtime
        $tickets = [];
        $counts = [
            'open'          => 0,
            'in_progress'   => 0,
            'needs_action'  => 0,
            'resolved'      => 0,
        ];

        foreach ($rawTickets as $row) {
            $rawStatus = strtolower(trim($row['status'] ?? 'open'));
            $status = 'open';
            $statusLabel = 'Tiket Terbuka';
            $actionLabel = 'Lihat Detail';
            $actionTone  = 'default';

            if (in_array($rawStatus, ['needs_action', 'pending', 'waiting_user', 'waiting_employee'], true)) {
                $status      = 'needs_action';
                $statusLabel = 'Butuh Respon Anda';
                $actionLabel = 'Beri Respon →';
                $actionTone  = 'purple';
                $counts['needs_action']++;
            } elseif (in_array($rawStatus, ['in_progress', 'assigned', 'investigating', 'processing'], true)) {
                $status      = 'in_progress';
                $statusLabel = 'Sedang Diproses';
                $counts['in_progress']++;
            } elseif (in_array($rawStatus, ['resolved', 'closed', 'selesai'], true)) {
                $status      = 'resolved';
                $statusLabel = 'Selesai';
                $counts['resolved']++;
            } else {
                $counts['open']++;
            }

            // Priority
            $prio = strtolower(trim($row['sla_priority'] ?? 'medium'));
            $priorityLabel = 'Medium';
            $priorityTone  = 'warning';

            if ($prio === 'high' || $prio === 'urgent') {
                $priorityLabel = 'Urgent';
                $priorityTone  = 'danger';
            } elseif ($prio === 'low' || $prio === 'normal') {
                $priorityLabel = 'Normal';
                $priorityTone  = 'info';
            }

            // SLA Time Calculation
            $slaBadge = 'Tepat Waktu';
            $slaTone  = 'success';

            if (!empty($row['resolution_due_date'])) {
                $nowTs = time();
                $dueTs = strtotime($row['resolution_due_date']);
                $diff  = $dueTs - $nowTs;

                if ($status === 'resolved') {
                    $slaBadge = 'Tepat Waktu';
                    $slaTone  = 'success';
                } elseif ($diff > 0) {
                    $hours = (int) floor($diff / 3600);
                    $mins  = (int) floor(($diff % 3600) / 60);

                    if ($hours < 1) {
                        $slaBadge = "Sisa SLA: {$mins} Menit";
                        $slaTone  = 'danger';
                    } elseif ($hours < 24) {
                        $slaBadge = "Sisa SLA: {$hours} Jam {$mins} Menit";
                        $slaTone  = 'warning';
                    } else {
                        $days = (int) ceil($hours / 24);
                        $slaBadge = "Sisa SLA: {$days} Hari Kerja";
                        $slaTone  = 'info';
                    }
                } else {
                    $slaBadge = 'Terlewat SLA';
                    $slaTone  = 'danger';
                }
            }

            // PIC Info
            $picName     = !empty($row['pic_name']) ? $row['pic_name'] : 'Menunggu penugasan tim HC';
            $picRole     = !empty($row['pic_role']) ? $row['pic_role'] : (!empty($row['pic_job_title']) ? $row['pic_job_title'] : 'HC Specialist');
            $picInitials = null;

            if (!empty($row['pic_name'])) {
                $words = explode(' ', trim($row['pic_name']));
                $picInitials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            }

            // Tanggal Pembuatan
            $createdTs = !empty($row['created_date']) ? strtotime($row['created_date']) : time();
            $createdFormatted = $this->formatTicketDate($createdTs);

            $tickets[] = [
                'id'            => $row['id'] ?? $row['ticket_no'],
                'ticket_no'     => '#' . ($row['ticket_no'] ?? 'HC-2026-00000'),
                'category'      => $row['request_type_name'] ?? 'Layanan HC',
                'priority'      => $priorityLabel,
                'priority_tone' => $priorityTone,
                'sla_badge'     => $slaBadge,
                'sla_tone'      => $slaTone,
                'title'         => $row['title'] ?? 'Tiket Tanpa Judul',
                'description'   => $row['description'] ?? 'Tidak ada rincian keterangan tambahan.',
                'created_at'    => $createdFormatted,
                'pic_name'      => $picName,
                'pic_role'      => $picRole,
                'pic_initials'  => $picInitials,
                'status'        => $status,
                'status_label'  => $statusLabel,
                'action_label'  => $actionLabel,
                'action_tone'   => $actionTone,
            ];
        }

        $totalActive = $counts['open'] + $counts['in_progress'] + $counts['needs_action'];
        $totalAll    = count($tickets);

        // 5. Metrik Stat Summary Bar
        $metrics = [
            'open' => [
                'count'      => $counts['open'],
                'title'      => 'TIKET TERBUKA',
                'subtitle'   => 'antrean',
                'detail'     => 'Menunggu alokasi PIC',
                'icon'       => 'fa-regular fa-envelope',
                'theme'      => 'blue',
            ],
            'in_progress' => [
                'count'      => $counts['in_progress'],
                'title'      => 'SEDANG DIPROSES',
                'subtitle'   => 'berjalan',
                'detail'     => 'Dalam penanganan HC',
                'icon'       => 'fa-regular fa-clock',
                'theme'      => 'amber',
            ],
            'action_needed' => [
                'count'      => $counts['needs_action'],
                'title'      => 'BUTUH RESPON',
                'subtitle'   => 'tindakan',
                'detail'     => 'Perhatian Segera',
                'icon'       => 'fa-regular fa-comment-dots',
                'theme'      => 'purple',
                'has_dot'    => $counts['needs_action'] > 0,
            ],
            'resolved' => [
                'count'      => $counts['resolved'],
                'title'      => 'TIKET SELESAI',
                'subtitle'   => 'riwayat',
                'detail'     => 'CSAT 4.9/5 • 100% SLA',
                'icon'       => 'fa-regular fa-circle-check',
                'theme'      => 'green',
            ],
        ];

        // 6. Tab Counts
        $tabCounts = [
            'all'         => $totalAll,
            'action'      => $counts['needs_action'],
            'in_progress' => $counts['in_progress'] + $counts['open'],
            'resolved'    => $counts['resolved'],
        ];

        // 7. Standar SLA Layanan HC
        $slaStandards = [
            [
                'title'       => 'Reset Akun & OTP',
                'badge'       => 'Maks. 2 Jam',
                'badge_tone'  => 'success',
                'description' => 'Penyelesaian instan otomatisasi sistem IT HC',
                'rate'        => '99.4% SLA Tercapai',
                'link'        => base_url('ticket/create?type=reset_otp'),
            ],
            [
                'title'       => 'Koreksi Absensi & Cuti',
                'badge'       => 'Maks. 4 Jam',
                'badge_tone'  => 'warning',
                'description' => 'Memerlukan verifikasi riwayat biometrik & atasan',
                'rate'        => '98.2% SLA Tercapai',
                'link'        => base_url('ticket/create?type=koreksi_absensi'),
            ],
            [
                'title'       => 'Klaim Asuransi & Payroll',
                'badge'       => '1 Hari Kerja',
                'badge_tone'  => 'info',
                'description' => 'Koordinasi langsung dengan Third-Party Administrator (TPA)',
                'rate'        => '100% SLA Tercapai',
                'link'        => base_url('ticket/create?type=klaim_asuransi'),
            ],
            [
                'title'       => 'Surat Keterangan Kerja (Paklaring)',
                'badge'       => '2 Hari Kerja',
                'badge_tone'  => 'neutral',
                'description' => 'Verifikasi tanda tangan digital legal & HC Head',
                'rate'        => '99.0% SLA Tercapai',
                'link'        => base_url('ticket/create?type=paklaring'),
            ],
        ];

        // 8. Link Cepat Bantuan
        $quickLinks = [
            [
                'icon'  => 'fa-regular fa-file-lines',
                'title' => 'Panduan Registrasi Tanggungan BPJS',
                'url'   => base_url('pusat-bantuan?q=bpjs'),
            ],
            [
                'icon'  => 'fa-regular fa-calendar-check',
                'title' => 'Jadwal Cut-Off Presensi & Payroll 2026',
                'url'   => base_url('pusat-bantuan?q=cut-off'),
            ],
            [
                'icon'  => 'fa-regular fa-circle-question',
                'title' => 'FAQ Kendala Login Akun HRIS',
                'url'   => base_url('pusat-bantuan?q=login-hris'),
            ],
            [
                'icon'  => 'fa-solid fa-notes-medical',
                'title' => 'Prosedur Klaim Rawat Inap & Kacamata',
                'url'   => base_url('pusat-bantuan?q=klaim-kesehatan'),
            ],
        ];

        $viewData = [
            'title'          => 'Dashboard Karyawan - HC Helpdesk',
            'activePage'     => 'dashboard',
            'userName'       => $userName,
            'division'       => $division,
            'currentDate'    => $currentDate,
            'metrics'        => $metrics,
            'tickets'        => $tickets,
            'slaStandards'   => $slaStandards,
            'quickLinks'     => $quickLinks,
            'userRefData'    => $userRefData,
            'totalActive'    => $totalActive,
            'displayedCount' => count($tickets),
            'tabCounts'      => $tabCounts,
        ];

        return view('pages/user/dashboard', $viewData);
    }

    /**
     * Format tanggal tiket: 08 Okt 2026 • 09:15 WIB
     */
    protected function formatTicketDate(int $timestamp): string
    {
        $months = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
        ];

        $day   = date('d', $timestamp);
        $month = $months[date('m', $timestamp)] ?? 'Okt';
        $year  = date('Y', $timestamp);
        $time  = date('H:i', $timestamp);

        return "{$day} {$month} {$year} • {$time} WIB";
    }

    /**
     * Format tanggal Indonesia untuk greeting header
     */
    protected function formatIndonesianDate(): string
    {
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $dayName = $days[date('l')] ?? 'Kamis';
        $dayNum  = date('d');
        $month   = $months[date('m')] ?? 'Oktober';
        $year    = date('Y');
        $time    = date('H:i');

        return "{$dayName}, {$dayNum} {$month} {$year} • {$time} WIB";
    }
}
