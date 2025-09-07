<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/error_confirm.php'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan User</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <style>
        body { background: #f5f6fa; font-family: 'Montserrat', Arial, sans-serif; margin: 0; }
        .main-content { margin-left: 70px; padding: 32px 0 0 0; min-height: 100vh; }
        .container { max-width: 100vw; width: 100%; margin: 0 auto; padding: 0 32px; box-sizing: border-box; }
        .page-title { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .breadcrumb { font-size: 15px; color: #444; margin-bottom: 18px; }
        .welcome-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .welcome-user { font-size: 20px; font-weight: 500; }
        .welcome-role { font-size: 13px; color: #444; }
        .report-card { background: #fff; border-radius: 22px; box-shadow: 0 2px 12px #eee; padding: 28px 32px; margin-bottom: 22px; width: 100%; max-width: 1200px; margin-left: auto; margin-right: auto; }
        .report-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 18px; }
        .form-row { display: flex; gap: 18px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 220px; }
        .form-group label { font-weight: 500; display: block; margin-bottom: 8px; }
        .form-group select, .form-group input { width: 100%; padding: 10px 16px; border-radius: 22px; border: 1px solid #ccc; }
        .btn-report { background: #2563eb; color: #fff; border: none; border-radius: 22px; padding: 8px 32px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-report:hover { background: #1a3bb3; }
        @media (max-width: 900px) {
            .main-content { margin-left: 0 !important; padding: 12px 0 0 0; }
            .container { padding: 0 8px; }
            .report-card { padding: 12px 4px; max-width: 100vw; }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            table-layout: auto;
            background: #fff;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
            font-size: 15px;
        }
        th {
            background: #f5f6fa;
            font-weight: 600;
            font-size: 15px;
        }
        @media (max-width: 900px) {
            table, th, td {
                font-size: 13px;
                padding: 6px 4px;
            }
            .report-card {
                padding: 8px 2px;
            }
            th, td {
                min-width: 80px;
            }
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
    <script>
        function onReportTypeChange() {
            var val = document.getElementById('report_type').value;
            var ticketFields = document.getElementById('ticket-fields');
            var slaFields = document.getElementById('sla-fields');
            if (val === 'Report Ticket Detail') {
                ticketFields.style.display = 'flex';
                slaFields.style.display = 'none';
            } else {
                ticketFields.style.display = 'none';
                slaFields.style.display = 'flex';
            }
        }

        // Fungsi modal error
        function showGlobalError(msg) {
            var errorDiv = document.getElementById('global-error-confirm');
            errorDiv.innerHTML = '<div style="background:#fff;padding:32px 24px;border-radius:18px;box-shadow:0 2px 12px #eee;max-width:350px;margin:auto;text-align:center;"><div style="font-size:22px;color:#ef4444;margin-bottom:12px;">❌ Error</div><div style="font-size:16px;color:#333;margin-bottom:18px;">'+msg+'</div><button onclick="document.getElementById(\'global-error-confirm\').style.display=\'none\'" style="background:#ef4444;color:#fff;border:none;border-radius:12px;padding:8px 24px;font-size:15px;cursor:pointer;">Tutup</button></div>';
            errorDiv.style.display = 'flex';
        }

        window.addEventListener('DOMContentLoaded', function() {
            var exportForm = document.getElementById('exportAsyncForm');
            if (exportForm) {
                exportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var form = e.target;
                    var formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('global-success-message').innerText = 'Export job berhasil ditambahkan! Silakan cek daftar di bawah.';
                            document.getElementById('global-success-confirm-bg').style.display = 'block';
                            document.getElementById('global-success-confirm').style.display = 'block';
                            setTimeout(function() {
                                document.getElementById('global-success-confirm-bg').style.display = 'none';
                                document.getElementById('global-success-confirm').style.display = 'none';
                                location.reload();
                            }, 1500);
                        } else {
                            showGlobalError(data.message || 'Gagal submit export job.');
                        }
                    })
                    .catch(function() {
                        showGlobalError('Gagal submit export job.');
                    });
                });
            }
            onReportTypeChange();

            // Success modal function
            function showSuccessModal(msg) {
                document.getElementById('global-success-message').innerText = msg;
                document.getElementById('global-success-confirm-bg').style.display = 'block';
                document.getElementById('global-success-confirm').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('global-success-confirm-bg').style.display = 'none';
                    document.getElementById('global-success-confirm').style.display = 'none';
                    location.reload();
                }, 1500);
            }

            // Warning modal function
            function showWarningModal(msg, onOk) {
                document.getElementById('global-warning-message').innerText = msg;
                document.getElementById('global-warning-confirm-bg').style.display = 'block';
                document.getElementById('global-warning-confirm').style.display = 'block';
                document.getElementById('global-warning-cancel-btn').onclick = function() {
                    document.getElementById('global-warning-confirm-bg').style.display = 'none';
                    document.getElementById('global-warning-confirm').style.display = 'none';
                };
                document.getElementById('global-warning-ok-btn').onclick = function() {
                    document.getElementById('global-warning-confirm-bg').style.display = 'none';
                    document.getElementById('global-warning-confirm').style.display = 'none';
                    onOk();
                };
            }

            // Intercept delete form submit
            document.querySelectorAll('.delete-job-form').forEach(function(form) {
                var btn = form.querySelector('button[type="button"]');
                btn.addEventListener('click', function(e) {
                    showWarningModal('Yakin hapus job ini?', function() {
                        // Submit via AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', form.action, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                showSuccessModal('Job berhasil dihapus!');
                            } else {
                                showGlobalError('Gagal menghapus job.');
                            }
                        };
                        xhr.send(new FormData(form));
                    });
                });
            });
        });
    </script>
</head>
<body>
    <?php include(APPPATH . 'Views/admin/navbar.php'); ?>
    <div class="main-content">
        <div class="container">
            <div class="page-title">Laporan</div>
            <div class="breadcrumb">Home &gt; Laporan</div>
            <div class="welcome-row">
                <div class="welcome-user">Selamat datang, <?= esc($username) ?></div>
                <div class="welcome-role"><?= esc($role) ?></div>
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
                        <button type="submit" class="btn-report">Submit Export</button>
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