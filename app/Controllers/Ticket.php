<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\TicketAttModel;
use CodeIgniter\Controller;

class Ticket extends Controller
{
    public function create()
    {
        // menampilkan form submit tiket
        return view('ticket_form');
    }

    public function store()
    {
        $ticketModel = new TicketModel();
        $ticketAttModel = new TicketAttModel();

        // ambil data dari form
        $data = [
            'emp_id'        => $this->request->getPost('emp_id'),
            'emp_name'      => $this->request->getPost('emp_name'),
            'email'         => $this->request->getPost('email'),
            'wa_no'         => $this->request->getPost('wa_no'),
            'req_type'      => $this->request->getPost('req_type'),
            'subject'       => $this->request->getPost('subject'),
            'message'       => $this->request->getPost('message'),
            'ticket_status' => 'open',
            'ticket_priority' => 'normal',
            'created_by'    => $this->request->getPost('emp_name'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        // insert ke tiket_trx
        $ticketId = $ticketModel->insert($data);

        // kalau ada file upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);

            $ticketAttModel->insert([
                'tiket_trx_id' => $ticketId,
                'file_name'    => $file->getClientName(),
                'file_path'    => $newName,
                'created_by'   => $this->request->getPost('emp_name'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        }

        return view('ticket_success');

    }
}
