<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\TicketTransactionModel;
use App\Models\TicketAttachmentModel;
use App\Models\TicketResponseModel;
use App\Models\UserModel;
use App\Models\SlaModel;
use App\Models\RequestTypeModel;
use App\Services\TicketEmailService;
use DateTime;
use CodeIgniter\Exceptions\PageNotFoundException;

class TicketAdminController extends BaseController
{
    protected $emailService;

    public function __construct()
    {
        $this->emailService = new TicketEmailService();
    }

    /**
     * Display ticket dashboard with filter, pagination and Kanban data
     */
    public function Ticket_dashboard()
    {
        $model = new TicketTransactionModel();
        $perPage = (int) ($this->request->getGet('per_page') ?? 12);
        $page = (int) ($this->request->getGet('page') ?? 1);
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $priority = $this->request->getGet('priority');
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        if ($perPage <= 0) $perPage = 12;
        if ($page <= 0) $page = 1;

        // Build base query
        $query = $model;
        if ($start) $query = $query->where('created_date >=', $start . ' 00:00:00');
        if ($end) $query = $query->where('created_date <=', $end . ' 23:59:59');
        if (!empty($priority)) $query = $query->where('ticket_priority', $priority);
        if (!empty($type)) $query = $query->where('req_type', $type);
        if (!empty($status)) $query = $query->where('ticket_status', $status);
        if (!empty($search)) {
            $query = $query->groupStart()
                ->like('id', $search)
                ->orLike('emp_name', $search)
                ->orLike('subject', $search)
                ->orLike('req_type', $search)
                ->groupEnd();
        }

        $totalRecords = $query->countAllResults(false);
        $ticketsRaw = $query->orderBy('created_date', 'DESC')
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
            ->get()
            ->getResult('array');

        $encrypter = \Config\Services::encrypter();

        $tickets = [];
        foreach ($ticketsRaw as $ticket) {
            $nip_decrypted = '';
            if (!empty($ticket['nip_encrypted'])) {
                try {
                    $nip_decrypted = $encrypter->decrypt(hex2bin($ticket['nip_encrypted']));
                } catch (\Exception $e) {
                    $nip_decrypted = '[Invalid]';
                }
            }
            $ticket['emp_nip'] = $nip_decrypted;
            $tickets[] = $ticket;
        }

        // Kanban Swimlane dataset (up to 300 recent records)
        $kanbanQuery = (clone $model);
        if ($start) $kanbanQuery = $kanbanQuery->where('created_date >=', $start . ' 00:00:00');
        if ($end) $kanbanQuery = $kanbanQuery->where('created_date <=', $end . ' 23:59:59');
        if (!empty($priority)) $kanbanQuery = $kanbanQuery->where('ticket_priority', $priority);
        if (!empty($type)) $kanbanQuery = $kanbanQuery->where('req_type', $type);
        if (!empty($status)) $kanbanQuery = $kanbanQuery->where('ticket_status', $status);
        if (!empty($search)) {
            $kanbanQuery = $kanbanQuery->groupStart()
                ->like('id', $search)
                ->orLike('emp_name', $search)
                ->orLike('subject', $search)
                ->orLike('req_type', $search)
                ->groupEnd();
        }
        $kanbanRaw = $kanbanQuery->orderBy('created_date', 'DESC')->limit(300)->findAll();
        $kanbanTickets = [];
        foreach ($kanbanRaw as $kt) {
            $nip_dec = '';
            if (!empty($kt['nip_encrypted'])) {
                try {
                    $nip_dec = $encrypter->decrypt(hex2bin($kt['nip_encrypted']));
                } catch (\Exception $e) {
                    $nip_dec = '[Invalid]';
                }
            }
            $kt['emp_nip'] = $nip_dec;
            $kanbanTickets[] = $kt;
        }

        // Active request types
        try {
            $requestTypeModel = new RequestTypeModel();
            $requestTypes = $requestTypeModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $requestTypes = [];
        }

        $totalPages = $perPage > 0 ? ceil($totalRecords / $perPage) : 1;

        $additionalParams = '&per_page=' . $perPage;
        if ($start) $additionalParams .= '&start=' . urlencode($start);
        if ($end) $additionalParams .= '&end=' . urlencode($end);
        if ($priority) $additionalParams .= '&priority=' . urlencode($priority);
        if ($type) $additionalParams .= '&type=' . urlencode($type);
        if ($status) $additionalParams .= '&status=' . urlencode($status);
        if ($search) $additionalParams .= '&search=' . urlencode($search);

        $paginationHTML = $this->generatePaginationHTML($page, $totalPages, base_url('admin/Ticket_dashboard'), $additionalParams);

        return view('admin/Ticket_dashboard', [
            'tickets'        => $tickets,
            'kanbanTickets'  => $kanbanTickets,
            'paginationHTML' => $paginationHTML,
            'perPage'        => $perPage,
            'currentPage'    => $page,
            'totalPages'     => $totalPages,
            'totalRecords'   => $totalRecords,
            'active'         => 'tickets',
            'start'          => $start,
            'end'            => $end,
            'priority'       => $priority,
            'type'           => $type,
            'status'         => $status,
            'search'         => $search,
            'requestTypes'   => $requestTypes,
        ]);
    }

