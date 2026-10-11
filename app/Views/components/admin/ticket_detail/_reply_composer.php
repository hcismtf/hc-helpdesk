<?php
/**
 * Ticket Detail Component: Reply Composer
 *
 * Form composer tanggapan admin: Public Reply vs 🔒 Internal Note (Admin Only), status checkbox, input hidden, dan tombol kirim.
 *
 * @var array  $ticket             Data tiket utama
 * @var string $uniqueUrl          Token unik URL tiket
 * @var array  $assignedSpecialist Data specialist yang ditugaskan
 */
$isClosed = (($ticket['ticket_status'] ?? ($ticket['status'] ?? '')) === 'closed');
$isAdmin  = session('isLoggedIn');
?>
<?php if ($isClosed && !$isAdmin): ?>
    <div class="reply-composer-card" style="background:#fff1f2; border-color:#fecaca; text-align:center;">
        <strong style="color:#be123c;">Ticket Closed</strong>
        <p style="color:#e11d48; margin:4px 0 0 0; font-size:13px;">This ticket is closed. Please submit a new ticket if you need further assistance.</p>
    </div>
<?php else: ?>
    <div class="reply-composer-card">
        <form id="ticketReplyForm" method="post" action="<?= base_url('admin/send_reply/' . $uniqueUrl) ?>">
            <div class="composer-tabs-row">
                <button type="button" class="composer-tab-btn active" id="tab-public-reply" onclick="switchComposerTab('public')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span>Public Reply</span>
                </button>
                <button type="button" class="composer-tab-btn tab-internal" id="tab-internal-note" onclick="switchComposerTab('internal')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Internal Note (Admin Only)</span>
                </button>
            </div>
            <input type="hidden" name="is_internal" id="composerIsInternal" value="0">

            <textarea name="reply" id="replyInput" class="composer-textarea" placeholder="Tulis tanggapan untuk user atau tekan Shift+Enter untuk baris baru..." required></textarea>

            <div class="composer-bottom-bar">
                <div class="composer-quick-actions">
                    <label class="composer-checkbox-label">
                        <input type="checkbox" name="keep_status_closed" id="cbKeepClosed" value="1" <?= $isClosed ? 'checked' : '' ?> onchange="toggleCloseCheckbox(this)">
                        <span>Mark / keep ticket as <strong>Closed</strong></span>
                    </label>
                </div>

                <!-- Hidden state inputs for reply -->
                <input type="hidden" name="status" id="composerStatus" value="<?= esc($ticket['ticket_status'] ?? 'in_progress') ?>">
                <input type="hidden" name="priority" value="<?= esc($ticket['ticket_priority'] ?? 'medium') ?>">
                <input type="hidden" name="assigned_to" value="<?= esc($assignedSpecialist['id'] ?? '') ?>">

                <button type="submit" class="btn-send-reply-primary">
                    <span>Send Reply</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>
