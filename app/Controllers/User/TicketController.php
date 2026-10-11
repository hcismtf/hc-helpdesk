<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\TicketResponseModel;
use App\Models\TicketAttachmentModel;
use App\Models\RequestTypeModel;
use App\Models\SlaModel;
use App\Models\UserModel;
use App\Entities\TicketEntity;
use App\Entities\TicketStatus;
use App\Services\TicketService;
use App\Services\AttachmentService;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use Config\Database;
use Throwable;

/**
 * Controller Tiket Pengguna (Karyawan Terautentikasi)
 * Menampilkan rincian tiket secara dinamis dari database tanpa data hardcode.
 */
class TicketController extends BaseController
{
    protected TicketModel $ticketModel;
    protected TicketResponseModel $responseModel;
    protected TicketAttachmentModel $attachmentModel;
    protected RequestTypeModel $requestTypeModel;
    protected SlaModel $slaModel;
    protected UserModel $userModel;
    protected TicketService $ticketService;
    protected AttachmentService $attachmentService;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->responseModel = new TicketResponseModel();
        $this->attachmentModel = new TicketAttachmentModel();
        $this->requestTypeModel = new RequestTypeModel();
        $this->slaModel = new SlaModel();
        $this->userModel = new UserModel();
        $this->ticketService = new TicketService();
        $this->attachmentService = new AttachmentService($this->attachmentModel);
    }

    /**
     * Daftar tiket yang dibuat oleh karyawan yang sedang login
     */
    public function index()
    {
        $currentEmpNo = session('employee_no') ?? session('username') ?? '';
        $currentEmpName = session('name') ?? '';
        $currentUserId = session('user_id') ?? '';
        $currentUserEmail = session('email') ?? '';

        $tickets = $this->ticketModel
            ->groupStart()
            ->where('created_by', $currentEmpNo)
            ->orWhere('created_by', $currentEmpName)
            ->orWhere('reporter_id', $currentUserId)
            ->orWhere('reporter_email', $currentUserEmail)
            ->groupEnd()
            ->orderBy('created_date', 'DESC')
            ->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $tickets,
            ]);
        }

        return redirect()->to(base_url('dashboard'));
    }

    /**
     * Detail tiket milik karyawan (Data Murni Dinamis dari Database)
     *
     * @param string $id Ticket ID (UUID) atau Ticket No (e.g. HC-2026-00001)
     */
    public function show(string $id)
    {
        // 1. Ambil data tiket asli
        $ticket = $this->ticketService->getTicketByIdentifier($id);

        if (!$ticket) {
            throw PageNotFoundException::forPageNotFound('Tiket tidak ditemukan.');
        }

        // 2. Isolasi keamanan: Wajib login
        if (!session('isLoggedIn') || (!session('user_id') && !session('employee_no'))) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk mengakses tiket.');
        }

        // 3. Hak akses: Pemilik tiket atau Admin/Staff Helpdesk
        $currentEmpNo = (string) (session('employee_no') ?? session('username') ?? '');
        $currentEmpName = (string) (session('name') ?? '');
        $currentUserId = (string) (session('user_id') ?? '');
        $currentUserEmail = (string) (session('email') ?? '');
        $userRole = strtolower((string) (session('role') ?? ''));

        $isStaff = ($userRole === 'superadmin' || in_array('ticket:read', session('user_permissions') ?? [], true));
        $isOwner = (
            (!empty($currentEmpNo) && $ticket->getCreatedBy() === $currentEmpNo) ||
            (!empty($currentEmpName) && $ticket->getCreatedBy() === $currentEmpName) ||
            (!empty($currentUserId) && $ticket->getReporterId() === $currentUserId) ||
            (!empty($currentUserEmail) && $ticket->getReporterEmail() === $currentUserEmail)
        );

        if (!$isOwner && !$isStaff) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk melihat tiket karyawan lain.');
        }

        // 4. Resolve Reporter Information (Dinamis dari Users)
        $reporterUser = null;
        if (!empty($ticket->getReporterId())) {
            $reporterUser = $this->userModel->find($ticket->getReporterId());
        } elseif (!empty($ticket->getCreatedBy())) {
            $reporterUser = $this->userModel->where('employee_no', $ticket->getCreatedBy())->first();
        }

        $reporterName = $reporterUser ? ($reporterUser['name'] ?? $reporterUser->name ?? '') : ($ticket->getCreatedBy() ?: 'Karyawan');
        $reporterNik = $reporterUser ? ($reporterUser['employee_no'] ?? $reporterUser->employee_no ?? $ticket->getCreatedBy()) : ($ticket->getCreatedBy() ?: '');
        $reporterDept = $reporterUser ? ($reporterUser['position'] ?? $reporterUser->position ?? ($reporterUser['job_title'] ?? '')) : '';
        $reporterTitle = $reporterUser ? ($reporterUser['job_title'] ?? $reporterUser->job_title ?? '') : '';

        // 5. Resolve Request Type / Category Name (Dinamis dari request_type)
        $categoryName = 'Layanan HC';
        if (!empty($ticket->getRequestTypeId())) {
            $reqType = $this->requestTypeModel->find($ticket->getRequestTypeId());
            if ($reqType && !empty($reqType['name'])) {
                $categoryName = $reqType['name'];
            }
        }

        // 6. Resolve Status & Status Labels (Dinamis dari status tiket)
        $rawStatus = $ticket->getStatus() ?? 'open';
        $statusNorm = TicketStatus::normalize($rawStatus);
        $statusInfo = $this->resolveStatusDetails($statusNorm);

        // 7. Resolve Priority & SLA Configuration (Dinamis dari sla)
        $priority = 'normal';
        $slaRow = null;
        if (!empty($ticket->getSlaId())) {
            $slaRow = $this->slaModel->find($ticket->getSlaId());
            if ($slaRow && !empty($slaRow['priority'])) {
                $priority = strtolower($slaRow['priority']);
            }
        }

        // 8. SLA Countdown & Calculation (Dinamis 100% dari timestamps DB)
        $slaDetails = $this->calculateSlaDetails($ticket, $slaRow);

        // 9. Resolve PIC Details (Dinamis: Jika belum ditugaskan, tampilkan Belum Ditugaskan)
        $picDetails = $this->resolvePicDetails($ticket);

        // 10. Resolve Attachments (Murni dari ticket_attachment di database, tanpa mock)
        $rawAttachments = $this->attachmentModel->where('ticket_id', $ticket->getId())->findAll();
        $attachmentsData = $this->formatAttachments($rawAttachments);

        // 11. Resolve Conversation Responses & Timeline Events (Murni dari ticket_response DB)
        $rawResponses = $this->responseModel->where('ticket_id', $ticket->getId())
            ->where('is_internal', 0)
            ->orderBy('created_date', 'ASC')
            ->findAll();

        $discussionData = $this->formatDiscussion($ticket, $rawResponses, $reporterName, $picDetails);

        // 12. Related FAQs (Dinamis dari database FAQ jika ada)
        $relatedFaqs = $this->getRelatedFaqs();

        // 13. Stepper Stage Resolution (Dinamis dari status tiket & alokasi PIC)
        $stepperSteps = $this->resolveStepperSteps($ticket, $statusNorm, $discussionData['events'], $picDetails);

        // Timestamps dinamis
        $createdTime = !empty($ticket->getCreatedDate()) ? strtotime((string) $ticket->getCreatedDate()) : time();
        $modifiedTime = !empty($ticket->getModifiedDate()) ? strtotime((string) $ticket->getModifiedDate()) : $createdTime;

        $createdFormatted = $this->formatIndonesianDateTime($createdTime);
        $timeAgoUpdated = $this->formatTimeAgo($modifiedTime);

        $viewData = [
            'ticket' => $ticket,
            'ticketId' => $ticket->getId(),
            'ticketNo' => $ticket->getTicketNo() ?? 'HC-2026-00000',
            'ticketTitle' => $ticket->getTitle() ?: 'Tiket Bantuan',
            'ticketDesc' => $ticket->getDescription() ?: '',
            'categoryName' => $categoryName,
            'status' => $statusNorm,
            'statusLabel' => $statusInfo['label'],
            'statusClass' => $statusInfo['class'],
            'hasPulse' => $statusInfo['hasPulse'],
            'priority' => $priority,
            'priorityLabel' => ucfirst($priority),
            'reporter' => [
                'name' => $reporterName,
                'nik' => $reporterNik,
                'department' => $reporterDept,
                'title' => $reporterTitle,
                'email' => $ticket->getReporterEmail() ?? ($reporterUser['email'] ?? ''),
                'phone' => $ticket->getReporterPhone() ?? '',
            ],
            'createdFormatted' => $createdFormatted,
            'timeAgoUpdated' => $timeAgoUpdated,
            'sla' => $slaDetails,
            'pic' => $picDetails,
            'attachments' => $attachmentsData['initial'],
            'totalAttachmentCount' => count($attachmentsData['initial']),
            'totalAttachmentSize' => $attachmentsData['totalSizeFormatted'],
            'timelineEvents' => $discussionData['events'],
            'responses' => $discussionData['messages'],
            'historyCount' => count($discussionData['events']) + count($discussionData['messages']),
            'stepperSteps' => $stepperSteps,
            'relatedFaqs' => $relatedFaqs,
            'activePage' => 'my_tickets',
        ];

        return view('pages/user/ticket_detail', $viewData);
    }

    /**
     * Karyawan mengirim balasan/tanggapan konfirmasi ke tiket miliknya
     */
    public function reply(string $id)
    {
        if (!session('isLoggedIn') || (!session('user_id') && !session('employee_no'))) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Silakan masuk terlebih dahulu.']);
        }

        $ticket = $this->ticketService->getTicketByIdentifier($id);
        if (!$ticket) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Tiket tidak ditemukan.']);
        }

        $currentUserId = (string) (session('user_id') ?? '');
        $currentEmpNo = (string) (session('employee_no') ?? session('username') ?? '');
        $currentName = (string) (session('name') ?? $currentEmpNo);
        $currentUserEmail = (string) (session('email') ?? '');

        $isOwner = (
            (!empty($currentEmpNo) && $ticket->getCreatedBy() === $currentEmpNo) ||
            (!empty($currentName) && $ticket->getCreatedBy() === $currentName) ||
            (!empty($currentUserId) && $ticket->getReporterId() === $currentUserId) ||
            (!empty($currentUserEmail) && $ticket->getReporterEmail() === $currentUserEmail)
        );

        if (!$isOwner && strtolower((string) session('role')) !== 'superadmin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak: Anda tidak memiliki izin pada tiket ini.']);
        }

        $replyMessage = trim($this->request->getPost('reply') ?? '');
        $files = $this->request->getFiles();

        if (empty($replyMessage) && empty($files['attachments'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Pesan tanggapan atau lampiran berkas wajib diisi.']);
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            // Jika tiket berstatus ditutup/selesai dan dibalas, ubah menjadi reopened
            $newStatus = TicketStatus::IN_PROGRESS;
            if ($ticket->getStatus() === TicketStatus::CLOSED || $ticket->getStatus() === TicketStatus::RESOLVED) {
                $newStatus = TicketStatus::REOPENED;
            }

            // Simpan catatan respon
            $responseId = \App\Models\BaseModel::generateUuidV4();
            $now = date('Y-m-d H:i:s');

            $db->table('ticket_response')->insert([
                'id' => $responseId,
                'ticket_id' => $ticket->getId(),
                'user_id' => $currentUserId,
                'author_name' => $currentName,
                'reply' => $replyMessage ?: '(Mengunggah berkas lampiran pendukung)',
                'is_internal' => 0,
                'status' => $newStatus,
                'created_by' => $currentEmpNo ?: $currentUserId,
                'created_date' => $now,
            ]);

            // Tangani lampiran berkas jika ada
            $uploadedFiles = [];
            if (!empty($files['attachments'])) {
                $attachments = is_array($files['attachments']) ? $files['attachments'] : [$files['attachments']];
                foreach ($attachments as $file) {
                    if ($file && $file->isValid() && !$file->hasMoved()) {
                        $processed = $this->attachmentService->processUpload(
                            $ticket->getId(),
                            $file,
                            $currentEmpNo ?: $currentName,
                            $responseId
                        );
                        $uploadedFiles[] = $processed['original_name'];
                    }
                }
            }

            // Perbarui tiket
            $this->ticketModel->update($ticket->getId(), [
                'status' => $newStatus,
                'modified_by' => $currentEmpNo ?: $currentName,
                'modified_date' => $now,
            ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Tanggapan dan lampiran berhasil dikirim.',
                'new_status' => $newStatus,
                'uploaded_count' => count($uploadedFiles),
            ]);

        } catch (Throwable $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengirim balasan: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Karyawan menutup tiket mereka secara mandiri (Close Ticket)
     */
    public function close(string $id)
    {
        if (!session('isLoggedIn') || (!session('user_id') && !session('employee_no'))) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Silakan masuk terlebih dahulu.']);
        }

        $ticket = $this->ticketService->getTicketByIdentifier($id);
        if (!$ticket) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Tiket tidak ditemukan.']);
        }

        $currentUserId = (string) (session('user_id') ?? '');
        $currentEmpNo = (string) (session('employee_no') ?? session('username') ?? '');
        $currentName = (string) (session('name') ?? $currentEmpNo);

        $db = Database::connect();
        $db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');
            $responseId = \App\Models\BaseModel::generateUuidV4();

            $closeReason = trim($this->request->getPost('reason') ?? 'Tiket ditutup secara mandiri oleh pelapor.');

            $db->table('ticket_response')->insert([
                'id' => $responseId,
                'ticket_id' => $ticket->getId(),
                'user_id' => $currentUserId,
                'author_name' => $currentName,
                'reply' => 'Tiket telah ditutup oleh pelapor: ' . $closeReason,
                'is_internal' => 0,
                'status' => TicketStatus::CLOSED,
                'created_by' => $currentEmpNo ?: $currentUserId,
                'created_date' => $now,
            ]);

            $this->ticketModel->update($ticket->getId(), [
                'status' => TicketStatus::CLOSED,
                'modified_by' => $currentEmpNo ?: $currentName,
                'modified_date' => $now,
            ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Tiket berhasil ditutup.',
            ]);

        } catch (Throwable $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menutup tiket: ' . $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // PRIVATE / PROTECTED HELPER METHODS (Dinamis dari Data Asli)
    // =========================================================================

    protected function resolveStatusDetails(string $statusNorm): array
    {
        $map = [
            'pending_user' => [
                'label' => '• Butuh Respon Anda',
                'class' => 'tag-status-purple',
                'hasPulse' => true,
            ],
            TicketStatus::ON_HOLD => [
                'label' => '• Menunggu Konfirmasi',
                'class' => 'tag-status-purple',
                'hasPulse' => true,
            ],
            TicketStatus::OPEN => [
                'label' => '• Tiket Terbuka',
                'class' => 'tag-status-blue',
                'hasPulse' => false,
            ],
            TicketStatus::IN_PROGRESS => [
                'label' => '• Sedang Diproses',
                'class' => 'tag-status-blue',
                'hasPulse' => false,
            ],
            TicketStatus::RESOLVED => [
                'label' => '✓ Selesai',
                'class' => 'tag-status-green',
                'hasPulse' => false,
            ],
            TicketStatus::DONE => [
                'label' => '✓ Selesai',
                'class' => 'tag-status-green',
                'hasPulse' => false,
            ],
            TicketStatus::CLOSED => [
                'label' => '✓ Tiket Ditutup',
                'class' => 'tag-status-green',
                'hasPulse' => false,
            ],
            TicketStatus::REOPENED => [
                'label' => '• Dibuka Kembali',
                'class' => 'tag-status-purple',
                'hasPulse' => true,
            ],
        ];

        return $map[$statusNorm] ?? [
            'label' => '• ' . ucfirst($statusNorm),
            'class' => 'tag-status-blue',
            'hasPulse' => false,
        ];
    }

    protected function calculateSlaDetails(TicketEntity $ticket, ?array $slaRow): array
    {
        $now = time();
        $createdTs = !empty($ticket->getCreatedDate()) ? strtotime((string) $ticket->getCreatedDate()) : $now;
        $targetTimestamp = null;

        if ($ticket->getResolutionDueDate()) {
            $targetTimestamp = $ticket->getResolutionDueDate()->getTimestamp();
        } elseif ($slaRow && !empty($slaRow['resolution_time'])) {
            $targetTimestamp = $createdTs + ((int) $slaRow['resolution_time'] * 3600);
        } else {
            $targetTimestamp = $createdTs + (2 * 3600);
        }

        $totalSlaMinutes = max(1, round(($targetTimestamp - $createdTs) / 60));
        $commitmentHours = round($totalSlaMinutes / 60, 1);
        if ($commitmentHours == round($commitmentHours)) {
            $commitmentHours = (int) $commitmentHours;
        }

        $diffSeconds = $targetTimestamp - $now;
        $spentMins = max(0, round(($now - $createdTs) / 60));
        if ($spentMins > $totalSlaMinutes) {
            $spentMins = $totalSlaMinutes;
        }

        $isClosed = TicketStatus::isClosedOrResolved($ticket->getStatus() ?? '');

        if ($isClosed) {
            return [
                'target_label' => 'TARGET SLA PENYELESAIAN',
                'remaining_text' => 'SLA Terpenuhi',
                'remaining_badge' => 'SLA Tercapai',
                'commitment_hours' => $commitmentHours,
                'commitment_text' => "Komitmen SLA: {$commitmentHours} Jam",
                'total_minutes' => $totalSlaMinutes,
                'spent_minutes' => $spentMins,
                'remaining_minutes' => 0,
                'progress_label' => "{$spentMins} / {$totalSlaMinutes} Menit",
                'progress_percent' => 100,
            ];
        }

        if ($diffSeconds <= 0) {
            $overdueMins = abs(round($diffSeconds / 60));
            return [
                'target_label' => 'TARGET SLA PENYELESAIAN',
                'remaining_text' => "Terlambat {$overdueMins} Menit",
                'remaining_badge' => "SLA Terlewat",
                'commitment_hours' => $commitmentHours,
                'commitment_text' => "Komitmen SLA: {$commitmentHours} Jam",
                'total_minutes' => $totalSlaMinutes,
                'spent_minutes' => $totalSlaMinutes,
                'remaining_minutes' => 0,
                'progress_label' => "{$totalSlaMinutes} / {$totalSlaMinutes} Menit",
                'progress_percent' => 100,
            ];
        }

        $remainingMins = round($diffSeconds / 60);
        $percent = min(100, max(0, round(($spentMins / $totalSlaMinutes) * 100)));
        $targetTimeStr = date('H:i', $targetTimestamp) . ' WIB';

        return [
            'target_label' => 'TARGET SLA PENYELESAIAN',
            'remaining_text' => "Sisa {$remainingMins} Menit (Batas: {$targetTimeStr})",
            'remaining_badge' => "SLA {$remainingMins}m Tersisa",
            'commitment_hours' => $commitmentHours,
            'commitment_text' => "Komitmen SLA: {$commitmentHours} Jam",
            'total_minutes' => $totalSlaMinutes,
            'spent_minutes' => $spentMins,
            'remaining_minutes' => $remainingMins,
            'progress_label' => "{$spentMins} / {$totalSlaMinutes} Menit",
            'progress_percent' => $percent,
        ];
    }

    protected function resolvePicDetails(TicketEntity $ticket): array
    {
        $picId = $ticket->getPicHelpdeskId() ?: $ticket->getPicDevBaId();
        $picUser = null;

        if (!empty($picId)) {
            $picUser = $this->userModel->find($picId);
        }

        if ($picUser) {
            $name = $picUser['name'] ?? $picUser->name ?? '';
            $email = $picUser['email'] ?? $picUser->email ?? '';
            $role = $picUser['job_title'] ?? $picUser->job_title ?? ($picUser['position'] ?? 'HC Specialist');
            $dept = $picUser['position'] ?? $picUser->position ?? 'Divisi Human Capital';

            $words = explode(' ', trim($name));
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

            return [
                'is_assigned' => true,
                'name' => $name,
                'role' => $role,
                'dept' => $dept,
                'email' => $email,
                'initials' => $initials,
                'is_online' => true,
                'teams_url' => !empty($email) ? 'https://teams.microsoft.com/l/chat/0/0?users=' . urlencode($email) : '',
            ];
        }

        // Jika tiket belum dialokasikan ke PIC
        return [
            'is_assigned' => false,
            'name' => 'Belum Ditugaskan',
            'role' => 'Menunggu Alokasi PIC',
            'dept' => 'Tim Helpdesk Human Capital',
            'email' => '',
            'initials' => 'HC',
            'is_online' => false,
            'teams_url' => '',
        ];
    }

    protected function formatAttachments(array $rawAttachments): array
    {
        $encrypter = Services::encrypter();
        $formatted = [];
        $totalBytes = 0;

        foreach ($rawAttachments as $att) {
            $rawName = $att->getFileName();
            $rawPath = $att->getFilePath();

            $decName = $att->getDecryptedFileName();
            if (empty($decName)) {
                $decName = 'lampiran_dokumen.bin';
            }

            $decPath = $rawPath;
            try {
                if (ctype_xdigit($rawPath) && strlen($rawPath) % 2 === 0) {
                    $dec = $encrypter->decrypt(hex2bin($rawPath));
                    if ($dec !== false)
                        $decPath = (string) $dec;
                }
            } catch (Throwable $e) {
            }

            $size = $att->getFileSize() ?? 0;
            $totalBytes += $size;

            $ext = strtolower(pathinfo($decName, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true);
            $isPdf = ($ext === 'pdf');

            $typeLabel = match ($ext) {
                'png', 'jpg', 'jpeg', 'webp' => 'Gambar Tangkapan Layar',
                'txt', 'log' => 'Berkas Log Sistem',
                'pdf' => 'Dokumen PDF',
                'xlsx', 'xls', 'csv' => 'Lembar Kerja Excel',
                'docx', 'doc' => 'Dokumen Word',
                default => 'Berkas Pendukung',
            };

            $cleanRoutePath = str_replace('\\', '/', $decPath);
            $cleanRoutePath = ltrim($cleanRoutePath, '/');

            $formatted[] = [
                'id' => $att->getId(),
                'response_id' => $att->getResponseId(),
                'file_name' => $decName,
                'file_path' => $cleanRoutePath,
                'file_size' => $size,
                'file_size_formatted' => $this->formatBytes($size),
                'file_type_label' => $typeLabel,
                'is_image' => $isImage,
                'is_pdf' => $isPdf,
                'ext' => $ext,
                'preview_url' => base_url('attachment/view/' . $cleanRoutePath),
                'download_url' => base_url('attachment/download/' . $cleanRoutePath),
            ];
        }

        // Pisahkan initial ticket attachments (response_id is null/empty)
        $initial = array_filter($formatted, fn($a) => empty($a['response_id']));

        return [
            'initial' => array_values($initial),
            'all' => $formatted,
            'totalSizeFormatted' => $this->formatBytes($totalBytes),
        ];
    }

    protected function formatDiscussion(TicketEntity $ticket, array $rawResponses, string $reporterName, array $pic): array
    {
        $events = [];
        $messages = [];

        $createdTs = !empty($ticket->getCreatedDate()) ? strtotime((string) $ticket->getCreatedDate()) : time();
        $createdTimeStr = date('H:i', $createdTs) . ' WIB';

        // Event 1: Pelapor membuat tiket (Dinamis dari created_date & reporterName)
        $events[] = [
            'text' => "<strong>" . esc($reporterName) . "</strong> membuat tiket pengajuan.",
            'time' => $createdTimeStr,
            'ts'   => $createdTs,
        ];

        // Ambil riwayat audit logs (perubahan status & assignment PIC)
        try {
            $auditModel = new \App\Models\TicketAuditLogModel();
            $auditLogs = $auditModel->where('ticket_id', $ticket->getId())->orderBy('created_date', 'ASC')->findAll();

            if (!empty($auditLogs)) {
                foreach ($auditLogs as $al) {
                    $field = is_object($al) ? (string) $al->getFieldName() : ($al['field_name'] ?? '');
                    $action = is_object($al) ? (string) $al->getAction() : ($al['action'] ?? '');
                    $newV = is_object($al) ? (string) $al->getNewValue() : ($al['new_value'] ?? '');
                    $alDate = is_object($al) ? (string) $al->getCreatedDate() : ($al['created_date'] ?? '');
                    $alTs = !empty($alDate) ? strtotime($alDate) : time();
                    $alTimeStr = date('H:i', $alTs) . ' WIB';

                    if (in_array(strtolower($field), ['assigned_to', 'specialist', 'pic', 'assignee']) || $action === 'change_assigned_to') {
                        $events[] = [
                            'text' => "Tiket dialihkan / ditugaskan ke PIC Spesialis: <strong>" . esc($newV ?: 'Spesialis HC') . "</strong>.",
                            'time' => $alTimeStr,
                            'ts'   => $alTs,
                        ];
                    } elseif (strtolower($field) === 'status' || $action === 'change_status') {
                        $label = \App\Entities\TicketStatus::getLabel($newV);
                        $events[] = [
                            'text' => "Status tiket diperbarui menjadi: <strong>" . esc($label) . "</strong>.",
                            'time' => $alTimeStr,
                            'ts'   => $alTs,
                        ];
                    }
                }
            } elseif (!empty($pic['is_assigned'])) {
                // Fallback jika belum ada audit log tetapi PIC sudah diset di tiket
                $events[] = [
                    'text' => "Tiket ditugaskan ke PIC Spesialis: <strong>" . esc($pic['name']) . "</strong> (" . esc($pic['dept']) . ").",
                    'time' => $createdTimeStr,
                    'ts'   => $createdTs,
                ];
            }
        } catch (\Throwable $e) {
            if (!empty($pic['is_assigned'])) {
                $events[] = [
                    'text' => "Tiket ditugaskan ke PIC Spesialis: <strong>" . esc($pic['name']) . "</strong> (" . esc($pic['dept']) . ").",
                    'time' => $createdTimeStr,
                    'ts'   => $createdTs,
                ];
            }
        }

        // Urutkan seluruh events kronologis
        usort($events, fn($a, $b) => ($a['ts'] ?? 0) <=> ($b['ts'] ?? 0));

        // Tanggapan HANYA DARI DATA ASLI ticket_response (Tanpa mock text)
        foreach ($rawResponses as $r) {
            $author = is_object($r) ? ($r->getAuthorName() ?? $r->author_name) : ($r['author_name'] ?? '');
            $replyText = is_object($r) ? ($r->getReply() ?? $r->reply) : ($r['reply'] ?? '');
            $rDate = is_object($r) ? ($r->getCreatedDate() ?? $r->created_date) : ($r['created_date'] ?? '');
            $rTs = !empty($rDate) ? strtotime((string) $rDate) : time();

            $isStaff = ($pic['is_assigned'] && $author === $pic['name']) ||
                stripos($author, 'IT') !== false ||
                stripos($author, 'Admin') !== false ||
                stripos($author, 'Officer') !== false ||
                stripos($author, 'Helpdesk') !== false;

            $messages[] = [
                'id' => is_object($r) ? $r->getId() : ($r['id'] ?? ''),
                'author_name' => $author,
                'is_staff' => $isStaff,
                'role_label' => $isStaff ? 'HC Officer' : 'Pelapor',
                'job_title' => $isStaff ? ($pic['role'] ?: 'HC Specialist') : 'Karyawan',
                'time_ago' => $this->formatTimeAgo($rTs),
                'time_formatted' => date('H:i', $rTs) . ' WIB',
                'reply' => $replyText,
                'callout' => null,
            ];
        }

        return [
            'events' => $events,
            'messages' => $messages,
        ];
    }

    protected function resolveStepperSteps(TicketEntity $ticket, string $statusNorm, array $events, array $pic): array
    {
        $createdTs = !empty($ticket->getCreatedDate()) ? strtotime((string) $ticket->getCreatedDate()) : time();
        $date1 = date('d M Y, H:i', $createdTs) . ' WIB • Sistem';

        $isClosed = TicketStatus::isClosedOrResolved($statusNorm);
        $isResolved = ($statusNorm === TicketStatus::RESOLVED || $statusNorm === TicketStatus::DONE);
        $isOnHold = ($statusNorm === TicketStatus::ON_HOLD || $statusNorm === 'pending_user');
        $isInProg = ($statusNorm === TicketStatus::IN_PROGRESS);

        // Tahap 1: Tiket Diajukan
        $step1 = [
            'step' => 1,
            'title' => 'Tiket Diajukan',
            'subtext' => $date1,
            'status' => 'completed',
            'icon' => 'fa-check',
        ];

        // Tahap 2: Penugasan & Pemeriksaan Tim IT
        $step2Sub = 'Dalam antrean alokasi PIC';
        $step2Status = 'pending';

        if (!empty($pic['is_assigned'])) {
            $step2Sub = 'Ditangani oleh: ' . $pic['name'];
            $step2Status = ($isInProg || $isOnHold || $isResolved || $isClosed) ? 'completed' : 'active';
        } elseif ($isInProg || $isOnHold || $isResolved || $isClosed) {
            $step2Sub = 'Sedang ditindaklanjuti tim HC';
            $step2Status = 'completed';
        } elseif ($statusNorm === TicketStatus::OPEN) {
            $step2Status = 'active';
        }

        $step2 = [
            'step' => 2,
            'title' => 'Diproses oleh Tim HC',
            'subtext' => $step2Sub,
            'status' => $step2Status,
            'icon' => ($step2Status === 'completed') ? 'fa-check' : 'fa-clock',
        ];

        // Tahap 3: Tindak Lanjut & Respon
        $step3Title = $isOnHold ? 'Menunggu Respon Karyawan' : 'Tindak Lanjut Penanganan';
        $step3Sub = $isOnHold ? 'Konfirmasi tanggapan Anda diperlukan' : ($isInProg ? 'Pemeriksaan teknis berjalan' : 'Menunggu antrean');
        $step3Status = 'pending';

        if ($isClosed || $isResolved) {
            $step3Sub = 'Pemeriksaan selesai';
            $step3Status = 'completed';
        } elseif ($isOnHold || $isInProg) {
            $step3Status = 'active';
        }

        $step3 = [
            'step' => 3,
            'title' => $step3Title,
            'subtext' => $step3Sub,
            'status' => $step3Status,
            'icon' => ($step3Status === 'completed') ? 'fa-check' : 'fa-hourglass-half',
        ];

        // Tahap 4: Solusi & Verifikasi
        $step4Status = ($isClosed || $isResolved) ? 'completed' : 'pending';
        $step4Sub = ($isClosed || $isResolved) ? 'Solusi perbaikan telah diterapkan' : 'Penerapan solusi kendala';

        $step4 = [
            'step' => 4,
            'title' => 'Solusi & Verifikasi',
            'subtext' => $step4Sub,
            'status' => $step4Status,
            'icon' => ($step4Status === 'completed') ? 'fa-check' : '4',
        ];

        // Tahap 5: Tiket Ditutup
        $step5Status = $isClosed ? 'completed' : ($isResolved ? 'active' : 'pending');
        $step5Sub = $isClosed ? 'Tiket telah selesai dan ditutup' : ($isResolved ? 'Menunggu konfirmasi penutupan' : 'Penutupan tiket');

        $step5 = [
            'step' => 5,
            'title' => 'Tiket Selesai & Ditutup',
            'subtext' => $step5Sub,
            'status' => $step5Status,
            'icon' => ($step5Status === 'completed') ? 'fa-check' : '5',
        ];

        return [$step1, $step2, $step3, $step4, $step5];
    }

    protected function getRelatedFaqs(): array
    {
        $db = Database::connect();
        $faqs = $db->table('faq_detail')
            ->select('id, question, answer')
            ->limit(2)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($faqs as $f) {
            $result[] = [
                'title' => $f['question'],
                'desc' => mb_strimwidth(strip_tags($f['answer']), 0, 70, '...'),
                'url' => base_url('faq'),
            ];
        }

        if (empty($result)) {
            $result = [
                [
                    'title' => 'Panduan Bantuan HC Helpdesk',
                    'desc' => 'Pelajari tata cara pengajuan tiket dan tracking status kendala.',
                    'url' => base_url('faq'),
                ],
            ];
        }

        return $result;
    }

    protected function formatIndonesianDateTime(int $timestamp): string
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        $day = date('d', $timestamp);
        $month = $months[(int) date('m', $timestamp)] ?? date('M', $timestamp);
        $year = date('Y', $timestamp);
        $time = date('H:i', $timestamp);

        return "{$day} {$month} {$year}, {$time} WIB";
    }

    protected function formatTimeAgo(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $mins = max(1, round($diff / 60));
            return "{$mins} menit lalu";
        } elseif ($diff < 86400) {
            $hours = round($diff / 3600);
            return "{$hours} jam lalu";
        } else {
            $days = round($diff / 86400);
            return "{$days} hari lalu";
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024) . ' KB';
        }
        return $bytes . ' B';
    }
}
