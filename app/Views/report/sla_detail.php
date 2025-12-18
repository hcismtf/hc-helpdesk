<?php
// app/Views/report/sla_detail.php
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>SLA Compliance Report</h2>
            
            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= site_url('admin/report/sla-detail') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $this->request->getGet('start_date') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $this->request->getGet('end_date') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">-- All Priorities --</option>
                                <option value="LOW" <?= ($this->request->getGet('priority') == 'LOW') ? 'selected' : '' ?>>Low</option>
                                <option value="MEDIUM" <?= ($this->request->getGet('priority') == 'MEDIUM') ? 'selected' : '' ?>>Medium</option>
                                <option value="HIGH" <?= ($this->request->getGet('priority') == 'HIGH') ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                                <th>Ticket ID</th>
                                <th>Priority</th>
                                <th>Request Type</th>
                                <th>SLA Target (Hours)</th>
                                <th>Actual Time (Hours)</th>
                                <th>Status</th>
                                <th>Compliance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <?php 
                                    $compliance = isset($row['actual_hours']) && isset($row['sla_target_hours']) 
                                        ? ($row['actual_hours'] <= $row['sla_target_hours'] ? 'Met' : 'Missed')
                                        : 'N/A';
                                ?>
                                <tr>
                                    <td><?= $row['ticket_id'] ?? $row['id'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($row['priority'] == 'HIGH') ? 'danger' : ($row['priority'] == 'MEDIUM' ? 'warning' : 'info') ?>">
                                            <?= $row['priority'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['request_type'] ?? $row['req_type'] ?></td>
                                    <td><?= $row['sla_target_hours'] ?? '-' ?></td>
                                    <td><?= isset($row['actual_hours']) ? round($row['actual_hours'], 2) : '-' ?></td>
                                    <td><?= $row['status'] ?? '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($compliance == 'Met') ? 'success' : 'danger' ?>">
                                            <?= $compliance ?>
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
