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
use App\Models\TicketAuditLogModel;
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
    protected TicketAuditLogModel $auditLogModel;
    protected TicketEmailService $emailService;
    protected TicketDetailService $detailService;

    public function __construct(
        ?TicketModel $ticketModel = null,
        ?TicketResponseModel $responseModel = null,
        ?TicketAttachmentModel $attachmentModel = null,
        ?UserModel $userModel = null,
        ?SlaModel $slaModel = null,
        ?RequestTypeModel $requestTypeModel = null,
        ?TicketAuditLogModel $auditLogModel = null,
        ?TicketEmailService $emailService = null,
        ?TicketDetailService $detailService = null
    ) {
        $this->ticketModel      = $ticketModel ?? new TicketModel();
        $this->responseModel    = $responseModel ?? new TicketResponseModel();
        $this->attachmentModel  = $attachmentModel ?? new TicketAttachmentModel();
        $this->userModel        = $userModel ?? new UserModel();
        $this->slaModel         = $slaModel ?? new SlaModel();
        $this->requestTypeModel = $requestTypeModel ?? new RequestTypeModel();
        $this->auditLogModel    = $auditLogModel ?? new TicketAuditLogModel();
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
     * Memperbarui status tiket (misal dari quick status dropdown atau Kanban board)
     */
    public function updateTicketStatus(string $ticketId, string $rawStatus, string $actorId, string $actorName, ?string $reasonNotes = null): array
    {
        $status = TicketStatus::normalize($rawStatus);
        if (!TicketStatus::isValid($status)) {
            throw new InvalidArgumentException('Status tiket tidak valid: ' . $rawStatus);
        }

        $ticket = $this->ticketModel->find($ticketId);
        if (!$ticket) {
            throw new RuntimeException('Tiket tidak ditemukan.');
        }

        $ticketArray = is_object($ticket) ? $ticket->toArray() : (array) $ticket;
        $oldStatus = $ticketArray['status'] ?? 'open';

        $now = date('Y-m-d H:i:s');
        $updateData = [
            'status'        => $status,
            'modified_date' => $now,
            'modified_by'   => $actorName,
        ];

        $this->ticketModel->update($ticketId, $updateData);

        // Catat Audit Trail ke tabel ticket_audit_log
        try {
            $this->auditLogModel->logChange(
                $ticketId,
                'change_status',
                'status',
                $oldStatus,
                $status,
                $reasonNotes ?: ('Status changed to ' . TicketStatus::getLabel($status)),
                $actorId,
                $actorName
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mencatat audit log: ' . $e->getMessage());
        }

        // Catat ke response timeline sebagai riwayat sistem
        try {
            $actionText = $actorName . ' changed status to ' . TicketStatus::getLabel($status);
            if (!empty($reasonNotes)) {
                $actionText .= ' (' . $reasonNotes . ')';
            }
            $this->responseModel->insert([
                'ticket_id'   => $ticketId,
                'user_id'     => $actorId,
                'author_name' => $actorName,
                'status'      => $status,
                'reply'       => $actionText,
                'is_internal' => 1,
                'created_by'  => $actorName,
                'created_date'=> $now,
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
     * Memperbarui SLA category & priority tiket via quick table dropdown
     */
    public function updateTicketSla(string $ticketId, string $slaInput, string $actorId, string $actorName): array
    {
        $ticket = $this->ticketModel->find($ticketId);
        if (!$ticket) {
            throw new RuntimeException('Tiket tidak ditemukan.');
        }

        $ticketArray = is_object($ticket) ? $ticket->toArray() : (array) $ticket;
        $now = date('Y-m-d H:i:s');
        $oldPriority = strtolower((string)($ticketArray['sla_status'] ?? 'medium'));

        // Cek apakah $slaInput merupakan SLA ID (UUID) atau nama priority (low/medium/high/urgent)
        $sla = $this->slaModel->find($slaInput);
        if (!$sla) {
            $sla = $this->slaModel->where('priority', ucfirst($slaInput))->first();
        }

        if ($sla) {
            $slaId = is_object($sla) ? ($sla->id ?? $slaInput) : ($sla['id'] ?? $slaInput);
            $newPriority = strtolower(is_object($sla) ? ($sla->priority ?? $slaInput) : ($sla['priority'] ?? $slaInput));
            $resHours = (int)(is_object($sla) ? ($sla->resolution_time ?? 24) : ($sla['resolution_time'] ?? 24));
        } else {
            $slaId = null;
            $newPriority = strtolower($slaInput);
            $resHours = 24;
        }

        $createdDate = $ticketArray['created_date'] ?? $now;
        $newDueDate = date('Y-m-d H:i:s', strtotime("+{$resHours} hours", strtotime($createdDate)));

        $updateData = [
            'sla_status'          => $newPriority,
            'resolution_due_date' => $newDueDate,
            'modified_date'       => $now,
            'modified_by'         => $actorName,
        ];
        if (!empty($slaId)) {
            $updateData['sla_id'] = $slaId;
        }

        $this->ticketModel->update($ticketId, $updateData);

        // Catat Audit Trail
        try {
            $this->auditLogModel->logChange(
                $ticketId,
                'change_sla',
                'sla_status',
                $oldPriority,
                $newPriority,
                'SLA updated to ' . ucfirst($newPriority),
                $actorId,
                $actorName
            );
        } catch (\Throwable $e) {}

        $secondsLeft = strtotime($newDueDate) - time();

        return [
            'status'         => 'success',
            'new_priority'   => ucfirst($newPriority),
            'sla_id'         => $slaId,
            'due_date'       => $newDueDate,
            'seconds_left'   => $secondsLeft,
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
        $ticketArray['ticket_status']   = $ticketArray['status'] ?? 'open';
        $ticketArray['ticket_priority'] = $ticketArray['sla_status'] ?? 'medium';
        $ticketArray['subject']         = $ticketArray['title'] ?? ($ticketArray['subject'] ?? '-');
        $ticketArray['message']         = $ticketArray['description'] ?? ($ticketArray['message'] ?? '');
        $ticketArray['assigned_to']     = $ticketArray['pic_helpdesk_id'] ?? ($ticketArray['pic_dev_ba_id'] ?? null);

        // Cari data reporter dari tabel users berdasarkan reporter_id atau created_by
        $reporterUser = null;
        if (!empty($ticketArray['reporter_id'])) {
            $reporterUser = $this->userModel->find($ticketArray['reporter_id']);
            if (!$reporterUser) {
                $reporterUser = $this->userModel->where('employee_no', $ticketArray['reporter_id'])->orWhere('username', $ticketArray['reporter_id'])->first();
            }
        }
        if (!$reporterUser && !empty($ticketArray['created_by'])) {
            $reporterUser = $this->userModel->where('employee_no', $ticketArray['created_by'])->orWhere('username', $ticketArray['created_by'])->orWhere('id', $ticketArray['created_by'])->first();
        }

        if ($reporterUser) {
            $uName = is_object($reporterUser) ? ($reporterUser->name ?? $reporterUser->getName()) : ($reporterUser['name'] ?? '');
            $uNip  = is_object($reporterUser) ? ($reporterUser->employee_no ?? $reporterUser->getEmployeeNo()) : ($reporterUser['employee_no'] ?? '');
            $uPos  = is_object($reporterUser) ? ($reporterUser->position ?? $reporterUser->getPosition()) : ($reporterUser['position'] ?? '');
            $uEmail= is_object($reporterUser) ? ($reporterUser->email ?? $reporterUser->getEmail()) : ($reporterUser['email'] ?? '');

            $ticketArray['emp_name']     = $uName;
            $ticketArray['reporter_name']= $uName;
            $ticketArray['emp_nip']      = $uNip ?: ($ticketArray['created_by'] ?? '-');
            $ticketArray['emp_position'] = $uPos ?: 'Employee Staff';
            if (empty($ticketArray['email'])) {
                $ticketArray['email'] = $uEmail ?: ($ticketArray['reporter_email'] ?? '-');
            }
        } else {
            $ticketArray['emp_name']     = $ticketArray['created_by'] ?? ($ticketArray['reporter_id'] ?? 'User');
            $ticketArray['reporter_name']= $ticketArray['emp_name'];
        }

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

        // Ambil daftar user aktif yang memiliki role_id untuk assignment dropdown
        $users = $this->userModel
            ->where('status', 'active')
            ->where('role_id IS NOT NULL')
            ->where('role_id !=', '')
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
        $repliesRaw = $this->responseModel->where('ticket_id', $ticketId)->orderBy('created_date', 'asc')->findAll();
        $replies = $this->hydrateReplies($repliesRaw, $ticketArray);

        // Build data SLA & metric dari TicketDetailService
        $detailData = $this->detailService->buildDetailViewData($ticketArray, $repliesRaw, $attachments, $this->userModel);

        if (!empty($detailData['assignedSpecialist']['id'])) {
            $ticketArray['assigned_to'] = $detailData['assignedSpecialist']['id'];
        }

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
        $isInternal    = !empty($input['is_internal']) ? 1 : 0;
        $inputStatus   = !empty($input['status']) ? TicketStatus::normalize($input['status']) : ($ticketArray['status'] ?? TicketStatus::OPEN);
        $inputPriority = !empty($input['priority']) ? strtolower(trim($input['priority'])) : strtolower($ticketArray['sla_status'] ?? 'medium');
        $assignedTo = !empty($input['pic_helpdesk_id']) 
            ? $input['pic_helpdesk_id'] 
            : (!empty($input['assigned_to']) ? $input['assigned_to'] : ($ticketArray['pic_helpdesk_id'] ?? null));
        $assignedDevBa = array_key_exists('pic_dev_ba_id', $input) ? ($input['pic_dev_ba_id'] ?: null) : ($ticketArray['pic_dev_ba_id'] ?? null);

        $now = date('Y-m-d H:i:s');
        $userId   = $sessionData['user_id'] ?? null;
        $username = $sessionData['username'] ?? 'Admin';

        // Hitung SLA Due Date jika prioritas ditentukan
        $sla = $this->slaModel->where('priority', $inputPriority)->first();
        $resHours = $sla ? (int) $sla['resolution_time'] : 24;
        $dueDate = date('Y-m-d H:i:s', strtotime("+{$resHours} hours", strtotime($ticketArray['created_date'] ?? $now)));

        // Audit check jika status / priority / assignedTo berubah
        $oldStatus = $ticketArray['status'] ?? TicketStatus::OPEN;
        $oldPriority = strtolower($ticketArray['sla_status'] ?? 'medium');
        $oldAssigned = $ticketArray['pic_helpdesk_id'] ?? null;
        $oldAssignedDevBa = $ticketArray['pic_dev_ba_id'] ?? null;

        if ($oldStatus !== $inputStatus) {
            try {
                $this->auditLogModel->logChange(
                    $ticketId,
                    'change_status',
                    'status',
                    $oldStatus,
                    $inputStatus,
                    'Status updated' . ($replyText ? ': ' . mb_substr($replyText, 0, 80) : ''),
                    $userId,
                    $username
                );
            } catch (\Throwable $e) {}
        }

        if ($oldPriority !== $inputPriority) {
            try {
                $this->auditLogModel->logChange(
                    $ticketId,
                    'change_priority',
                    'priority',
                    $oldPriority,
                    $inputPriority,
                    'Priority updated',
                    $userId,
                    $username
                );
            } catch (\Throwable $e) {}
        }

        if ($oldAssigned !== $assignedTo) {
            try {
                $oldSpecialistName = 'Unassigned';
                if (!empty($oldAssigned)) {
                    $oldU = $this->userModel->find($oldAssigned);
                    if ($oldU) {
                        $oldSpecialistName = is_object($oldU) ? ($oldU->name ?? $oldU->getName()) : ($oldU['name'] ?? 'Unassigned');
                    }
                }

                $newSpecialistName = 'Unassigned';
                if (!empty($assignedTo)) {
                    $newU = $this->userModel->find($assignedTo);
                    if ($newU) {
                        $newSpecialistName = is_object($newU) ? ($newU->name ?? $newU->getName()) : ($newU['name'] ?? 'Unassigned');
                    }
                }

                $this->auditLogModel->logChange(
                    $ticketId,
                    'change_assigned_to',
                    'pic_helpdesk',
                    $oldSpecialistName,
                    $newSpecialistName,
                    'PIC Helpdesk assigned to ' . $newSpecialistName,
                    $userId,
                    $username
                );
            } catch (\Throwable $e) {}
        }

        if ($oldAssignedDevBa !== $assignedDevBa) {
            try {
                $oldDevName = 'Unassigned';
                if (!empty($oldAssignedDevBa)) {
                    $oldU = $this->userModel->find($oldAssignedDevBa);
                    if ($oldU) {
                        $oldDevName = is_object($oldU) ? ($oldU->name ?? $oldU->getName()) : ($oldU['name'] ?? 'Unassigned');
                    }
                }

                $newDevName = 'Unassigned';
                if (!empty($assignedDevBa)) {
                    $newU = $this->userModel->find($assignedDevBa);
                    if ($newU) {
                        $newDevName = is_object($newU) ? ($newU->name ?? $newU->getName()) : ($newU['name'] ?? 'Unassigned');
                    }
                }

                $this->auditLogModel->logChange(
                    $ticketId,
                    'change_assigned_dev',
                    'pic_dev_ba',
                    $oldDevName,
                    $newDevName,
                    'PIC Developer/BA assigned to ' . $newDevName,
                    $userId,
                    $username
                );
            } catch (\Throwable $e) {}
        }

        // Simpan response record
        $this->responseModel->insert([
            'ticket_id'   => $ticketId,
            'user_id'     => $userId,
            'author_name' => $username,
            'status'      => $inputStatus,
            'priority'    => $inputPriority,
            'assigned_to' => $assignedTo,
            'reply'       => $replyText,
            'is_internal' => $isInternal,
            'created_by'  => $username,
            'created_date'=> $now,
        ]);

        // Update ticket record
        $updateData = [
            'status'              => $inputStatus,
            'resolution_due_date' => $dueDate,
            'pic_helpdesk_id'     => $assignedTo,
            'pic_dev_ba_id'       => $assignedDevBa,
            'modified_date'       => $now,
            'modified_by'         => $username,
        ];

        $this->ticketModel->update($ticketId, $updateData);

        // Kirim email notifikasi jika BUKAN internal note
        if (!$isInternal) {
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
        if (!empty($filters['scope']) && $filters['scope'] === 'my' && $userContext) {
            $staffUserId = $userContext['user_id'] ?? '';
            $staffEmpNo  = $userContext['employee_no'] ?? '';
            $builder->groupStart()
                ->where('pic_helpdesk_id', $staffUserId)
                ->orWhere('pic_dev_ba_id', $staffUserId)
                ->orWhere('pic_helpdesk_id', $staffEmpNo)
                ->orWhere('pic_dev_ba_id', $staffEmpNo)
                ->groupEnd();
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
        if (empty($rows)) {
            return [];
        }

        // 1. Ambil data user untuk mapping (ID & employee_no)
        $allUsers = $this->userModel->findAll();
        $userMap = [];
        $nipMap = [];
        foreach ($allUsers as $u) {
            $uId   = is_object($u) ? ($u->id ?? null) : ($u['id'] ?? null);
            $uName = is_object($u) ? ($u->name ?? '') : ($u['name'] ?? '');
            $uNip  = is_object($u) ? ($u->employee_no ?? '') : ($u['employee_no'] ?? '');
            if ($uId) {
                $userMap[$uId] = ['name' => $uName, 'nip' => $uNip];
            }
            if ($uNip) {
                $nipMap[$uNip] = ['name' => $uName, 'nip' => $uNip];
            }
        }

        // 2. Ambil data request category / sub category
        $catMap = [];
        try {
            $allCategories = $this->requestTypeModel->findAll();
            foreach ($allCategories as $cat) {
                $cId   = is_object($cat) ? ($cat->id ?? null) : ($cat['id'] ?? null);
                $cName = is_object($cat) ? ($cat->name ?? '') : ($cat['name'] ?? '');
                if ($cId) {
                    $catMap[$cId] = $cName;
                }
            }
        } catch (\Throwable $e) {}

        // 3. Ambil data SLA master untuk mapping priority
        $slaMap = [];
        try {
            $allSla = $this->slaModel->findAll();
            foreach ($allSla as $sla) {
                $sId = is_object($sla) ? ($sla->id ?? null) : ($sla['id'] ?? null);
                if ($sId) {
                    $slaMap[$sId] = is_object($sla) ? ($sla->priority ?? '') : ($sla['priority'] ?? '');
                }
            }
        } catch (\Throwable $e) {}

        $nowTs = time();
        $hydrated = [];
        foreach ($rows as $row) {
            $row['ticket_status']   = $row['status'] ?? 'open';
            $row['ticket_priority'] = $row['sla_status'] ?? 'medium';
            $row['subject']         = $row['title'] ?? '-';

            // Resolve Requester name & NIP
            $reporterId   = $row['reporter_id'] ?? null;
            $createdBy    = $row['created_by'] ?? null;
            $resolvedName = 'Karyawan';
            $resolvedNip  = $createdBy ?? '-';

            if (!empty($reporterId) && isset($userMap[$reporterId])) {
                $resolvedName = $userMap[$reporterId]['name'] ?: $resolvedName;
                $resolvedNip  = $userMap[$reporterId]['nip'] ?: $resolvedNip;
            } elseif (!empty($createdBy) && isset($nipMap[$createdBy])) {
                $resolvedName = $nipMap[$createdBy]['name'] ?: $resolvedName;
                $resolvedNip  = $createdBy;
            } elseif (!empty($createdBy)) {
                $resolvedName = $createdBy;
            }

            $row['emp_name'] = $resolvedName;
            $row['emp_nip']  = $resolvedNip;

            // Resolve Category Name
            $reqTypeId = $row['request_type_id'] ?? null;
            $row['req_type'] = (!empty($reqTypeId) && isset($catMap[$reqTypeId])) ? $catMap[$reqTypeId] : 'HC Helpdesk';

            // Resolve PIC Helpdesk
            $picHelpdeskId = $row['pic_helpdesk_id'] ?? null;
            $row['pic_helpdesk_name'] = (!empty($picHelpdeskId) && isset($userMap[$picHelpdeskId])) ? $userMap[$picHelpdeskId]['name'] : 'Unassigned';

            // Resolve PIC Developer / BA
            $picDevBaId = $row['pic_dev_ba_id'] ?? null;
            $row['pic_dev_ba_name'] = (!empty($picDevBaId) && isset($userMap[$picDevBaId])) ? $userMap[$picDevBaId]['name'] : 'Unassigned';

            // Resolve SLA Category
            $slaId = $row['sla_id'] ?? null;
            $row['sla_category'] = (!empty($slaId) && isset($slaMap[$slaId])) ? $slaMap[$slaId] : ucfirst((string)($row['sla_status'] ?? 'Medium'));

            // Calculate target due date & time left in seconds
            $dueDate = $row['resolution_due_date'] ?? $row['response_due_date'] ?? null;
            $row['due_date'] = $dueDate;

            $secondsLeft = null;
            if (!empty($dueDate)) {
                $dueTs = strtotime($dueDate);
                if ($dueTs !== false) {
                    $secondsLeft = $dueTs - $nowTs;
                }
            }
            $row['seconds_left'] = $secondsLeft;

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
