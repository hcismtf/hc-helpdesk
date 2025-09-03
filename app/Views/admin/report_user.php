<?php include(APPPATH . 'Views/admin/navbar.php'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
<div class="main-content" style="padding:32px; margin-left:220px;">
    <div style="font-size:2rem; font-weight:600; margin-bottom:8px;">Report</div>
    <div style="color:#888; margin-bottom:18px;">Home &gt; Report</div>
    <div style="display:flex; flex-wrap:wrap; justify-content:flex-end; align-items:center; margin-bottom:18px;">
        <div style="font-size:18px; margin-right:auto;">Welcome, <?= esc($username) ?></div>
        <div style="font-size:16px; color:#666;"><?= esc($role) ?></div>
    </div>

    <!-- Submit Report -->
    <div style="background:#fff; border-radius:22px; box-shadow:0 2px 12px #eee; padding:28px 32px; margin-bottom:22px;">
        <div style="font-size:1.2rem; font-weight:600; margin-bottom:18px;">Submit Report</div>
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
                <button type="submit" style="background:#2563eb; color:#fff; border:none; border-radius:22px; padding:8px 32px; font-size:1rem; font-weight:600; cursor:pointer;">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <!-- Report History -->
    <div style="background:#fff; border-radius:22px; box-shadow:0 2px 12px #eee; padding:28px 32px;">
        <div style="font-size:1.2rem; font-weight:600; margin-bottom:18px;">Report History</div>
        <div style="display:flex; flex-wrap:wrap; justify-content:flex-end; align-items:center; margin-bottom:12px;">
            <span style="margin-right:12px;">Showing Data</span>
            <select id="perPage" style="padding:6px 18px; border-radius:8px; border:1px solid #ccc;">
                <option value="10" <?= $perPage==10?'selected':'' ?>>10</option>
                <option value="20" <?= $perPage==20?'selected':'' ?>>20</option>
                <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
            </select>
            <button style="margin-left:18px; background:#2563eb; color:#fff; border:none; border-radius:22px; padding:8px 32px; font-size:1rem; font-weight:600; cursor:pointer;">
                Add New User
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:600px;">
                <thead>
                    <tr style="background:#f7f7f7;">
                        <th style="padding:12px 8px; font-weight:600;">Report Type</th>
                        <th style="padding:12px 8px; font-weight:600;">Created Date</th>
                        <th style="padding:12px 8px; font-weight:600;">Created Date</th>
                        <th style="padding:12px 8px; font-weight:600;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td style="padding:12px 8px;"><?= esc($report['type_name'] ?? 'All') ?></td>
                                <td style="padding:12px 8px;"><?= esc($report['created_date']) ?></td>
                                <td style="padding:12px 8px;"><?= esc($report['created_date']) ?></td>
                                <td style="padding:12px 8px;">
                                    <button style="background:#2563eb; color:#fff; border:none; border-radius:12px; padding:6px 18px; font-weight:600; cursor:pointer;">
                                        Download
                                    </button>
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
        <div style="display:flex; justify-content:center; align-items:center; margin-top:18px; flex-wrap:wrap;">
            <?php if ($totalPages > 1): ?>
                <?php for ($i=1; $i<=$totalPages; $i++): ?>
                    <button style="margin:0 4px; padding:6px 14px; border:none; border-radius:8px; background:<?= $i==$page?'#2563eb':'#f7f7f7' ?>; color:<?= $i==$page?'#fff':'#222' ?>; font-weight:600; cursor:pointer;">
                        <?= $i ?>
                    </button>
                <?php endfor ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .main-content {
        margin-left: 0 !important;
        padding: 12px !important;
    }
    .sidebar {
        width: 100vw !important;
        position: fixed !important;
        left: 0; top: 0;
        z-index: 1000;
    }
    .main-content > div, .main-content form, .main-content table {
        font-size: 0.95rem !important;
    }
    table {
        min-width: 400px !important;
    }
}
</style>