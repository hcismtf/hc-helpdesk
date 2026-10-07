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

        $ticketModel = new \App\Models\TicketTransactionModel();
        $builder = $ticketModel->builder();
        $builder->groupStart()
                ->where('id', $cleanNo)
                ->orLike('id', $cleanNo)
                ->groupEnd();

        if (!empty($email)) {
            $builder->where('email', $email);
        }

        $ticket = $builder->get()->getRowArray();

        if (!$ticket) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tiket dengan nomor "' . esc($ticketNo) . '" tidak ditemukan' . (!empty($email) ? ' atau email tidak sesuai.' : '.')
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'            => $ticket['id'],
                'subject'       => $ticket['subject'] ?? 'Tiket Bantuan',
                'req_type'      => $ticket['req_type'] ?? '-',
                'ticket_status' => $ticket['ticket_status'] ?? 'Open',
                'ticket_priority' => $ticket['ticket_priority'] ?? 'Normal',
                'created_date'  => $ticket['created_date'] ?? date('Y-m-d H:i:s'),
                'due_date'      => $ticket['due_date'] ?? null,
                'finish_date'   => $ticket['finish_date'] ?? null,
                'created_by'    => $ticket['created_by'] ?? ($ticket['emp_name'] ?? 'Karyawan')
            ]
        ]);
    }
}
