<?php
/**
 * Ticket Detail Admin Orchestrator View
 *
 * Mengikuti arsitektur Anti-Monolith & Clean Views (rule.md).
 * Bertindak sebagai orchestrator tata letak modular dengan memanggil view partials ber-prefix '_'.
 *
 * @var array       $ticket              Data tiket utama
 * @var array       $users               Daftar staf dan admin
 * @var array       $replies             Daftar tanggapan balasan
 * @var array       $activities          Daftar timeline aktivitas gabungan
 * @var array       $attachments         Daftar file lampiran
 * @var array       $assignedSpecialist  Data specialist yang ditugaskan
 * @var array       $requester           Data profil pemohon tiket
 * @var array       $lifecycle           Data siklus waktu tiket
 * @var array       $sla                 Data metrik kepatuhan SLA
 * @var int         $commentsCount       Jumlah balasan
 * @var int         $auditCount          Jumlah log audit
 */

$uniqueUrl = basename(current_url());
$statusKey = strtolower($ticket['ticket_status'] ?? ($ticket['status'] ?? 'open'));

// Sinkronisasi status definitions dengan Domain Entity TicketStatus
$rawDefinitions = \App\Entities\TicketStatus::getDefinitions();
$statusDefinitions = [];
foreach ($rawDefinitions as $sCode => $sDef) {
    $statusDefinitions[$sCode] = [
        'label'    => $sDef['name'],
        'dotClass' => 'dot-' . $sCode
    ];
}
$currentStatusLabel = \App\Entities\TicketStatus::getLabel($statusKey);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(!empty($ticket['ticket_no']) ? $ticket['ticket_no'] . ' - ' : '') ?><?= esc($ticket['subject'] ?? ($ticket['title'] ?? 'Ticket Detail')) ?> | Helpdesk</title>
    <!-- Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
    <!-- Backward compatibility legacy overrides -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/ticket_detail.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php 
        if (session('isLoggedIn')) {
            $active = 'tickets'; 
            include('navbar.php'); 
        }
    ?>

    <div class="main-content" id="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => !empty($ticket['ticket_no']) ? ('Ticket ' . $ticket['ticket_no']) : ('Ticket #' . $ticket['id']),
            'pageTitle'        => 'Ticket Details',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="ticket-detail-page">
            <!-- 1. Top Action & Breadcrumb Command Bar -->
            <?= view('components/admin/ticket_detail/_top_action_bar', [
                'ticket'             => $ticket,
                'uniqueUrl'          => $uniqueUrl,
                'statusKey'          => $statusKey,
                'currentStatusLabel' => $currentStatusLabel,
                'statusDefinitions'  => $statusDefinitions,
            ]) ?>

            <!-- 2. 2-Column Main Layout Grid -->
            <div class="ticket-detail-grid">
                <!-- Left Main Column -->
                <div class="ticket-main-column">
                    <!-- Ticket Headline Banner -->
                    <?= view('components/admin/ticket_detail/_headline_banner', [
                        'ticket'             => $ticket,
                        'lifecycle'          => $lifecycle,
                        'sla'                => $sla,
                        'statusKey'          => $statusKey,
                        'currentStatusLabel' => $currentStatusLabel,
                    ]) ?>

                    <!-- Original Incident Description & Attachments -->
                    <?= view('components/admin/ticket_detail/_incident_description', [
                        'ticket'      => $ticket,
                        'lifecycle'   => $lifecycle,
                        'attachments' => $attachments ?? [],
                    ]) ?>

                    <!-- Activity & Responses Timeline -->
                    <?= view('components/admin/ticket_detail/_activity_timeline', [
                        'activities'         => $activities ?? [],
                        'commentsCount'      => $commentsCount ?? 0,
                        'internalNotesCount' => $internalNotesCount ?? 0,
                        'auditCount'         => $auditCount ?? 0,
                    ]) ?>

                    <!-- Reply Composer (Public Reply / Internal Note) -->
                    <?= view('components/admin/ticket_detail/_reply_composer', [
                        'ticket'             => $ticket,
                        'uniqueUrl'          => $uniqueUrl,
                        'assignedSpecialist' => $assignedSpecialist,
                    ]) ?>
                </div>

                <!-- Right Sidebar Column -->
                <?= view('components/admin/ticket_detail/_properties_sidebar', [
                    'ticket'             => $ticket,
                    'assignedSpecialist' => $assignedSpecialist,
                    'picHelpdesk'        => $picHelpdesk ?? $assignedSpecialist,
                    'picDevBa'           => $picDevBa ?? null,
                    'sla'                => $sla,
                    'lifecycle'          => $lifecycle,
                    'requester'          => $requester,
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Modals Modular Partials -->
    <?= view('components/admin/ticket_detail/_reassign_properties_modal', [
        'ticket'             => $ticket,
        'uniqueUrl'          => $uniqueUrl,
        'users'              => $users ?? [],
        'assignedSpecialist' => $assignedSpecialist,
        'picHelpdesk'        => $picHelpdesk ?? $assignedSpecialist,
        'picDevBa'           => $picDevBa ?? null,
        'statusDefinitions'  => $statusDefinitions,
    ]) ?>

    <?= view('components/admin/ticket_detail/_status_confirm_modal') ?>

    <?= view('components/admin/ticket_detail/_attachment_modal') ?>

    <!-- Floating Feedback Toast Container -->
    <div id="detailToast" class="detail-floating-toast" style="display:none;"></div>

    <!-- Ticket Detail Client Configurations -->
    <script>
        window.TicketDetailConfig = {
            ticketId: '<?= esc($ticket['id'] ?? '') ?>',
            updateStatusUrl: '<?= base_url('admin/update_ticket_status') ?>'
        };
    </script>

    <!-- Modular JavaScript Handler -->
    <script src="<?= base_url('assets/js/modules/admin-ticket-detail.js') ?>?v=<?= time() ?>"></script>
</body>
</html>