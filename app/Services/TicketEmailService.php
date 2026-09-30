<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class TicketEmailService
{
    /**
     * Send email notification to the user who submitted the ticket.
     *
     * @param array $ticket
     * @param string $status
     * @param string $replyText
     * @param string|null $assignedName
     * @return bool
     */
    public function sendTicketStatusNotification(array $ticket, string $status, string $replyText, ?string $assignedName = '-'): bool
    {
        $toEmail = $ticket['email'] ?? '';
        if (empty($toEmail)) {
            return false;
        }

        $ticketId = $ticket['id'] ?? '';
        $empName = htmlspecialchars($ticket['emp_name'] ?? 'User');
        $reqType = htmlspecialchars($ticket['req_type'] ?? '-');
        $subject = htmlspecialchars($ticket['subject'] ?? '-');
        $replyEscaped = nl2br(htmlspecialchars($replyText));
        $uuid = $ticket['emp_id'] ?? '';
        $publicURL = base_url('ticket/detail/' . $uuid);

        if ($status === 'closed') {
            $emailSubject = "Ticket #" . $ticketId . " - HC Helpdesk [Closed]";
            $emailBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                        .content { line-height: 1.6; color: #333; }
                        .ticket-info { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
                        .ticket-info p { margin: 8px 0; }
                        .label { font-weight: bold; color: #555; }
                        .status-closed { color: #22c55e; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Ticket Closed - Resolution Complete</h2>
                        </div>
                        <div class='content'>
                            <p>Dear <strong>{$empName}</strong>,</p>
                            <p>Terima kasih telah menunggu. Tiket Anda telah <span class='status-closed'>SELESAI</span> dikerjakan.</p>
                            
                            <div class='ticket-info'>
                                <p><span class='label'>Ticket ID:</span> {$ticketId}</p>
                                <p><span class='label'>Request Type:</span> {$reqType}</p>
                                <p><span class='label'>Subject:</span> {$subject}</p>
                                <p><span class='label'>Status:</span> <span class='status-closed'>Closed</span></p>
                                <p><span class='label'>Admin Feedback:</span></p>
                                <p style='margin-left: 15px; font-style: italic;'>{$replyEscaped}</p>
                            </div>
                            
                            <p>Jika masih ada pertanyaan atau membutuhkan bantuan lebih lanjut, silakan hubungi kami melalui email atau submit ticket baru.</p>
                            
                            <br>
                            <p>Hormat kami,<br>
                            <strong>Human Capital Division</strong></p>
                        </div>
                    </div>
                </body>
                </html>
            ";
        } else {
            $emailSubject = "Ticket #" . $ticketId . " - HC Helpdesk [In Progress]";
            $emailBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                        .content { line-height: 1.6; color: #333; }
                        .ticket-info { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
                        .ticket-info p { margin: 8px 0; }
                        .label { font-weight: bold; color: #555; }
                        .status-progress { color: #0091ffff; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Ticket Update - In Progress</h2>
                        </div>
                        <div class='content'>
                            <p>Dear <strong>{$empName}</strong>,</p>
                            <p>Terima kasih telah mengajukan ticket di HC Helpdesk. Tiket Anda sedang dalam proses penanganan.</p>
                            
                            <div class='ticket-info'>
                                <p><span class='label'>Ticket ID:</span> {$ticketId}</p>
                                <p><span class='label'>Request Type:</span> {$reqType}</p>
                                <p><span class='label'>Subject:</span> {$subject}</p>
                                <p><span class='label'>Status:</span> <span class='status-progress'>In Progress</span></p>
                                <p><span class='label'>Assigned To:</span> {$assignedName}</p>
                                <p><span class='label'>Admin Update:</span></p>
                                <p style='margin-left: 15px; font-style: italic;'>{$replyEscaped}</p>
                            </div>
                            
                            <p>Tim support kami akan segera menyelesaikan request Anda. Mohon ditunggu untuk update berikutnya.</p>
                            <p>Anda juga dapat mengunjungi link berikut untuk memantau status atau menghubungi admin: <a href='{$publicURL}'>{$publicURL}</a></p>
                            
                            <br>
                            <p>Hormat kami,<br>
                            <strong>Human Capital Division</strong></p>
                        </div>
                    </div>
                </body>
                </html>
            ";
        }

        return $this->sendMail($toEmail, $emailSubject, $emailBody);
    }

    /**
     * Send email notification to admin when a user replies.
     *
     * @param array $ticket
     * @param array $adminUser
     * @param string $replyText
     * @return bool
     */
    public function sendUserReplyNotification(array $ticket, array $adminUser, string $replyText): bool
    {
        $adminEmail = $adminUser['email'] ?? '';
        if (empty($adminEmail)) {
            return false;
        }

        $ticketId = $ticket['id'] ?? '';
        $adminName = htmlspecialchars($adminUser['name'] ?? 'Admin');
        $empName = htmlspecialchars($ticket['emp_name'] ?? 'User');
        $reqType = htmlspecialchars($ticket['req_type'] ?? '-');
        $subject = htmlspecialchars($ticket['subject'] ?? '-');
        $status = htmlspecialchars($ticket['ticket_status'] ?? '-');
        $replyEscaped = nl2br(htmlspecialchars($replyText));

        $emailSubject = "Ticket Update - User Reply - Ticket #" . $ticketId;
        $emailBody = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                    .content { line-height: 1.6; color: #333; }
                    .ticket-info { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .ticket-info p { margin: 8px 0; }
                    .label { font-weight: bold; color: #555; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New User Reply</h2>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>{$adminName}</strong>,</p>
                        <p>User <strong>{$empName}</strong> telah mengirim balasan baru untuk ticket #{$ticketId}.</p>
                        
                        <div class='ticket-info'>
                            <p><span class='label'>Ticket ID:</span> {$ticketId}</p>
                            <p><span class='label'>Request Type:</span> {$reqType}</p>
                            <p><span class='label'>Subject:</span> {$subject}</p>
                            <p><span class='label'>Status:</span> {$status}</p>
                            <p><span class='label'>User Message:</span></p>
                            <p style='margin-left: 15px; font-style: italic;'>{$replyEscaped}</p>
                        </div>
                        
                        <p>Silakan login ke dashboard admin untuk merespon ticket ini.</p>
                        
                        <br>
                        <p>Hormat kami,<br>
                        <strong>System Notification</strong></p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->sendMail($adminEmail, $emailSubject, $emailBody);
    }

    /**
     * Send email via PHPMailer.
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $body
     * @return bool
     */
    protected function sendMail(string $toEmail, string $subject, string $body): bool
    {
        // Load PHPMailer files if not already loaded via Composer
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $phpMailerPath = ROOTPATH . 'vendor/phpmailer/phpmailer/src/';
            if (file_exists($phpMailerPath . 'PHPMailer.php')) {
                require_once($phpMailerPath . 'PHPMailer.php');
                require_once($phpMailerPath . 'Exception.php');
                require_once($phpMailerPath . 'SMTP.php');
            }
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = getenv('email.SMTPHost') ?: 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('email.SMTPUser') ?: 'support@example.com';
            $mail->Password = getenv('email.SMTPPass') ?: '';
            $mail->SMTPSecure = getenv('email.SMTPCrypto') ?: 'tls';
            $mail->Port = (int) (getenv('email.SMTPPort') ?: 587);

            $mail->setFrom(
                getenv('email.fromEmail') ?: 'support@example.com',
                getenv('email.fromName') ?: 'HC Helpdesk'
            );
            $mail->addAddress($toEmail);

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $body;

            $mail->send();
            log_message('info', 'Ticket notification email sent to: ' . $toEmail);
            return true;
        } catch (Exception $e) {
            log_message('error', 'Failed to send ticket email: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
