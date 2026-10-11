<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
    <!-- Modern Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
</head>

<body>

    <?php $active = 'dashboard';
    include('navbar.php'); ?>

    <div class="main-content" id="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot' => 'Helpdesk Admin',
            'breadcrumbActive' => 'Analytics Overview',
            'pageTitle' => 'Dashboard',
            'showCreateTicket' => true,
            'showNotif' => true,
        ]) ?>

        <!-- SLA Overview Banner with Connected Interactive Trend Chart -->
        <?= view('components/admin/dashboard/_sla_banner', [
            'avgResponseStr'   => $avgResponseStr,
            'avgResolutionStr' => $avgResolutionStr,
            'slaRate'          => $slaRate,
        ]) ?>

        <!-- Status Overview Metric Cards -->
        <?= view('components/admin/dashboard/_status_metrics', [
            'openCount'       => $openCount,
            'inProgressCount' => $inProgressCount,
            'doneCount'       => $doneCount,
            'totalCount'      => $totalCount,
        ]) ?>

        <!-- Reusable Dynamic Data Table Component -->
        <?= view('components/common/_data_table', [
            'title' => 'Open Tickets',
            'tableId' => 'ticketsTable',
            'perPage' => $perPage,
            'columns' => [
                'emp_name'     => ['label' => 'Requester', 'type' => 'requester'],
                'req_type'     => ['label' => 'Request Type', 'type' => 'category'],
                'created_date' => ['label' => 'Created Date', 'type' => 'datetime'],
                'due_date'     => ['label' => 'Due Date', 'type' => 'datetime'],
                'day_left'     => ['label' => 'Day Left', 'type' => 'day_left', 'due_field' => 'due_date'],
                'action'       => ['label' => 'Action', 'type' => 'action', 'detail_url' => 'admin/ticket_detail'],
            ],
            'rows' => $openTickets,
            'emptyMessage' => 'No open tickets found.',
            'paginationHTML' => $paginationHTML ?? '',
        ]) ?>

        <!-- Filter Modal -->
        <div id="filterModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeFilterModal()">&times;</span>
                <h3 style="margin-bottom:20px; padding-left:10px;">Filter</h3>
                <form id="filterForm" onsubmit="applyFilter();return false;">
                    <!-- Request Type -->
                    <div class="form-row">
                        <label>Request Type</label>
                        <select name="type">
                            <option value="">Select</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t['id'] ?? $t['name']) ?>" <?= $type == ($t['id'] ?? $t['name']) ? 'selected' : '' ?>>
                                    <?= esc($t['name'] ?? ($t['req_type'] ?? '-')) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <!-- Date Inputs -->
                    <div class="form-row dates-row">
                        <div class="date-col">
                            <label>Start Date</label>
                            <input type="date" name="start" id="startDate" value="<?= esc($start) ?>">
                        </div>
                        <div class="date-col">
                            <label>End Date</label>
                            <input type="date" name="end" id="endDate" value="<?= esc($end) ?>">
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="form-row btn-row">
                        <button type="submit" class="btn-blue">Apply Filter</button>
                        <button type="button" onclick="resetFilter()" class="btn-blue">Reset Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Chart Trends Data Payload Container -->
        <div id="dashboardChartData" style="display:none;"
            data-dates='<?= json_encode($trendDates ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) ?>'
            data-incoming='<?= json_encode($trendIncoming ?? [0, 0, 0, 0, 0, 0, 0]) ?>'
            data-resolved='<?= json_encode($trendResolved ?? [0, 0, 0, 0, 0, 0, 0]) ?>'
            data-sla='<?= json_encode($trendSla ?? [100, 100, 100, 100, 100, 100, 100]) ?>'>
        </div>
    </div>

    <!-- Chart.js and Modular Dashboard Logic -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/modules/admin-dashboard.js') ?>?v=<?= time() ?>"></script>
</body>

</html>