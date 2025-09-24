<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/error_confirm.php'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report User</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/report_user.css') ?>">
    <script src="<?= base_url('assets/js/admin/report_user.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/auto_logout.js') ?>"></script>

</head>
<body>
    <?php include(APPPATH . 'Views/admin/navbar.php'); ?>
    <div class="main-content">
        <div class="container">
            <div class="page-title">Report</div>
            <div class="breadcrumb">Home &gt; Report</div>
            <div class="welcome-row">
                <div class="welcome-user">Selamat datang, <?= esc($username) ?></div>
                
            </div>

            <div class="report-card">
                <div class="report-title">Submit Report</div>
                <form id="exportAsyncForm" method="post" action="<?= base_url('admin/submit_report_job') ?>" style="display:flex; flex-direction:column; gap:18px;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="report_type">Report Type</label>
                            <select name="report_type" id="report_type" onchange="onReportTypeChange()">
                                <option value="Report Ticket Detail" <?= ($reportType ?? '')=='Report Ticket Detail'?'selected':'' ?>>Report Ticket Detail</option>
                                <option value="Report SLA" <?= ($reportType ?? '')=='Report SLA'?'selected':'' ?>>Report SLA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row" id="ticket-fields" style="<?= ($reportType ?? '')=='Report Ticket Detail'?'display:flex':'display:none' ?>">
                        <div class="form-group">
                            <label for="request_type">Request Type</label>
                            <select name="request_type" id="request_type">
                                <option value="">-- Pilih Request Type --</option>
                                <?php foreach ($requestTypes ?? [] as $rt): ?>
                                    <option value="<?= esc($rt['req_type']) ?>" <?= ($requestType ?? '')==$rt['req_type']?'selected':'' ?>>
                                        <?= esc($rt['req_type']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="priority_ticket">Prioritas</label>
                            <select name="priority_ticket" id="priority_ticket">
                                <option value="">-- Pilih Prioritas --</option>
                                <?php foreach ($priorities ?? [] as $p): ?>
                                    <?php if (!empty($p['ticket_priority'])): ?>
                                        <option value="<?= esc(strtolower($p['ticket_priority'])) ?>" <?= (strtolower($priority_ticket ?? ''))==strtolower($p['ticket_priority'])?'selected':'' ?>>
                                            <?= esc(ucfirst($p['ticket_priority'])) ?>
                                        </option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date_ticket">Tanggal Mulai</label>
                            <input type="date" name="start_date_ticket" id="start_date_ticket" value="<?= esc($start_date_ticket ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_date_ticket">Tanggal Akhir</label>
                            <input type="date" name="end_date_ticket" id="end_date_ticket" value="<?= esc($end_date_ticket ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row" id="sla-fields" style="<?= ($reportType ?? '')=='Report SLA'?'display:flex':'display:none' ?>">
                        <div class="form-group">
                            <label for="priority_sla">Prioritas</label>
                            <select name="priority_sla" id="priority_sla">
                                <option value="">-- Pilih Prioritas --</option>
                                <?php foreach ($priorities ?? [] as $p): ?>
                                    <option value="<?= esc(strtolower($p['ticket_priority'])) ?>" <?= (strtolower($priority_sla ?? ''))==strtolower($p['ticket_priority'])?'selected':'' ?>>
                                        <?= esc(ucfirst($p['ticket_priority'])) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date_sla">Tanggal Mulai</label>
                            <input type="date" name="start_date_sla" id="start_date_sla" value="<?= esc($start_date_sla ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_date_sla">Tanggal Akhir</label>
                            <input type="date" name="end_date_sla" id="end_date_sla" value="<?= esc($end_date_sla ?? '') ?>">
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn-report">Submit Report</button>
                    </div>
                </form>
            </div>

            <!-- List Job Export -->
            <div class="report-card">
                <div class="report-title">Report List</div>
                <table style="border-radius:12px; overflow:hidden;">
                    <thead style="background:#f5f6fa;">
                        <tr>
                            <th style="width:180px;">Report Type</th>
                            <th style="width:180px;">Request Date</th>
                            <th style="width:120px;">Status</th>
                            <th style="width:220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportJobs ?? [] as $job): ?>
                            <tr>
                                <td><?= esc($job['report_type']) ?></td>
                                <td><?= esc($job['created_at']) ?></td>
                                <td>
                                    <?php if ($job['status'] == 'done'): ?>
                                        <span style="color:#22c55e;font-weight:600;">Selesai</span>
                                    <?php elseif ($job['status'] == 'failed'): ?>
                                        <span style="color:red;font-weight:600;">Gagal</span>
                                    <?php else: ?>
                                        <span style="color:gray;">Menunggu</span>
                                    <?php endif ?>
                                </td>
                                <td style="display:flex; gap:8px; align-items:center;">
                                    <?php if ($job['status'] == 'done' && !empty($job['file_path'])): ?>
                                        <a href="<?= base_url('admin/download_report/' . $job['id']) ?>"
                                        class="btn-report"
                                        style="background:#22c55e;min-width:80px;padding:6px 18px;font-size:15px;border-radius:18px;text-align:center;display:inline-block;">
                                            Download
                                        </a>
                                    <?php else: ?>
                                        <span class="btn-report"
                                            style="background:#a1a1aa;min-width:80px;padding:6px 18px;font-size:15px;border-radius:18px;text-align:center;cursor:not-allowed;opacity:0.7;display:inline-block;">
                                            Download
                                        </span>
                                    <?php endif ?>
                                    <form method="post"
                                        action="<?= base_url('admin/delete_report_job/' . $job['id']) ?>"
                                        class="delete-job-form"
                                        style="display:inline;">
                                        <button type="button"
                                                class="btn-report"
                                                style="background:transparent; color:#ef4444; border:none; min-width:32px; font-size:22px; padding:0; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;"
                                                title="Delete">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>