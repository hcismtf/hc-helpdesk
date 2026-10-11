<?php
/**
 * Ticket Detail Component: Headline Banner
 *
 * Menampilkan dev/ticket tag, waktu pembaruan, judul subjek tiket, serta badge strip status, prioritas, tipe, dan SLA.
 *
 * @var array  $ticket             Data tiket utama
 * @var array  $lifecycle          Informasi timestamp siklus tiket
 * @var array  $sla                Metrik SLA
 * @var string $statusKey          Key status aktif
 * @var string $currentStatusLabel Label teks status aktif
 */
$prioKey = strtolower($ticket['ticket_priority'] ?? 'medium');
?>
<div class="ticket-headline-card">
    <div class="ticket-meta-top">
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="meta-dev-tag"><?= esc(!empty($ticket['ticket_no']) ? $ticket['ticket_no'] : ('DEV-' . $ticket['id'])) ?></span>
            <span class="meta-source-text">Reported by <?= esc($ticket['reporter_name'] ?? ($ticket['emp_name'] ?? 'User')) ?></span>
        </div>
        <span class="meta-time-text">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            Updated <?= esc($lifecycle['updated_ago']) ?>
        </span>
    </div>

    <h1 class="ticket-subject-title"><?= esc($ticket['subject'] ?? ($ticket['title'] ?? 'No Subject')) ?></h1>

    <div class="ticket-badge-strip">
        <!-- Status Badge -->
        <span class="pill-badge status-select-pill ticket-headline-status-badge status-<?= esc($statusKey) ?>" id="headlineStatusBadge">
            <?= esc($currentStatusLabel) ?>
        </span>

        <!-- Priority Badge -->
        <span class="pill-badge pill-priority-<?= esc($prioKey) ?>">
            Priority: <?= ucfirst($prioKey) ?>
        </span>

        <!-- Request Type Badge -->
        <span class="pill-badge pill-type">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            </svg>
            <?= esc($ticket['req_type'] ?? 'General') ?>
        </span>

        <!-- SLA Metric Badge -->
        <span class="pill-badge pill-<?= esc($sla['badge_class']) ?>" id="headlineSlaBadge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span id="headlineSlaText"><?= esc($sla['badge_text']) ?></span>
        </span>
    </div>
</div>
