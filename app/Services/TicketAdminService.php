<?php

namespace App\Services;

use App\Entities\TicketEntity;
use App\Entities\TicketStatus;
use App\Models\TicketModel;
use App\Models\TicketResponseModel;
use App\Models\TicketAttachmentModel;
use App\Models\UserModel;
use App\Models\SlaModel;
use App\Models\RequestTypeModel;
use App\Services\TicketEmailService;
use App\Services\TicketDetailService;
use DateTime;
use RuntimeException;
use InvalidArgumentException;

/**
 * Service khusus untuk kebutuhan operasional Backoffice / Admin
 * (Dashboard filter, Kanban swimlane, Status update, Assignment PIC, Replies & Attachments)
 */
class TicketAdminService
{
    protected TicketModel $ticketModel;
    protected TicketResponseModel $responseModel;
    protected TicketAttachmentModel $attachmentModel;
    protected UserModel $userModel;
    protected SlaModel $slaModel;
    protected RequestTypeModel $requestTypeModel;
    protected TicketEmailService $emailService;
    protected TicketDetailService $detailService;

    public function __construct(
        ?TicketModel $ticketModel = null,
        ?TicketResponseModel $responseModel = null,
        ?TicketAttachmentModel $attachmentModel = null,
        ?UserModel $userModel = null,
        ?SlaModel $slaModel = null,
        ?RequestTypeModel $requestTypeModel = null,
        ?TicketEmailService $emailService = null,
        ?TicketDetailService $detailService = null
    ) {
        $this->ticketModel      = $ticketModel ?? new TicketModel();
        $this->responseModel    = $responseModel ?? new TicketResponseModel();
        $this->attachmentModel  = $attachmentModel ?? new TicketAttachmentModel();
        $this->userModel        = $userModel ?? new UserModel();
        $this->slaModel         = $slaModel ?? new SlaModel();
        $this->requestTypeModel = $requestTypeModel ?? new RequestTypeModel();
        $this->emailService     = $emailService ?? new TicketEmailService();
        $this->detailService    = $detailService ?? new TicketDetailService();
    }

