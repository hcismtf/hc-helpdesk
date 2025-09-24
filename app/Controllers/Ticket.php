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
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
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

        $emp_id = $this->generateUUIDv4();
        $nip_asli = $this->request->getPost('emp_id');
        $encrypter = \Config\Services::encrypter();
        $nip_encrypted = bin2hex($encrypter->encrypt($nip_asli));

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
            'ticket_priority' => null,
            'due_date'      => null,
            'created_by'    => $this->request->getPost('emp_name'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $ticketId = $ticketModel->insert($data);

        // kalau ada file upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $maxSize = 1024 * 1024; // 1MB
            $newName = $file->getRandomName();
            // Jika file > 1MB dan tipe gambar, compress
            if ($file->getSize() > $maxSize && strpos($file->getMimeType(), 'image/') === 0) {
                $imageType = $file->getMimeType();
                $srcPath = $file->getTempName();
                $dstPath = 'D:/uploads/images/' . $newName;

                // Kompres gambar (JPEG/PNG)
                if ($imageType === 'image/jpeg') {
                    $image = imagecreatefromjpeg($srcPath);
                    imagejpeg($image, $dstPath, 70); // quality 70%
                    imagedestroy($image);
                } elseif ($imageType === 'image/png') {
                    $image = imagecreatefrompng($srcPath);
                    imagepng($image, $dstPath, 7); // compression level 0-9
                    imagedestroy($image);
                } else {
                    // Jika bukan jpeg/png, tetap move tanpa compress
                    $file->move('D:/uploads/images', $newName);
                }
            } else {
                // File <= 1MB atau bukan gambar, langsung move
                $file->move('D:/uploads/images', $newName);
            }

            // Enkripsi file_name dan file_path
            $file_name_encrypted = bin2hex($encrypter->encrypt($file->getClientName()));
            $file_path_encrypted = bin2hex($encrypter->encrypt($newName));
            $encrypter = \Config\Services::encrypter();
            $tiket_trx_id_encrypted = bin2hex($encrypter->encrypt($ticketId));

            $ticketAttModel->insert([
                'tiket_trx_id' => $tiket_trx_id_encrypted, 
                'file_name'    => $file_name_encrypted,
                'file_path'    => $file_path_encrypted,
                'created_by'   => $this->request->getPost('emp_name'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        }
        // Kirim email ke user
        $email = \Config\Services::email();
        $email->setTo($this->request->getPost('email'));
        $email->setSubject('Konfirmasi Pengajuan Ticket HC Helpdesk');

        $emp_name = $this->request->getPost('emp_name');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        $emailBody = "
        Dear {$emp_name},<br><br>
        Selamat!<br><br>
        Anda telah berhasil mengajukan ticket ke HC Helpdesk. Berikut detail tiket anda :<br>
        No tiket : {$ticketId}<br>
        Subject : {$subject}<br>
        Message : {$message}<br><br>
        Mohon di tunggu konfirmasi dari tim kami,<br><br>
        Terima kasih.<br><br>
        Hormat kami,<br>
        Human Capital Division
        ";

        $email->setMessage($emailBody);
        $email->setMailType('html');
        if (!$email->send()) {
            log_message('error', $email->printDebugger(['headers']));
        }  
        return view('components/success_confirm');
    }
}
