<?php
/**
 * Admin Dashboard Status Metrics Cards Component
 *
 * @var int $openCount
 * @var int $inProgressCount
 * @var int $doneCount
 * @var int $totalCount
 */
?>
<div class="modern-stats-grid">
    <!-- Open Tickets -->
    <a href="<?= base_url('admin/ticket_dashboard?status=open') ?>" class="modern-stat-card card-open" style="text-decoration:none; color:inherit;">
        <div class="stat-card-header">
            <div class="stat-card-badge">Needs Action</div>
            <div class="stat-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
        </div>
        <div class="stat-card-body">
            <div class="stat-card-number"><?= esc($openCount) ?></div>
            <div class="stat-card-label">Open Tickets</div>
        </div>
        <div class="stat-card-footer">
            <span class="stat-footer-text">Awaiting assignment & response</span>
        </div>
    </a>

    <!-- In Progress Tickets -->
    <a href="<?= base_url('admin/ticket_dashboard?status=in_progress') ?>" class="modern-stat-card card-inprogress" style="text-decoration:none; color:inherit;">
        <div class="stat-card-header">
            <div class="stat-card-badge">Active Handling</div>
            <div class="stat-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
            </div>
        </div>
        <div class="stat-card-body">
            <div class="stat-card-number"><?= esc($inProgressCount) ?></div>
            <div class="stat-card-label">In Progress</div>
        </div>
        <div class="stat-card-footer">
            <span class="stat-footer-text">Currently being handled by team</span>
        </div>
    </a>

    <!-- Done Tickets -->
    <a href="<?= base_url('admin/ticket_dashboard?status=done') ?>" class="modern-stat-card card-done" style="text-decoration:none; color:inherit;">
        <div class="stat-card-header">
            <div class="stat-card-badge">Completed</div>
            <div class="stat-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
        </div>
        <div class="stat-card-body">
            <div class="stat-card-number"><?= esc($doneCount) ?></div>
            <div class="stat-card-label">Done / Resolved</div>
        </div>
        <div class="stat-card-footer">
            <span class="stat-footer-text">Successfully finished & closed</span>
        </div>
    </a>

    <!-- Total Tickets -->
    <a href="<?= base_url('admin/ticket_dashboard') ?>" class="modern-stat-card card-total" style="text-decoration:none; color:inherit;">
        <div class="stat-card-header">
            <div class="stat-card-badge">Total Volume</div>
            <div class="stat-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
        </div>
        <div class="stat-card-body">
            <div class="stat-card-number"><?= esc($totalCount) ?></div>
            <div class="stat-card-label">Total Tickets</div>
        </div>
        <div class="stat-card-footer">
            <span class="stat-footer-text">All recorded tickets in period</span>
        </div>
    </a>
</div>
