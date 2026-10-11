<?php
/**
 * Ticket Management Dashboard Orchestrator View
 *
 * Mengikuti arsitektur Anti-Monolith & Clean Views (rule.md).
 * Bertindak sebagai orchestrator tata letak modular dengan memanggil view partials ber-prefix '_'.
 *
 * @var array       $tickets         Data tiket (dengan pagination) untuk Table View
 * @var array       $kanbanTickets   Data tiket lengkap untuk Kanban Swimlane View
 * @var array       $requestTypes    Daftar master sub-kategori / tipe permohonan
 * @var string|null $priority        Filter prioritas aktif
 * @var string|null $status          Filter status aktif
 * @var string|null $type            Filter tipe permohonan aktif
 * @var string|null $search          Keyword pencarian aktif
 * @var int         $perPage         Jumlah tiket per halaman
 * @var string      $paginationHTML  Markup pagination CI4
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management - Helpdesk Admin</title>
    <!-- Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
</head>

<body>

    <?php
    $active = 'tickets';
    $subActive = $subActive ?? 'all_tickets';
    include('navbar.php');
    ?>

    <div class="main-content" id="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot' => 'Helpdesk Admin',
            'breadcrumbActive' => ($subActive === 'my_tickets') ? 'My Tickets' : 'All Tickets',
            'pageTitle' => ($subActive === 'my_tickets') ? 'My Assigned Tickets' : 'Ticket List',
            'showCreateTicket' => true,
            'showNotif' => true,
        ]) ?>

        <!-- 1. Filter, Search & View Switcher Bar -->
        <?= view('components/admin/ticket_dashboard/_control_strip', [
            'search'       => $search ?? '',
            'priority'     => $priority ?? '',
            'type'         => $type ?? '',
            'status'       => $status ?? '',
            'perPage'      => $perPage ?? 12,
            'requestTypes' => $requestTypes ?? [],
            'slaList'      => $slaList ?? [],
        ]) ?>

        <!-- 2. Table View Component -->
        <?= view('components/admin/ticket_dashboard/_table_view', [
            'tickets'        => $tickets ?? [],
            'paginationHTML' => $paginationHTML ?? '',
            'slaList'        => $slaList ?? []
        ]) ?>

        <!-- 3. Kanban Board Swimlanes Component -->
        <?= view('components/admin/ticket_dashboard/_kanban_view', [
            'kanbanTickets' => $kanbanTickets ?? $tickets ?? [],
            'tickets'       => $tickets ?? []
        ]) ?>

        <!-- Toast Notification for Kanban Drag & Drop -->
        <div id="kanbanToast" class="kanban-toast" style="display:none;"></div>
    </div>

    <!-- Inject Endpoint URL for JS Modules -->
    <script>
        window.TICKET_UPDATE_STATUS_URL = '<?= base_url('admin/update_ticket_status') ?>';
        window.TICKET_UPDATE_SLA_URL    = '<?= base_url('admin/update_ticket_sla') ?>';
    </script>
    <script src="<?= base_url('assets/js/modules/admin-ticket-dashboard.js') ?>?v=<?= time() ?>"></script>
</body>

</html>