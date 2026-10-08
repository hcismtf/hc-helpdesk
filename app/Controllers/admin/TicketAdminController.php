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

    public function __construct()
    {
        $this->ticketAdminService = new TicketAdminService();
        $this->requestTypeModel   = new RequestTypeModel();
    }

    /**
     * Menampilkan dashboard tiket (Tabel & Kanban Swimlane Grid)
     */
    public function ticket_dashboard()
    {
        $perPage = max(1, (int) ($this->request->getGet('per_page') ?? 12));
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));

        $filters = [
            'start'    => $this->request->getGet('start'),
            'end'      => $this->request->getGet('end'),
            'priority' => $this->request->getGet('priority'),
            'type'     => $this->request->getGet('type'),
            'status'   => $this->request->getGet('status'),
            'search'   => trim($this->request->getGet('search') ?? ''),
        ];

        $userContext = $this->buildUserContext();

        $data = $this->ticketAdminService->getDashboardTickets($filters, $page, $perPage, $userContext);

        // Ambil daftar tipe pengajuan aktif untuk filter dropdown
        $requestTypes = $this->requestTypeModel
            ->where('status', 'Active')
            ->orderBy('name', 'ASC')
            ->findAll();

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
            'requestTypes'   => $requestTypes,
        ]));
    }

    /**
     * AJAX endpoint: Update status tiket dari drag & drop Kanban board
     */
    public function update_ticket_status()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $ticketId  = $this->request->getPost('ticket_id');
        $rawStatus = trim($this->request->getPost('status') ?? '');

        if (empty($ticketId) || empty($rawStatus)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID tiket dan status wajib diisi.']);
        }

        $actorId   = (string) (session('user_id') ?? 'system');
        $actorName = (string) (session('username') ?? 'Admin');

        try {
            $result = $this->ticketAdminService->updateTicketStatus($ticketId, $rawStatus, $actorId, $actorName);

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
            return redirect()->to('/')->with('error', $e->getMessage());
        } catch (Throwable $e) {
            return redirect()->to('/admin/ticket_dashboard')->with('error', 'Tiket tidak ditemukan.');
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
        ];

        $sessionData = [
            'user_id'  => session('user_id'),
            'username' => session('username') ?? 'Admin',
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
        $basePath = FCPATH . 'uploads/images-attachment/';
        $filePath = realpath($basePath . $filename);

        if ($filePath === false || !str_starts_with($filePath, realpath($basePath)) || !is_file($filePath)) {
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
        $role  = strtolower(session('role') ?? '');
        $perms = session('user_permissions') ?? [];

        $isStaff = ($role === 'superadmin' || in_array('ticket:read', $perms, true) || in_array('tickets', $perms, true));

        return [
            'is_staff'    => $isStaff,
            'user_id'     => session('user_id') ?? '',
            'employee_no' => session('employee_no') ?? session('username') ?? '',
            'name'        => session('name') ?? '',
        ];
    }
}