    /**
     * Mengambil daftar tiket dengan filter, pagination, dan dekripsi informasi pelapor
     */
    public function getDashboardTickets(array $filters, int $page = 1, int $perPage = 12, ?array $userContext = null): array
    {
        $builder = $this->ticketModel->builder();
        $this->applyFiltersToBuilder($builder, $filters, $userContext);

        $totalRecords = $builder->countAllResults(false);

        $ticketsRaw = $builder->orderBy('created_date', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $tickets = $this->hydrateTicketList($ticketsRaw);

        // Ambil data kanban swimlane (limit 300)
        $kanbanBuilder = $this->ticketModel->builder();
        $this->applyFiltersToBuilder($kanbanBuilder, $filters, $userContext);
        $kanbanRaw = $kanbanBuilder->orderBy('created_date', 'DESC')->limit(300)->get()->getResultArray();
        $kanbanTickets = $this->hydrateTicketList($kanbanRaw);

        return [
            'tickets'       => $tickets,
            'kanbanTickets' => $kanbanTickets,
            'totalRecords'  => $totalRecords,
            'totalPages'    => $perPage > 0 ? (int) ceil($totalRecords / $perPage) : 1,
            'currentPage'   => $page,
            'perPage'       => $perPage,
        ];
    }

    /**
     * Memperbarui status tiket (misal dari drag & drop Kanban board)
     */
    public function updateTicketStatus(string $ticketId, string $rawStatus, string $actorId, string $actorName): array
    {
        $status = TicketStatus::normalize($rawStatus);
        if (!TicketStatus::isValid($status)) {
            throw new InvalidArgumentException('Status tiket tidak valid: ' . $rawStatus);
        }

        $ticket = $this->ticketModel->find($ticketId);
        if (!$ticket) {
            throw new RuntimeException('Tiket tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $updateData = [
            'status'        => $status,
            'modified_date' => $now,
            'modified_by'   => $actorName,
        ];

        $this->ticketModel->update($ticketId, $updateData);

        // Catat ke response timeline sebagai riwayat sistem
        try {
            $this->responseModel->insert([
                'ticket_id'   => $ticketId,
                'user_id'     => $actorId,
                'author_name' => $actorName,
                'status'      => $status,
                'reply'       => 'Status changed to ' . TicketStatus::getLabel($status) . ' via Kanban Board',
                'is_internal' => 1,
                'created_at'  => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mencatat log status response: ' . $e->getMessage());
        }

        return [
            'status'     => 'success',
            'new_status' => $status,
            'label'      => TicketStatus::getLabel($status),
        ];
    }

    /**
     * Menyiapkan seluruh payload detail tiket untuk view
     */
    public function getTicketDetailData(string $id, ?array $userContext = null): array
    {
        $ticket = $this->ticketModel->find($id);
        if (!$ticket) {
            $ticket = $this->ticketModel->where('reporter_id', $id)->first();
        }
        if (!$ticket) {
            $ticket = $this->ticketModel->getByTicketNo($id);
        }

        if (!$ticket) {
            throw new RuntimeException('Tiket tidak ditemukan.');
        }

        $ticketArray = is_object($ticket) ? $ticket->toArray() : (array) $ticket;

        // Cek isolasi pengguna non-admin jika diperlukan
        if ($userContext && empty($userContext['is_staff'])) {
            $empNo = $userContext['employee_no'] ?? '';
            $empName = $userContext['name'] ?? '';
            $userId = $userContext['user_id'] ?? '';

            $isOwner = (
                ($ticketArray['created_by'] ?? '') === $empNo ||
                ($ticketArray['created_by'] ?? '') === $empName ||
                ($ticketArray['reporter_id'] ?? '') === $userId ||
                ($ticketArray['reporter_id'] ?? '') === $empNo
            );

            if (!$isOwner) {
                throw new InvalidArgumentException('Akses ditolak: Anda tidak memiliki izin untuk melihat tiket karyawan lain.');
            }
        }

        // Ambil daftar user aktif untuk assignment dropdown
        $users = $this->userModel
            ->where('status', 'active')
            ->groupStart()
                ->where('is_deleted !=', 1)
                ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->findAll();

        // Ambil data attachment
        $ticketId = $ticketArray['id'];
        $attachmentsRaw = $this->attachmentModel->where('ticket_id', $ticketId)->findAll();
        $attachments = $this->decryptAttachments($attachmentsRaw);

        // Ambil balasan / tanggapan
        $repliesRaw = $this->responseModel->where('ticket_id', $ticketId)->orderBy('created_at', 'asc')->findAll();
        $replies = $this->hydrateReplies($repliesRaw, $ticketArray);

        // Build data SLA & metric dari TicketDetailService
        $detailData = $this->detailService->buildDetailViewData($ticketArray, $repliesRaw, $attachments, $this->userModel);

        return [
            'ticket'          => $ticketArray,
            'users'           => $users,
            'replies'         => $replies,
            'attachments'     => $attachments,
            'hasReply'        => count($replies) > 0,
            'assignedName'    => $detailData['assignedSpecialist']['name'] ?? '-',
            'originalMessage' => $ticketArray['description'] ?? ($ticketArray['message'] ?? ''),
            'detailData'      => $detailData,
        ];
    }

    /**
     * Memproses pengiriman balasan/tanggapan dari admin atau pelapor
     */
    public function sendReply(string $id, array $input, array $sessionData): TicketEntity
    {
        $ticket = $this->ticketModel->find($id) 
            ?? $this->ticketModel->where('reporter_id', $id)->first() 
            ?? $this->ticketModel->getByTicketNo($id);

        if (!$ticket) {
            throw new RuntimeException('Tiket tidak ditemukan.');
        }

        $ticketArray = is_object($ticket) ? $ticket->toArray() : (array) $ticket;
        $ticketId = $ticketArray['id'];

        $replyText     = trim($input['reply'] ?? '');
        $inputStatus   = !empty($input['status']) ? TicketStatus::normalize($input['status']) : ($ticketArray['status'] ?? TicketStatus::OPEN);
        $inputPriority = !empty($input['priority']) ? strtolower(trim($input['priority'])) : 'medium';
        $assignedTo    = !empty($input['assigned_to']) ? $input['assigned_to'] : ($ticketArray['pic_helpdesk_id'] ?? null);

        $now = date('Y-m-d H:i:s');
        $userId   = $sessionData['user_id'] ?? null;
        $username = $sessionData['username'] ?? 'Admin';

        // Hitung SLA Due Date jika prioritas ditentukan
        $sla = $this->slaModel->where('priority', $inputPriority)->first();
        $resHours = $sla ? (int) $sla['resolution_time'] : 24;
        $dueDate = date('Y-m-d H:i:s', strtotime("+{$resHours} hours", strtotime($ticketArray['created_date'] ?? $now)));

        // Simpan response record
        $this->responseModel->insert([
            'ticket_id'   => $ticketId,
            'user_id'     => $userId,
            'author_name' => $username,
            'status'      => $inputStatus,
            'priority'    => $inputPriority,
            'assigned_to' => $assignedTo,
            'reply'       => $replyText,
            'is_internal' => 0,
            'created_at'  => $now,
        ]);

        // Update ticket record
        $updateData = [
            'status'              => $inputStatus,
            'resolution_due_date' => $dueDate,
            'pic_helpdesk_id'     => $assignedTo,
            'modified_date'       => $now,
            'modified_by'         => $username,
        ];

        $this->ticketModel->update($ticketId, $updateData);

        // Kirim email notifikasi
        try {
            $assignedName = '-';
            if ($assignedTo) {
                $user = $this->userModel->find($assignedTo);
                if ($user) {
                    $assignedName = $user['name'] ?? '-';
                }
            }
            $this->emailService->sendTicketStatusNotification($ticketArray, $inputStatus, $replyText, $assignedName);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mengirim email notifikasi reply tiket: ' . $e->getMessage());
        }

        return $this->ticketModel->find($ticketId);
    }

    /**
     * Membantu filter query builder
     */
    protected function applyFiltersToBuilder($builder, array $filters, ?array $userContext = null): void
    {
        if ($userContext && empty($userContext['is_staff'])) {
            $empNo = $userContext['employee_no'] ?? '';
            $empName = $userContext['name'] ?? '';
            $userId = $userContext['user_id'] ?? '';

            $builder->groupStart()
                ->where('created_by', $empNo)
                ->orWhere('created_by', $empName)
                ->orWhere('reporter_id', $userId)
                ->groupEnd();
        }

        if (!empty($filters['start'])) {
            $builder->where('created_date >=', $filters['start'] . ' 00:00:00');
        }
        if (!empty($filters['end'])) {
            $builder->where('created_date <=', $filters['end'] . ' 23:59:59');
        }
        if (!empty($filters['priority'])) {
            $builder->where('sla_status', $filters['priority']);
        }
        if (!empty($filters['type'])) {
            $builder->where('request_type_id', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $builder->where('status', TicketStatus::normalize($filters['status']));
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('ticket_no', $search)
                ->orLike('title', $search)
                ->orLike('description', $search)
                ->orLike('reporter_email', $search)
                ->orLike('id', $search)
                ->groupEnd();
        }
    }

    /**
     * Normalisasi baris tiket agar kompatibel dengan tabel & view admin
     */
    protected function hydrateTicketList(array $rows): array
    {
        $hydrated = [];
        foreach ($rows as $row) {
            $row['ticket_status']   = $row['status'] ?? 'open';
            $row['ticket_priority'] = $row['sla_status'] ?? 'medium';
            $row['subject']         = $row['title'] ?? '-';
            $row['req_type']        = 'HC Helpdesk';
            $row['emp_name']        = $row['created_by'] ?? 'User';
            $row['emp_nip']         = $row['created_by'] ?? '-';
            $hydrated[] = $row;
        }
        return $hydrated;
    }

    /**
     * Dekripsi attachment tiket
     */
    protected function decryptAttachments(array $raw): array
    {
        $encrypter = \Config\Services::encrypter();
        $list = [];

        foreach ($raw as $att) {
            try {
                $fileName = $encrypter->decrypt(hex2bin($att['file_name']));
                $filePath = $encrypter->decrypt(hex2bin($att['file_path']));
            } catch (\Throwable $e) {
                $fileName = $att['file_name'] ?? '[Invalid]';
                $filePath = $att['file_path'] ?? '';
            }

            $list[] = [
                'file_name' => $fileName,
                'file_path' => $filePath,
            ];
        }

        return $list;
    }

    /**
     * Memformat array balasan/komentar
     */
    protected function hydrateReplies(array $repliesRaw, array $ticket): array
    {
        $replies = [];
        foreach ($repliesRaw as $r) {
            $isUser = (!empty($r['user_id']) && $r['user_id'] === ($ticket['reporter_id'] ?? ''))
                   || (!empty($r['author_name']) && $r['author_name'] === ($ticket['created_by'] ?? ''));

            $replies[] = [
                'is_user'    => $isUser,
                'author'     => $r['author_name'] ?? 'User',
                'created_at' => $r['created_at'] ?? $r['created_date'] ?? date('Y-m-d H:i:s'),
                'text'       => $r['reply'] ?? '',
            ];
        }
        return $replies;
    }
}
