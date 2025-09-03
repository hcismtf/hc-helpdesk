<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\TicketAttModel;
use CodeIgniter\Controller;

class Ticket extends Controller
{

    private function generateUUIDv4()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    public function create()
    {
        $requestTypeModel = new \App\Models\RequestTypeModel();
        $requestTypes = $requestTypeModel->where('status', 'Active')->orderBy('name', 'asc')->findAll();
        $faqModel = new \App\Models\FaqModel();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll();

        return view('ticket_form', [
            'requestTypes' => $requestTypes,
            'faqs' => $faqs
        ]);
    }

    public function store()
    {
        $ticketModel = new TicketModel();
        $ticketAttModel = new TicketAttModel();

        // generate emp_id random UUID
        $emp_id = $this->generateUUIDv4();

        // ambil NIP asli dari form
        $nip_asli = $this->request->getPost('emp_id');

        // enkripsi NIP menggunakan encrypter bawaan CI4
        $encrypter = \Config\Services::encrypter();
        $nip_encrypted = bin2hex($encrypter->encrypt($nip_asli));
        

        // ambil data dari form
        $data = [
            
            'emp_id'        => $emp_id,
            'nip_encrypted' => $nip_encrypted,
            'emp_name'      => $this->request->getPost('emp_name'),
            'email'         => $this->request->getPost('email'),
            'wa_no'         => $this->request->getPost('wa_no'),
            'req_type'      => $this->request->getPost('req_type'),
            'subject'       => $this->request->getPost('subject'),
            'message'       => $this->request->getPost('message'),
            'ticket_status' => 'open',
            'ticket_priority' => null, // set null
            'due_date'      => null,   // set null
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
    public function faq()
    {
        $faqModel = new \App\Models\FaqModel();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll();
        return view('list_faq', ['faqs' => $faqs]);
    }
}
