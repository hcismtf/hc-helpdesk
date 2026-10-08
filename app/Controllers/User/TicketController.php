<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\TicketResponseModel;
use App\Models\TicketAttachmentModel;
use App\Entities\TicketEntity;
use App\Entities\TicketStatus;
use App\Services\TicketService;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Controller Tiket Pengguna (Karyawan Terautentikasi)
 * Menampilkan daftar tiket saya, melihat detail tiket, dan mengirim balasan/pesan tindak lanjut
 */
class TicketController extends BaseController
{
    protected TicketModel $ticketModel;
    protected TicketResponseModel $responseModel;
    protected TicketAttachmentModel $attachmentModel;
    protected TicketService $ticketService;

    public function __construct()
    {
        $this->ticketModel       = new TicketModel();
        $this->responseModel     = new TicketResponseModel();
        $this->attachmentModel   = new TicketAttachmentModel();
        $this->ticketService     = new TicketService();
    }

    /**
     * Daftar tiket yang dibuat oleh karyawan yang sedang login
     */
    public function index()
    {
        $currentEmpNo = session('employee_no') ?? session('username') ?? '';
        $currentEmpName = session('name') ?? '';

        $tickets = $this->ticketModel
            ->groupStart()
                ->where('created_by', $currentEmpNo)
                ->orWhere('created_by', $currentEmpName)
            ->groupEnd()
            ->orderBy('created_date', 'DESC')
            ->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $tickets,
            ]);
        }

        return view('user/ticket_list', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Detail tiket milik karyawan
     */
    public function show(string $id)
    {
        $ticket = $this->ticketService->getTicketByIdentifier($id);

        if (!$ticket) {
            throw PageNotFoundException::forPageNotFound('Tiket tidak ditemukan.');
        }

        // Isolasi keamanan: Karyawan hanya boleh melihat tiket miliknya sendiri
        $currentEmpNo   = session('employee_no') ?? session('username') ?? '';
        $currentEmpName = session('name') ?? '';

        $isOwner = (
            $ticket->getCreatedBy() === $currentEmpNo ||
            $ticket->getCreatedBy() === $currentEmpName
        );

        if (!$isOwner) {
            return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk melihat tiket karyawan lain.');
        }

        $responses = $this->responseModel->getResponsesByTicketId($ticket->getId());
        $attachments = $this->attachmentModel->getAttachmentsByTicketId($ticket->getId());

        return view('user/ticket_detail', [
            'ticket'      => $ticket,
            'responses'   => $responses,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Karyawan mengirim balasan/update ke tiket miliknya
     */
    public function reply(string $id)
    {
        $ticket = $this->ticketService->getTicketByIdentifier($id);
        if (!$ticket) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Tiket tidak ditemukan.']);
        }

        $currentUserId = session('user_id') ?? '';
        $currentEmpNo  = session('employee_no') ?? session('username') ?? '';
        $currentName   = session('name') ?? $currentEmpNo;

        $isOwner = (
            $ticket->getCreatedBy() === $currentEmpNo ||
            $ticket->getCreatedBy() === $currentName
        );

        if (!$isOwner) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $replyMessage = trim($this->request->getPost('reply') ?? '');
        if (empty($replyMessage)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Pesan balasan tidak boleh kosong.']);
        }

        $isReopen = (bool) $this->request->getPost('reopen');
        $status = $isReopen ? TicketStatus::REOPENED : $ticket->getStatus();

        $this->responseModel->insert([
            'ticket_id'   => $ticket->getId(),
            'user_id'     => $currentUserId,
            'author_name' => $currentName,
            'reply'       => $replyMessage,
            'status'      => $status,
            'is_internal' => 0,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Balasan berhasil dikirim.',
        ]);
    }
}
