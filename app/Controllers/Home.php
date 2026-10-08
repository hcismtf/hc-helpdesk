<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('landing_page');
    }

    public function trackTicket()
    {
        $ticketNo = trim($this->request->getPost('ticket_no') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');

        if (empty($ticketNo)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Silakan masukkan nomor tiket.'
            ]);
        }

        $cleanNo = ltrim($ticketNo, '#');

        $ticketService = new \App\Services\TicketService();
        $ticket = $ticketService->getTicketByIdentifier($cleanNo);

        if (!$ticket) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tiket dengan nomor "' . esc($ticketNo) . '" tidak ditemukan.'
            ]);
        }

        // Cek filter email jika diisi
        if (!empty($email) && stripos((string) $ticket->getDescription(), $email) === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tiket ditemukan, namun email yang dimasukkan tidak sesuai dengan data pelapor.'
            ]);
        }

        $finishDate = null;
        if ($ticket->isClosed()) {
            $respModel = new \App\Models\TicketResponseModel();
            $finishDate = $respModel->getCompletedDate((string) $ticket->getId());
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'              => $ticket->getTicketNo() ?? $ticket->getId(),
                'subject'         => $ticket->getTitle() ?? 'Tiket Bantuan',
                'req_type'        => 'HC Helpdesk',
                'ticket_status'   => $ticket->getStatusLabel(),
                'ticket_priority' => 'Normal',
                'created_date'    => (string) ($ticket->getCreatedDate() ?? date('Y-m-d H:i:s')),
                'due_date'        => (string) ($ticket->getResolutionDueDate() ?? $ticket->getResponseDueDate() ?? 'Dalam Antrean'),
                'finish_date'     => $finishDate,
                'created_by'      => $ticket->getCreatedBy() ?? 'Karyawan'
            ]
        ]);
    }
}
