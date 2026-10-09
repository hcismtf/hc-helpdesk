<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\FaqModel;
use App\Models\RequestTypeModel;
use App\Services\TicketService;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;
use Throwable;

/**
 * Controller Tiket Publik (Guest / Non-Authenticated)
 * Menangani form pembuatan tiket publik dan pelacakan tiket
 */
class TicketController extends BaseController
{
    protected TicketService $ticketService;
    protected RequestTypeModel $requestTypeModel;
    protected FaqModel $faqModel;

    public function __construct()
    {
        $this->ticketService = new TicketService();
        $this->requestTypeModel = new RequestTypeModel();
        $this->faqModel = new FaqModel();
    }

    /**
     * Menampilkan form pembuatan tiket publik
     */
    public function create()
    {
        $requestTypes = $this->requestTypeModel
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->findAll();

        $faqs = $this->faqModel
            ->orderBy('id', 'desc')
            ->findAll();

        return view('ticket_form', [
            'requestTypes' => $requestTypes,
            'faqs' => $faqs,
        ]);
    }

    /**
     * Menyimpan tiket baru publik via TicketService
     */
    public function store()
    {
        $isAjax = $this->request->isAJAX() || (bool) $this->request->getPost('is_ajax');

        // Validasi input: email & wa_no sekarang opsional (permit_empty)
        $rules = [
            'emp_id' => 'required|min_length[3]|max_length[50]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'wa_no' => 'permit_empty|min_length[8]|max_length[20]',
            'req_type' => 'required',
            'subject' => 'required|min_length[3]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = implode(' ', $errors);

            if ($isAjax) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'errors' => $errors,
                ]);
            }

            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        $payload = [
            'emp_id' => $this->request->getPost('emp_id'),
            'email' => $this->request->getPost('email'),
            'wa_no' => $this->request->getPost('wa_no'),
            'req_type' => $this->request->getPost('req_type'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'ticket_priority' => $this->request->getPost('ticket_priority') ?? 'medium',
        ];

        $file = $this->request->getFile('attachment');

        try {
            $ticket = $this->ticketService->createTicket($payload, $file);

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Tiket berhasil dibuat dengan nomor: #' . $ticket->getTicketNo(),
                    'ticket_no' => $ticket->getTicketNo(),
                    'ticket_id' => $ticket->getId(),
                ]);
            }

            return view('components/success_confirm');

        } catch (InvalidArgumentException $e) {
            if ($isAjax) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());

        } catch (Throwable $e) {
            log_message('error', '[Public\TicketController::store] ' . $e->getMessage());

            if ($isAjax) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi kendala pada sistem saat membuat tiket. Silakan coba beberapa saat lagi.',
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada server saat membuat tiket.');
        }
    }

    /**
     * Pelacakan tiket dari form tracking publik
     */
    public function track(?string $ticketNo = null)
    {
        $number = $ticketNo ?? $this->request->getGetPost('ticket_no');
        if (empty($number)) {
            return redirect()->to('/')->with('error', 'Nomor tiket tidak valid.');
        }

        $ticket = $this->ticketService->getTicketByIdentifier((string) $number);
        if (!$ticket) {
            throw PageNotFoundException::forPageNotFound('Tiket tidak ditemukan.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => $ticket->getId(),
                    'ticket_no' => $ticket->getTicketNo(),
                    'title' => $ticket->getTitle(),
                    'status' => $ticket->getStatus(),
                    'status_label' => $ticket->getStatusLabel(),
                    'response_due_date' => $ticket->getResponseDueDate(),
                    'resolution_due_date' => $ticket->getResolutionDueDate(),
                    'created_date' => $ticket->getCreatedDate(),
                ]
            ]);
        }

        return view('ticket_detail_public', [
            'ticket' => $ticket,
        ]);
    }
}
