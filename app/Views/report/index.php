<?php
// app/Views/report/index.php - Halaman utama report
?>

<div class="container mt-5">
    <h2>Dashboard Report</h2>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Ticket Detail Report</h5>
                    <p class="card-text">Laporan detail semua ticket</p>
                    <a href="<?= site_url('admin/report/ticket-detail') ?>" class="btn btn-primary btn-sm">
                        Lihat Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">SLA Detail Report</h5>
                    <p class="card-text">Status compliance SLA</p>
                    <a href="<?= site_url('admin/report/sla-detail') ?>" class="btn btn-primary btn-sm">
                        Lihat Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">SLA Response Time</h5>
                    <p class="card-text">Perbandingan response time</p>
                    <a href="<?= site_url('admin/report/sla-response') ?>" class="btn btn-primary btn-sm">
                        Lihat Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">SLA Resolution Time</h5>
                    <p class="card-text">Perbandingan resolution time</p>
                    <a href="<?= site_url('admin/report/sla-resolution') ?>" class="btn btn-primary btn-sm">
                        Lihat Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