    /**
     * AJAX endpoint to update ticket status from Kanban board
     */
    public function update_ticket_status()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $ticketId = $this->request->getPost('ticket_id');
        $newStatus = strtolower(trim($this->request->getPost('status') ?? ''));

        $allowedStatuses = ['open', 'in_progress', 'done', 'closed'];
        if (!in_array($newStatus, $allowedStatuses)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Status tidak valid.']);
        }

        $ticketModel = new TicketTransactionModel();
        $ticket = $ticketModel->find($ticketId);
        if (!$ticket) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Tiket tidak ditemukan.']);
        }

        $userId = session('user_id') ?? 9999;
        $username = session('username') ?? 'Admin';

        $updateData = [
            'ticket_status' => $newStatus,
            'modified_date' => date('Y-m-d H:i:s'),
            'modified_by'   => $username
        ];

        if ($newStatus === 'closed' || $newStatus === 'done') {
            $updateData['finish_date'] = date('Y-m-d H:i:s');
        } else {
            $updateData['finish_date'] = null;
        }

        if (empty($ticket['first_response_at']) && $newStatus === 'in_progress') {
            $updateData['first_response_at'] = date('Y-m-d H:i:s');
        }

        $ticketModel->update($ticketId, $updateData);

        // Record history log
        try {
            $trxModel = new TicketResponseModel();
            $trxModel->insert([
                'ticket_id'    => $ticketId,
                'user_id'      => $userId,
                'author_name'  => $username,
                'status'       => $newStatus,
                'priority'     => $ticket['ticket_priority'] ?? 'medium',
                'assigned_to'  => $ticket['assigned_to'] ?? null,
                'reply'        => 'Status changed to ' . ucwords(str_replace('_', ' ', $newStatus)) . ' via Kanban Board',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Status tiket berhasil diubah menjadi: ' . ucwords(str_replace('_', ' ', $newStatus)),
            'new_status' => $newStatus
        ]);
    }

    /**
     * Show ticket details, chat replies, and attachments
     *
     * @param string|int $id
     */
    public function Ticket_detail($id)
    {
        $model = new TicketTransactionModel();
        $userModel = new UserModel();

        if (!session('isLoggedIn') && is_numeric($id)) {
            throw PageNotFoundException::forPageNotFound("Ticket not found");
        }

        $ticket = null;
        if (is_numeric($id) || is_string($id)) {
            $ticket = $model->find($id);
        }
        if (!$ticket) {
            $ticket = $model->where('reporter_id', $id)->first();
        }
        if (!$ticket) {
            return redirect()->to('/admin/Ticket_dashboard')->with('error', 'Ticket not found');
        }

        $users = $userModel
            ->where('status', 'active')
            ->groupStart()
                ->where('is_deleted !=', 1)
                ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->findAll();

        $attModel = new TicketAttachmentModel();
        $attachmentsRaw = $attModel->where('ticket_id', $ticket['id'])->findAll();
        $attachments = [];

        $encrypter = \Config\Services::encrypter();
        if (!empty($ticket['nip_encrypted'])) {
            try {
                $ticket['emp_nip'] = $encrypter->decrypt(hex2bin($ticket['nip_encrypted']));
            } catch (\Exception $e) {
                $ticket['emp_nip'] = '[Invalid]';
            }
        }

        foreach ($attachmentsRaw as $att) {
            try {
                $file_name = $encrypter->decrypt(hex2bin($att['file_name']));
                $file_path = $encrypter->decrypt(hex2bin($att['file_path']));
            } catch (\Exception $e) {
                $file_name = '[Invalid]';
                $file_path = '';
            }
            $attachments[] = [
                'file_name' => $file_name,
                'file_path' => $file_path
            ];
        }

        $trxModel = new TicketResponseModel();
        $assignedName = '-';
        if (!empty($ticket['assigned_to'])) {
            $assignedUser = $userModel->find($ticket['assigned_to']);
            if ($assignedUser) {
                $assignedName = $assignedUser['name'];
            }
        }

        $ticketId = $ticket['id'];
        $repliesRaw = $trxModel->where('ticket_id', $ticketId)->orderBy('created_at', 'asc')->findAll();
        $replies = [];

        foreach ($repliesRaw as $r) {
            $isUser = ($r['submitted_by'] === 'user' || $r['submitted_by'] == ($ticket['reporter_id'] ?? '') || $r['submitted_by'] == ($ticket['created_by'] ?? ''));
            if ($isUser) {
                $authorName = $ticket['emp_name'] ?? $ticket['created_by'] ?? 'User';
            } else {
                $adminUser = $userModel->find($r['submitted_by']);
                $authorName = $adminUser ? $adminUser['name'] : ($assignedName !== '-' ? $assignedName : 'Admin');
            }

            $replies[] = [
                'is_user'    => $isUser,
                'author'     => $authorName ?: 'User',
                'created_at' => $r['created_at'],
                'text'       => $r['reply']
            ];
        }

        $detailService = new \App\Services\TicketDetailService();
        $detailData = $detailService->buildDetailViewData($ticket, $repliesRaw, $attachments, $userModel);

        return view('admin/Ticket_detail', array_merge($detailData, [
            'users'           => $users,
            'replies'         => $replies,
            'hasReply'        => count($replies) > 0,
            'assignedName'    => $detailData['assignedSpecialist']['name'],
            'originalMessage' => $ticket['message'] ?? ''
        ]));
    }

    /**
     * Handle replies for tickets (from Admin or User)
     *
     * @param string|int $id
     */
    public function send_reply($id)
    {
        $ticketModel = new TicketTransactionModel();
        $ticket = null;

        $replyText = $this->request->getPost('reply');
        $inputStatus = $this->request->getPost('status');
        $inputPriority = $this->request->getPost('priority');
        $assignedTo = $this->request->getPost('assigned_to');

        if (!session('isLoggedIn') && is_numeric($id)) {
            throw PageNotFoundException::forPageNotFound("Ticket not found");
        }

        if (is_numeric($id) || is_string($id)) {
            $ticket = $ticketModel->find($id);
        }
        if (!$ticket) {
            $ticket = $ticketModel->where('reporter_id', $id)->first();
        }
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        $trxModel = new TicketResponseModel();
        $ticketId = $ticket['id'];

        if (session('isLoggedIn')) {
            $userId = session('user_id');
            $role = session('role');
            $username = session('username') ?? 'system';

            if ($role === 'superadmin') {
                $userId = 9999;
                $username = 'superadmin';
            }

            $status = !empty($inputStatus) ? $inputStatus : ($ticket['ticket_status'] ?? 'open');
            $priority = !empty($inputPriority) ? $inputPriority : ($ticket['ticket_priority'] ?? 'medium');

            if (empty($assignedTo)) {
                $lastTrx = $trxModel->where('ticket_id', $ticketId)->orderBy('created_at', 'desc')->first();
                $assignedTo = !empty($lastTrx['assigned_to']) ? $lastTrx['assigned_to'] : ($ticket['assigned_to'] ?? null);
            }
        } else {
            $userId = $ticket['reporter_id'] ?? $ticket['created_by'] ?? null;
            $username = $ticket['created_by'] ?? $ticket['emp_name'] ?? 'User';
            $status = $ticket['ticket_status'] ?? 'open';
            $priority = $ticket['ticket_priority'] ?? 'medium';
            $assignedTo = $ticket['assigned_to'] ?? null;
        }

        // SLA resolution time calculation
        $slaModel = new SlaModel();
        $sla = $slaModel->where('priority', $priority)->first();
        $resolutionTime = $sla ? (int)$sla['resolution_time'] : 24;

        $createdDate = $ticket['created_date'] ?? date('Y-m-d H:i:s');
        $dueDate = (new DateTime($createdDate))->modify('+' . $resolutionTime . ' hours')->format('Y-m-d H:i:s');

        $firstResponseAt = $ticket['first_response_at'] ?? null;
        if (empty($firstResponseAt)) {
            $firstResponseAt = date('Y-m-d H:i:s');
        }

        $finishDate = ($status === 'closed') ? date('Y-m-d H:i:s') : null;

        // Insert reply record
        $trxModel->insert([
            'ticket_id'    => $ticketId,
            'user_id'      => $userId,
            'author_name'  => $username,
            'status'       => $status,
            'priority'     => $priority,
            'assigned_to'  => $assignedTo,
            'reply'        => $replyText,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Update ticket record
        $updateData = [
            'ticket_status'     => $status,
            'ticket_priority'   => $priority,
            'assigned_to'       => $assignedTo,
            'due_date'          => $dueDate,
            'first_response_at' => $firstResponseAt,
            'modified_date'     => date('Y-m-d H:i:s'),
            'modified_by'       => $username
        ];
        if ($finishDate) {
            $updateData['finish_date'] = $finishDate;
        }
        $ticketModel->update($ticketId, $updateData);

        // Send Email Notification
        if (session('isLoggedIn')) {
            $assignedName = '-';
            if ($assignedTo) {
                $userModel = new UserModel();
                $assignedUser = $userModel->find($assignedTo);
                if ($assignedUser) {
                    $assignedName = $assignedUser['name'];
                }
            }
            $this->emailService->sendTicketStatusNotification($ticket, $status, $replyText, $assignedName);
        } else {
            if (!empty($assignedTo)) {
                $userModel = new UserModel();
                $adminUser = $userModel->find($assignedTo);
                if ($adminUser) {
                    $this->emailService->sendUserReplyNotification($ticket, $adminUser, $replyText);
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Securely serve attachment files
     *
     * @param string $filename
     */
    public function view($filename)
    {
        $basePath = 'D:/uploads/images-attachment/';
        $filePath = realpath($basePath . $filename);

        if ($filePath === false || strpos($filePath, realpath($basePath)) !== 0 || !is_file($filePath)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mimeType = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setBody(file_get_contents($filePath));
    }
}
