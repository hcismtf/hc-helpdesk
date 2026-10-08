<?php

namespace App\Services;

use App\Entities\TicketEntity;
use App\Entities\TicketStatus;
use App\Models\TicketModel;
use App\Models\TicketAttachmentModel;
use App\Models\RequestTypeModel;
use App\Models\SlaModel;
use App\Models\UserModel;
use App\Services\TicketEmailService;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Database;
use RuntimeException;
use InvalidArgumentException;

class TicketService
{
    protected TicketModel $ticketModel;
    protected TicketAttachmentModel $attachmentModel;
    protected RequestTypeModel $requestTypeModel;
    protected SlaModel $slaModel;
    protected UserModel $userModel;
    protected TicketEmailService $emailService;

    public function __construct(
        ?TicketModel $ticketModel = null,
        ?TicketAttachmentModel $attachmentModel = null,
        ?RequestTypeModel $requestTypeModel = null,
        ?SlaModel $slaModel = null,
        ?UserModel $userModel = null,
        ?TicketEmailService $emailService = null
    ) {
        $this->ticketModel      = $ticketModel ?? new TicketModel();
        $this->attachmentModel  = $attachmentModel ?? new TicketAttachmentModel();
        $this->requestTypeModel = $requestTypeModel ?? new RequestTypeModel();
        $this->slaModel         = $slaModel ?? new SlaModel();
        $this->userModel        = $userModel ?? new UserModel();
        $this->emailService     = $emailService ?? new TicketEmailService();
    }

