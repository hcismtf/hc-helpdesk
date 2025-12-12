<?php
// app/Views/report/ticket_detail.php
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Ticket Detail Report</h2>
            
            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= site_url('admin/report/ticket-detail') ?>" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $this->request->getGet('start_date') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $this->request->getGet('end_date') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Request Type</label>
                            <select name="request_type" class="form-select">
                                <option value="">-- All Types --</option>
                                <?php if (isset($requestTypes)): ?>
                                    <?php foreach ($requestTypes as $type): ?>
                                        <option value="<?= $type['req_type'] ?>" <?= ($this->request->getGet('request_type') == $type['req_type']) ? 'selected' : '' ?>>
                                            <?= $type['req_type'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
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
                                <th>ID</th>
                                <th>Employee Name</th>
                                <th>Email</th>
                                <th>WA No</th>
                                <th>Request Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created Date</th>
                                <th>Due Date</th>
                                <th>Finish Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['emp_name'] ?></td>
                                    <td><?= $row['email'] ?></td>
                                    <td><?= $row['wa_no'] ?></td>
                                    <td><?= $row['req_type'] ?></td>
                                    <td><?= substr($row['subject'], 0, 30) ?>...</td>
                                    <td>
                                        <span class="badge bg-<?= ($row['ticket_status'] == 'Open') ? 'primary' : ($row['ticket_status'] == 'In Progress' ? 'warning' : 'success') ?>">
                                            <?= $row['ticket_status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($row['ticket_priority'] == 'HIGH') ? 'danger' : ($row['ticket_priority'] == 'MEDIUM' ? 'warning' : 'info') ?>">
                                            <?= $row['ticket_priority'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['due_date'])) ?></td>
                                    <td><?= !empty($row['finish_date']) ? date('d/m/Y H:i', strtotime($row['finish_date'])) : '-' ?></td>
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
