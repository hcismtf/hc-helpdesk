<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report User</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <style>
        body { background: #f5f6fa; font-family: 'Montserrat', Arial, sans-serif; margin: 0; }
        .main-content { margin-left: 70px; padding: 32px 0 0 0; min-height: 100vh; }
        .container {
            max-width: 100vw;
            width: 100%;
            margin: 0 auto;
            padding: 0 32px;
            box-sizing: border-box;
        }
        .page-title { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .breadcrumb { font-size: 15px; color: #444; margin-bottom: 18px; }
        .welcome-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .welcome-user { font-size: 20px; font-weight: 500; }
        .welcome-role { font-size: 13px; color: #444; }
        .report-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 2px 12px #eee;
            padding: 28px 32px;
            margin-bottom: 22px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .report-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 18px; }
        .report-actions-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 12px;
            gap: 12px;
        }
        .btn-report {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 8px 32px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-report:hover { background: #1a3bb3; }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }
        .report-table th, .report-table td {
            padding: 12px 8px;
            font-size: 15px;
            text-align: left;
        }
        .report-table th {
            background: #f7f7f7;
            font-weight: 600;
        }
        .report-table tr:not(:last-child) td {
            border-bottom: 1px solid #eee;
        }
        .btn-download {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-weight: 600;
            cursor: pointer;
        }
        .report-pagination-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 18px 0 0 0;
        }
        .report-page-btn {
            background: #fff;
            border: none;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 16px;
            font-weight: 600;
            color: #222;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .report-page-btn.active {
            background: #2940D3;
            color: #fff;
        }
        @media (max-width: 900px) {
            .main-content { margin-left: 0 !important; padding: 12px 0 0 0; }
            .container { padding: 0 8px; }
            .report-card { padding: 12px 4px; max-width: 100vw; }
            .report-table { min-width: 400px !important; }
        }
    </style>
</head>
<body>
    <?php include(APPPATH . 'Views/admin/navbar.php'); ?>
    <div class="main-content">
        <div class="container">
            <div class="page-title">Report</div>
            <div class="breadcrumb">Home &gt; Report</div>
            <div class="welcome-row">
                <div class="welcome-user">Welcome, <?= esc($username) ?></div>
                
            </div>

            <!-- Submit Report -->
            <div class="report-card">
                <div class="report-title">Submit Report</div>
                <form id="report-form" style="display:flex; flex-direction:column; gap:18px;">
                    <div>
                        <label style="font-weight:500;">Report Type</label>
                        <select name="report_type" style="width:100%; padding:10px 16px; border-radius:22px; border:1px solid #ccc; margin-top:8px;">
                            <option value="">Select report type</option>
                            <?php foreach ($reportTypes as $type): ?>
                                <option value="<?= esc($type['id']) ?>"><?= esc($type['name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn-report">Submit</button>
                    </div>
                </form>
            </div>

            <!-- Report History -->
            <div class="report-card">
                <div class="report-title">Report History</div>
                <div class="report-actions-row">
                    <span>Showing Data</span>
                    <select id="perPage" style="padding:6px 18px; border-radius:8px; border:1px solid #ccc;">
                        <option value="10" <?= $perPage==10?'selected':'' ?>>10</option>
                        <option value="20" <?= $perPage==20?'selected':'' ?>>20</option>
                        <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
                    </select>
                    <button class="btn-report">Add New User</button>
                </div>
                <div style="overflow-x:auto;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Report Type</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports)): ?>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= esc($report['type_name'] ?? 'All') ?></td>
                                        <td><?= esc($report['created_date']) ?></td>
                                        <td>
                                            <button class="btn-download">Download</button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; color:#888; padding:24px;">Belum ada list users</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="report-pagination-row">
                    <?php if ($totalPages > 1): ?>
                        <?php for ($i=1; $i<=$totalPages; $i++): ?>
                            <button class="report-page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></button>
                        <?php endfor ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>