    /**
     * Generate nomor tiket urut otomatis per tahun: HC-YYYY-XXXXX (e.g. HC-2026-00001)
     */
    public function generateTicketNo(string $prefix = 'HC'): string
    {
        $db = Database::connect();
        $year = date('Y');
        $codePattern = "{$prefix}-{$year}-%";

        $row = $db->query("
            SELECT ticket_no 
            FROM ticket 
            WHERE ticket_no LIKE ? 
            ORDER BY LENGTH(ticket_no) DESC, ticket_no DESC 
            LIMIT 1 
            FOR UPDATE
        ", [$codePattern])->getRow();

        if (!$row || empty($row->ticket_no)) {
            $nextSequence = 1;
        } else {
            $parts = explode('-', (string) $row->ticket_no);
            $lastSequence = (int) end($parts);
            $nextSequence = $lastSequence + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $nextSequence);
    }

    /**
     * Membuat tiket baru secara atomik (Database Transaction)
     *
     * @param array $payload
     * @param UploadedFile|null $file
     * @return TicketEntity
     * @throws InvalidArgumentException|RuntimeException
     */
    public function createTicket(array $payload, ?UploadedFile $file = null): TicketEntity
    {
        $empName  = trim($payload['emp_name'] ?? '');
        $empId    = trim($payload['emp_id'] ?? '');
        $email    = trim($payload['email'] ?? '');
        $waNo     = trim($payload['wa_no'] ?? '');
        $reqInput = trim($payload['req_type'] ?? '');
        $subject  = trim($payload['subject'] ?? '');
        $message  = trim($payload['message'] ?? '');
        $priority = strtolower(trim($payload['ticket_priority'] ?? 'medium'));

        // Validasi: nama, emp_id (NIP), req_type, subject wajib diisi. Email & WA bersifat opsional.
        if (empty($empName) || empty($empId) || empty($reqInput) || empty($subject)) {
            throw new InvalidArgumentException('Kolom Nama, NIP, Tipe Pengajuan, dan Subject wajib diisi.');
        }

        // 1. Resolve User / Reporter Relasi via Users table (berdasarkan employee_no / NIP)
        $user = $this->userModel->where('employee_no', $empId)->first();
        $reporterUserId = $user ? (string) ($user->getId() ?? $user['id'] ?? null) : null;

        // 2. Resolve Request Type ID
        $requestType = $this->resolveRequestType($reqInput);
        $requestTypeId = $requestType ? (string) $requestType['id'] : $this->findDefaultRequestTypeId();

        // 3. Resolve SLA & Target Due Dates
        $sla = $this->resolveSla($priority);
        $slaId = $sla ? (string) $sla['id'] : $this->findDefaultSlaId();

        $now = date('Y-m-d H:i:s');
        $responseDueDate = null;
        $resolutionDueDate = null;

        if ($sla) {
            $respHours = !empty($sla['response_time']) ? (int) $sla['response_time'] : null;
            $resHours  = !empty($sla['resolution_time']) ? (int) $sla['resolution_time'] : null;

            if ($respHours) {
                $responseDueDate = date('Y-m-d H:i:s', strtotime("+{$respHours} hours", strtotime($now)));
            }
            if ($resHours) {
                $resolutionDueDate = date('Y-m-d H:i:s', strtotime("+{$resHours} hours", strtotime($now)));
            }
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $ticketNo = $this->generateTicketNo('HC');

            $ticketEntity = new TicketEntity();
            $ticketEntity->setTicketNo($ticketNo);
            $ticketEntity->setRequestTypeId($requestTypeId);
            $ticketEntity->setSlaId($slaId);
            $ticketEntity->setTitle($subject);
            $ticketEntity->setDescription($message);
            $ticketEntity->setStatus(TicketStatus::OPEN);
            $ticketEntity->setCreatedBy($empId);

            // Reporter Data
            if ($reporterUserId) {
                $ticketEntity->setReporterId($reporterUserId);
            }
            if (!empty($email)) {
                $ticketEntity->setReporterEmail($email);
            }
            if (!empty($waNo)) {
                $ticketEntity->setReporterPhone($waNo);
            }

            if ($responseDueDate) {
                $ticketEntity->setResponseDueDate($responseDueDate);
            }
            if ($resolutionDueDate) {
                $ticketEntity->setResolutionDueDate($resolutionDueDate);
            }

            // Simpan tiket
            if (!$this->ticketModel->save($ticketEntity)) {
                $errors = $this->ticketModel->errors();
                throw new RuntimeException('Gagal menyimpan tiket: ' . implode(', ', $errors));
            }

            $ticketId = $ticketEntity->getId() ?? $this->ticketModel->getInsertID();

            // 4. Handle attachment file jika diunggah
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $this->saveTicketAttachment($ticketId, $file, $empId);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new RuntimeException('Transaksi database gagal dijalankan.');
            }

            $db->transCommit();

            // 5. Kirim konfirmasi email (Non-blocking)
            try {
                $this->sendConfirmationNotification($email, $empName, $ticketNo, $subject, $ticketId);
            } catch (\Throwable $e) {
                log_message('warning', 'Gagal mengirim email konfirmasi tiket ' . $ticketNo . ': ' . $e->getMessage());
            }

            // Ambil fresh instance
            $createdTicket = $this->ticketModel->find($ticketId);
            return $createdTicket instanceof TicketEntity ? $createdTicket : $ticketEntity;

        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Mengambil tiket berdasarkan UUID atau Ticket No
     */
    public function getTicketByIdentifier(string $identifier): ?TicketEntity
    {
        $clean = trim(ltrim($identifier, '#'));
        if (empty($clean)) {
            return null;
        }

        // Cek by ticket_no
        $ticket = $this->ticketModel->getByTicketNo($clean);
        if ($ticket) {
            return $ticket;
        }

        // Cek by primary key id (UUID)
        return $this->ticketModel->find($clean);
    }

    /**
     * Resolusi Request Type ID dari input name atau ID
     */
    protected function resolveRequestType(string $input): ?array
    {
        $type = $this->requestTypeModel->where('name', $input)->first();
        if ($type) {
            return $type;
        }

        $typeById = $this->requestTypeModel->find($input);
        if ($typeById) {
            return $typeById;
        }

        return $this->requestTypeModel->first();
    }

    /**
     * Fallback ID untuk Request Type
     */
    protected function findDefaultRequestTypeId(): string
    {
        $first = $this->requestTypeModel->first();
        return $first ? (string) $first['id'] : 'default-request-type';
    }

    /**
     * Resolusi SLA Configuration berdasarkan priority
     */
    protected function resolveSla(string $priority): ?array
    {
        $sla = $this->slaModel->where('priority', $priority)->first();
        if ($sla) {
            return $sla;
        }

        return $this->slaModel->first();
    }

    /**
     * Fallback ID untuk SLA
     */
    protected function findDefaultSlaId(): string
    {
        $first = $this->slaModel->first();
        return $first ? (string) $first['id'] : 'default-sla-id';
    }

    /**
     * Menyimpan attachment tiket secara aman
     */
    protected function saveTicketAttachment(string $ticketId, UploadedFile $file, string $actor): void
    {
        $uploadDir = FCPATH . 'uploads/images-attachment/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $randomName = $file->getRandomName();
        $mime = $file->getMimeType();
        $size = $file->getSize();
        $origName = $file->getClientName();

        // Optimasi kompresi gambar jika > 1MB
        if ($size > (1024 * 1024) && str_starts_with($mime, 'image/')) {
            $srcPath = $file->getTempName();
            $dstPath = $uploadDir . $randomName;

            if ($mime === 'image/jpeg') {
                $image = @imagecreatefromjpeg($srcPath);
                if ($image) {
                    imagejpeg($image, $dstPath, 75);
                    imagedestroy($image);
                } else {
                    $file->move($uploadDir, $randomName);
                }
            } elseif ($mime === 'image/png') {
                $image = @imagecreatefrompng($srcPath);
                if ($image) {
                    imagepng($image, $dstPath, 7);
                    imagedestroy($image);
                } else {
                    $file->move($uploadDir, $randomName);
                }
            } else {
                $file->move($uploadDir, $randomName);
            }
        } else {
            $file->move($uploadDir, $randomName);
        }

        // Enkripsi path & filename jika encrypter aktif
        try {
            $encrypter = \Config\Services::encrypter();
            $encryptedName = bin2hex($encrypter->encrypt($origName));
            $encryptedPath = bin2hex($encrypter->encrypt($randomName));
        } catch (\Throwable $e) {
            $encryptedName = $origName;
            $encryptedPath = $randomName;
        }

        $this->attachmentModel->insert([
            'ticket_id'    => $ticketId,
            'file_name'    => $encryptedName,
            'file_path'    => $encryptedPath,
            'file_size'    => $size,
            'file_type'    => $mime,
            'created_by'   => $actor,
            'created_date' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Kirim email konfirmasi tiket
     */
    protected function sendConfirmationNotification(string $toEmail, string $name, string $ticketNo, string $subject, string $ticketId): void
    {
        if (empty($toEmail)) {
            return;
        }

        $ticketData = [
            'id'        => $ticketId,
            'ticket_no' => $ticketNo,
            'email'     => $toEmail,
            'emp_name'  => $name,
            'subject'   => $subject,
            'req_type'  => 'General Request',
        ];

        $replyMessage = "Tiket bantuan Anda telah diterima oleh HC Helpdesk dengan Nomor: #{$ticketNo}. Tim kami akan segera menindaklanjuti kendala Anda.";
        $this->emailService->sendTicketStatusNotification($ticketData, TicketStatus::OPEN, $replyMessage);
    }
}
