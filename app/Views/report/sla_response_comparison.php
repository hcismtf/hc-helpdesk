<?php
// app/Views/report/sla_response_comparison.php
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>SLA Response Time Comparison Report</h2>
            
            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= site_url('admin/report/sla-response') ?>" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $this->request->getGet('start_date') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $this->request->getGet('end_date') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Table -->
            <?php if (!empty($data)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Priority Level</th>
                                <th>SLA Target (Hours)</th>
                                <th>Avg Response Time (Hours)</th>
                                <th>Min Response Time (Hours)</th>
                                <th>Max Response Time (Hours)</th>
                                <th>Total Tickets</th>
                                <th>Met SLA</th>
                                <th>Missed SLA</th>
                                <th>Compliance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <?php 
                                    $total = ($row['met'] ?? 0) + ($row['missed'] ?? 0);
                                    $compliance = $total > 0 ? round((($row['met'] ?? 0) / $total) * 100, 2) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= ($row['priority'] == 'HIGH') ? 'danger' : ($row['priority'] == 'MEDIUM' ? 'warning' : 'info') ?>">
                                            <?= $row['priority'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['sla_target_hours'] ?? '-' ?></td>
                                    <td><?= isset($row['avg_response_hours']) ? round($row['avg_response_hours'], 2) : '-' ?></td>
                                    <td><?= isset($row['min_response_hours']) ? round($row['min_response_hours'], 2) : '-' ?></td>
                                    <td><?= isset($row['max_response_hours']) ? round($row['max_response_hours'], 2) : '-' ?></td>
                                    <td><?= $total ?></td>
                                    <td><strong><?= $row['met'] ?? 0 ?></strong></td>
                                    <td><strong><?= $row['missed'] ?? 0 ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?= ($compliance >= 80) ? 'success' : 'warning' ?>">
                                            <?= $compliance ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Tidak ada data untuk ditampilkan. Silakan atur filter dan coba lagi.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
