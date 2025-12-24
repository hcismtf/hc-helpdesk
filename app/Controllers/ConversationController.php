<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConversationModel;
use App\Models\TicketModel;

class ConversationController extends BaseController
{
    public function __construct()
    {
        // Set timezone agar waktu chat sesuai
        date_default_timezone_set('Asia/Jakarta');
    }

    public function send_message()
    {
        // Ambil data dari form
        $ticketId = $this->request->getPost('ticket_id');
        $message  = $this->request->getPost('message');

        // Validasi Input
        if (empty($ticketId) || empty(trim($message))) {
            return redirect()->back()->with('error', 'Pesan tidak boleh kosong.');
        }

        // Load Model
        $chatModel   = new ConversationModel();
        $ticketModel = new TicketModel();

        // Validasi tiket
        $ticket = $ticketModel->find($ticketId);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        // Siapkan data untuk disimpan
        $data = [
            'ticket_trx_id'   => $ticketId,
            'message'         => $message,
            'reply_by'        => $ticket['emp_name'] ?? 'User',
            'ticket_status'   => $ticket['ticket_status'],
            'ticket_priority' => $ticket['ticket_priority'],
            'created_date'    => date('Y-m-d H:i:s'),
            'emp_name'        => $ticket['emp_name'] ?? '',
            'email'           => $ticket['email'] ?? '',
            'wa_no'           => $ticket['wa_no'] ?? '',
            'nip_encrypted'   => $ticket['nip_encrypted'] ?? '',
        ];

        // Simpan ke database
        $chatModel->insert($data);

        // Update waktu respons terakhir di tiket utama
        $ticketModel->update($ticketId, [
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        $uniqueCode = basename($ticket['monitoring_url']); 
        
        return redirect()->to('Ticket-detail/' . $uniqueCode);
    }
}