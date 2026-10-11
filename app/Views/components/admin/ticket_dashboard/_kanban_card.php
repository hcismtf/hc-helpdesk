<?php
/**
 * Ticket Dashboard Kanban Card Partial
 *
 * @var array $ticket
 */

$ticketId = $ticket['id'] ?? '';
$initials = strtoupper(substr(trim($ticket['emp_name'] ?? 'U'), 0, 2));
$statusVal = strtolower(trim($ticket['ticket_status'] ?? 'open'));
$statusLabel = ($statusVal === 'in_progress') ? 'In Progress' : ucwords(str_replace('_', ' ', $statusVal));
$priorityVal = ucfirst(trim($ticket['ticket_priority'] ?? ''));
$createdDateYmd = !empty($ticket['created_date']) ? date('Y-m-d', strtotime($ticket['created_date'])) : '';
$createdTimestamp = !empty($ticket['created_date']) ? strtotime($ticket['created_date']) : 0;
$modifiedTimestamp = !empty($ticket['modified_date']) ? strtotime($ticket['modified_date']) : (!empty($ticket['finish_date']) ? strtotime($ticket['finish_date']) : $createdTimestamp);

$dueVal = $ticket['due_date'] ?? null;
$overdueHtml = '<span class="text-muted">-</span>';
if (!empty($dueVal)) {
    $now = new DateTime();
    $due = new DateTime($dueVal);
    $interval = $now->diff($due);
    $days = (int) $interval->days;
    $hours = (int) $interval->h;
    $mins = (int) $interval->i;

    $parts = [];
    if ($days > 0) $parts[] = $days . 'd';
    if ($hours > 0) $parts[] = $hours . 'h';
    if ($mins > 0 || empty($parts)) $parts[] = $mins . 'm';
    $durStr = implode(' ', $parts);

    if ($statusVal === 'closed' || $statusVal === 'done') {
        $overdueHtml = '<span class="status-badge status-done">Resolved</span>';
    } elseif ($now <= $due) {
        $overdueHtml = '<span class="time-left">' . esc($durStr) . '</span>';
    } else {
        $overdueHtml = '<span class="badge-overdue">OVERDUE (' . esc($durStr) . ')</span>';
    }
}
$ticketDesc = !empty($ticket['message']) ? trim(strip_tags($ticket['message'])) : (!empty($ticket['description']) ? trim(strip_tags($ticket['description'])) : '-');
?>
<div class="ticket-card" id="card-<?= esc($ticketId) ?>" data-ticket-id="<?= esc($ticketId) ?>" data-status="<?= esc($statusVal) ?>" data-created-ymd="<?= esc($createdDateYmd) ?>" data-created-ts="<?= esc($createdTimestamp) ?>" data-modified-ts="<?= esc($modifiedTimestamp) ?>" draggable="true">
    <div class="ticket-card-header">
        <div class="ticket-header-left">
            <span class="ticket-id-tag">#<?= esc(!empty($ticket['ticket_no']) ? $ticket['ticket_no'] : substr($ticketId, 0, 8)) ?></span>
            <div class="ticket-requester-info">
                <span class="req-name" title="<?= esc($ticket['emp_name'] ?? '') ?>"><?= esc($ticket['emp_name'] ?? 'Unknown') ?></span>
                <?php if (!empty($ticket['emp_nip']) && $ticket['emp_nip'] !== '-'): ?>
                    <span class="req-nip">NIP: <?= esc($ticket['emp_nip']) ?></span>
                <?php endif; ?>
                <?php if (!empty($priorityVal) && $priorityVal !== '-'): ?>
                    <span class="card-priority-tag priority-<?= strtolower(esc($priorityVal)) ?>">
                        Priority: <?= esc($priorityVal) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-badges-right">
            <span class="status-badge status-<?= esc($statusVal) ?>"><?= esc($statusLabel) ?></span>
        </div>
    </div>

    <div class="ticket-card-body">
        <h4 class="ticket-card-title">
            <a href="<?= base_url('admin/ticket_detail/' . esc($ticketId)) ?>" title="<?= esc($ticket['subject'] ?? '') ?>">
                <?= esc($ticket['subject'] ?? 'Untitled Ticket') ?>
            </a>
        </h4>

        <p class="ticket-card-desc" title="<?= esc($ticketDesc) ?>">
            <?= esc($ticketDesc) ?>
        </p>

        <?php if (!empty($ticket['req_type'])): ?>
        <div class="ticket-card-tags">
            <span class="ticket-type-pill"><?= esc($ticket['req_type']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="ticket-card-footer">
        <div class="ticket-meta-left">
            <?= $overdueHtml ?>
            <span class="meta-date"><?= !empty($ticket['created_date']) ? esc(date('d M Y, H:i', strtotime($ticket['created_date']))) : '-' ?></span>
        </div>
        <a href="<?= base_url('admin/ticket_detail/' . esc($ticketId)) ?>" style="text-decoration:none;">
            <button type="button" class="btn-open">
                <span>Open</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </a>
    </div>
</div>
