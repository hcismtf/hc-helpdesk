<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\RequestTypeModel;
use App\Services\TicketAdminService;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;
use Throwable;

/**
 * Controller Admin Tiket (Clean & Skinny Controller)
 * Menangani routing, validasi request HTTP, dan mendelegasikan business logic ke TicketAdminService
 */
class TicketAdminController extends BaseController
{
    protected TicketAdminService $ticketAdminService;
    protected RequestTypeModel $requestTypeModel;
    protected \App\Models\SlaModel $slaModel;

    public function __construct()
    {
        $this->ticketAdminService = new TicketAdminService();
        $this->requestTypeModel   = new RequestTypeModel();
        $this->slaModel           = new \App\Models\SlaModel();
    }

    /**
     * Menampilkan dashboard tiket (Tabel & Kanban Swimlane Grid)
     */
    public function ticket_dashboard()
    {
        $perPage = max(1, (int) ($this->request->getGet('per_page') ?? 12));
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));

        $scope = trim($this->request->getGet('scope') ?? '');

        $filters = [
            'start'    => $this->request->getGet('start'),
            'end'      => $this->request->getGet('end'),
            'priority' => $this->request->getGet('priority'),
            'type'     => $this->request->getGet('type'),
            'status'   => $this->request->getGet('status'),
            'search'   => trim($this->request->getGet('search') ?? ''),
            'scope'    => $scope,
        ];

        $userContext = $this->buildUserContext();

        $data = $this->ticketAdminService->getDashboardTickets($filters, $page, $perPage, $userContext);

        // Ambil daftar tipe pengajuan aktif untuk filter dropdown
        $requestTypes = $this->requestTypeModel
            ->where('status', 'Active')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Ambil daftar SLA Categories untuk quick dropdown
        $slaList = $this->slaModel->orderBy('priority', 'ASC')->findAll();

        // Bangun pagination URL query string
        $additionalParams = '&per_page=' . $perPage;
        foreach ($filters as $k => $v) {
            if (!empty($v)) {
                $additionalParams .= "&{$k}=" . urlencode($v);
            }
        }

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $data['totalPages'],
            base_url('admin/ticket_dashboard'),
            $additionalParams
        );

        return view('admin/ticket_dashboard', array_merge($filters, [
            'tickets'        => $data['tickets'],
            'kanbanTickets'  => $data['kanbanTickets'],
            'paginationHTML' => $paginationHTML,
            'perPage'        => $perPage,
            'currentPage'    => $page,
            'totalPages'     => $data['totalPages'],
            'totalRecords'   => $data['totalRecords'],
            'active'         => 'tickets',
            'subActive'      => ($scope === 'my') ? 'my_tickets' : 'all_tickets',
            'requestTypes'   => $requestTypes,
            'slaList'        => $slaList,
        ]));
    }

    /**
     * AJAX endpoint: Update status tiket dari drag & drop Kanban board atau Table dropdown
     */
    public function update_ticket_status()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $ticketId    = $this->request->getPost('ticket_id');
        $rawStatus   = trim($this->request->getPost('status') ?? '');
        $reasonNotes = trim($this->request->getPost('reason_notes') ?? '');

        if (empty($ticketId) || empty($rawStatus)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID tiket dan status wajib diisi.']);
        }

        $actorId   = (string) (session('user_id') ?? 'system');
        $actorName = (string) (session('name') ?? session('username') ?? 'Admin');

        try {
            $result = $this->ticketAdminService->updateTicketStatus($ticketId, $rawStatus, $actorId, $actorName, $reasonNotes);

            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => 'Status tiket berhasil diubah menjadi: ' . $result['label'],
                'new_status' => $result['new_status'],
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX endpoint: Update SLA tiket dari Table dropdown
     */
    public function update_ticket_sla()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $ticketId = $this->request->getPost('ticket_id');
        $slaInput = trim($this->request->getPost('sla') ?? $this->request->getPost('priority') ?? '');

        if (empty($ticketId) || empty($slaInput)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID tiket dan SLA wajib diisi.']);
        }

        $actorId   = (string) (session('user_id') ?? 'system');
        $actorName = (string) (session('name') ?? session('username') ?? 'Admin');

        try {
            $result = $this->ticketAdminService->updateTicketSla($ticketId, $slaInput, $actorId, $actorName);

            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => 'SLA tiket berhasil diubah menjadi: ' . $result['new_priority'],
                'new_priority' => $result['new_priority'],
                'sla_id'       => $result['sla_id'],
                'due_date'     => $result['due_date'],
                'seconds_left' => $result['seconds_left'],
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Menampilkan halaman detail tiket, chat riwayat, dan lampiran
     *
     * @param string|int $id
     */
    public function ticket_detail($id)
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses detail tiket.');
        }

        $userContext = $this->buildUserContext();

        if (!$userContext['is_staff']) {
            return redirect()->to(base_url('my/ticket/' . $id));
        }

        try {
            $data = $this->ticketAdminService->getTicketDetailData((string) $id, $userContext);

            return view('admin/ticket_detail', array_merge($data['detailData'], [
                'ticket'          => $data['ticket'],
                'users'           => $data['users'],
                'replies'         => $data['replies'],
                'hasReply'        => $data['hasReply'],
                'assignedName'    => $data['assignedName'],
                'originalMessage' => $data['originalMessage'],
            ]));

        } catch (InvalidArgumentException $e) {
            log_message('error', '[TicketAdminController::ticket_detail] ' . $e->getMessage());
            return redirect()->to('/')->with('error', $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', '[TicketAdminController::ticket_detail] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->to('/admin/ticket_dashboard')->with('error', 'Tiket tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mengirimkan balasan/tanggapan dari form detail tiket
     *
     * @param string|int $id
     */
    public function send_reply($id)
    {
        $input = [
            'reply'       => $this->request->getPost('reply'),
            'status'      => $this->request->getPost('status'),
            'priority'    => $this->request->getPost('priority'),
            'assigned_to' => $this->request->getPost('assigned_to'),
            'is_internal' => (int) ($this->request->getPost('is_internal') ?? 0),
        ];

        $sessionData = [
            'user_id'  => session('user_id'),
            'username' => session('name') ?? session('username') ?? 'Admin',
        ];

        try {
            $this->ticketAdminService->sendReply((string) $id, $input, $sessionData);
            return redirect()->back()->with('success', 'Balasan tiket berhasil dikirim.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengirim balasan: ' . $e->getMessage());
        }
    }

    /**
     * Mengakses lampiran berkas tiket secara aman
     *
     * @param string $filename
     */
    public function view($filename)
    {
        $candidatePaths = [
            FCPATH . 'uploads/' . ltrim($filename, '/\\'),
            FCPATH . 'uploads/images-attachment/' . ltrim($filename, '/\\'),
            FCPATH . 'uploads/attachments/' . ltrim($filename, '/\\'),
        ];

        $filePath = false;
        foreach ($candidatePaths as $candidate) {
            $real = realpath($candidate);
            if ($real !== false && is_file($real) && str_starts_with($real, realpath(FCPATH . 'uploads'))) {
                $filePath = $real;
                break;
            }
        }

        if ($filePath === false) {
            throw PageNotFoundException::forPageNotFound('Berkas tidak ditemukan.');
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Membangun context informasi pengguna yang sedang login
     */
    protected function buildUserContext(): array
    {
        $roleRaw     = (string) (session('role') ?? session('role_name') ?? '');
        $cleanRole   = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]/', '', $roleRaw)));
        $perms       = session('user_permissions') ?? session('permissions') ?? [];
        $username    = (string) (session('username') ?? session('employee_no') ?? '');

        // 1. Superadmin check (role name, atau superadmin config)
        $isSuperadmin = ($cleanRole === 'superadmin');
        try {
            if (function_exists('config')) {
                $superConfig = config('Superadmin');
                if (!empty($superConfig->username) && strtolower($username) === strtolower($superConfig->username)) {
                    $isSuperadmin = true;
                }
            }
        } catch (\Throwable $e) {}

        // 2. Permission check: ticket:read, ticket.read, tickets, ticket:update, ticket:reply, ticket:status
        $hasTicketPerm = in_array('ticket:read', $perms, true)
            || in_array('ticket.read', $perms, true)
            || in_array('tickets', $perms, true)
            || in_array('ticket:update', $perms, true)
            || in_array('ticket.update', $perms, true)
            || in_array('ticket:reply', $perms, true)
            || in_array('ticket.reply', $perms, true)
            || in_array('ticket:status', $perms, true)
            || in_array('ticket.status', $perms, true);

        // 3. Admin access check via AuthService
        $isStaff = $isSuperadmin || $hasTicketPerm || \App\Services\AuthService::canAccessAdmin();

        return [
            'is_staff'      => $isStaff,
            'is_superadmin' => $isSuperadmin,
            'user_id'       => session('user_id') ?? '',
            'employee_no'   => $username,
            'name'          => session('name') ?? '',
            'permissions'   => $perms,
            'role'          => $cleanRole,
        ];
    }
}
