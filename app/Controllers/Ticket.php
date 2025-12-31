<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\TicketAttModel;
use CodeIgniter\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $ticket_att_id = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $maxSize = 1024 * 1024; // 1MB
            $newName = $file->getRandomName();

            // Tentukan folder tujuan (Gunakan FCPATH untuk folder public, atau WRITEPATH untuk folder writable)
            $uploadPath = FCPATH . 'uploads/images-attachment/'; 
            
            // PENTING: Cek apakah folder ada, jika tidak buat folder tersebut
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Jika file > 1MB dan tipe gambar, compress
            if ($file->getSize() > $maxSize && strpos($file->getMimeType(), 'image/') === 0) {
                $imageType = $file->getMimeType();
                $srcPath = $file->getTempName();
                // $dstPath = 'D:/uploads/images-attachment/' . $newName;
                $dstPath = $uploadPath . $newName;

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
                    $file->move($uploadPath, $newName);
                }
            } else {
                // File <= 1MB atau bukan gambar, langsung move
                $file->move($uploadPath, $newName);
            }

            // Enkripsi file_name dan file_path
            $file_name_encrypted = bin2hex($encrypter->encrypt($file->getClientName()));
            $file_path_encrypted = bin2hex($encrypter->encrypt($newName));

            $ticketAttModel->insert([
                'tiket_trx_id' => $ticketId,
                'file_name'    => $file_name_encrypted,
                'file_path'    => $file_path_encrypted,
                'created_by'   => $this->request->getPost('emp_name'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);

            $ticket_att_id = $ticketAttModel->getInsertID();
        }

        // Kirim email konfirmasi
        $emp_name = $this->request->getPost('emp_name');
        $email = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $this->sendTicketConfirmationEmail($emp_name, $email, $ticketId, $subject);

        return view('components/success_confirm');
    }

    /**
     * Kirim email konfirmasi ticket
     * 
     * @param string $emp_name Nama karyawan
     * @param string $email Email karyawan
     * @param string $ticketId ID ticket
     * @param string $subject Subject ticket
     * @return void
     */
    private function sendTicketConfirmationEmail($emp_name, $email, $ticketId, $subject)
    {
        if (empty($email)) {
            return;
        }

        require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/PHPMailer.php');
        require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/Exception.php');
        require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/SMTP.php');

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = getenv('email.SMTPHost') ?: 'smtp-mail.outlook.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('email.SMTPUser') ?: 'support@example.com';
            $mail->Password = getenv('email.SMTPPass') ?: '';
            $mail->SMTPSecure = getenv('email.SMTPCrypto') ?: 'tls';
            $mail->Port = getenv('email.SMTPPort') ?: 587;

            $mail->setFrom(getenv('email.fromEmail') ?: 'support@example.com', getenv('email.fromName') ?: 'HC Helpdesk');
            $mail->addAddress($email);

            $mail->Subject = "Ticket Confirmation - " . $subject;
            $mail->isHTML(true);

            $emailBody = "
                <html>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>Ticket Confirmation</h2>
                        
                        <p>Dear <strong>{$emp_name}</strong>,</p>
                        <p>Terima kasih telah mengajukan ticket. Kami telah menerima permintaan Anda dan akan segera ditangani oleh tim kami.</p>
                        
                        <div style='background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                            <p><strong>Ticket ID:</strong> {$ticketId}</p>
                            <p><strong>Subject:</strong> {$subject}</p>
                            <p><strong>Status:</strong> <span style='background-color: #fbbf24; color: #78350f; padding: 4px 8px; border-radius: 3px; display: inline-block;'>Open</span></p>
                            <p><strong>Submitted Date:</strong> " . date('d-m-Y H:i:s') . "</p>
                        </div>
                        
                        <p>Tim support kami akan meninjau dan memberikan update tentang ticket Anda segera.</p>
                        <p>Jika ada pertanyaan, silakan hubungi kami.</p>
                        <p> Anda juga dapat mengunjungi link berikut untuk menghubungi admin : <a href='#'></a></p>
                        
                        <p style='margin-top: 20px;'>Hormat kami,<br><strong>Human Capital Division</strong></p>
                    </div>
                </body>
                </html>
            ";

            $mail->Body = $emailBody;
            $mail->send();
            
            log_message('info', 'Ticket confirmation email sent to: ' . $email . ' for ticket ID: ' . $ticketId);
        } catch (Exception $e) {
            log_message('error', 'Failed to send ticket confirmation email: ' . $mail->ErrorInfo);
        }
    }
}
