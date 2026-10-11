<?php
/**
 * Ticket Detail Component: Activity Timeline
 *
 * Menampilkan tab filter (All, Comments, Audits), dan stream riwayat balasan serta audit logs.
 *
 * @var array $activities    Daftar timeline aktivitas gabungan
 * @var int   $commentsCount Jumlah balasan komentar
 * @var int   $auditCount    Jumlah audit log
 */
?>
<div>
    <div class="activity-section-header">
        <div class="activity-tabs-pill">
            <button type="button" class="activity-tab-btn active" onclick="filterActivity('all', this)">All Activity (<?= count($activities) ?>)</button>
            <button type="button" class="activity-tab-btn" onclick="filterActivity('comments', this)">Comments (<?= $commentsCount ?>)</button>
            <button type="button" class="activity-tab-btn" onclick="filterActivity('internal', this)">Internal Notes (<?= $internalNotesCount ?? 0 ?>)</button>
            <button type="button" class="activity-tab-btn" onclick="filterActivity('audit', this)">Audit Logs (<?= $auditCount ?>)</button>
        </div>
    </div>

    <div class="activity-timeline" id="activityTimeline">
        <?php if (!empty($activities)): ?>
            <?php foreach ($activities as $act): ?>
                <?php if ($act['type'] === 'audit'): ?>
                    <!-- Audit Log Event -->
                    <div class="timeline-event-system activity-item-node" data-type="audit" data-is-internal="0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="17 1 21 5 17 9"></polyline>
                            <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                            <polyline points="7 23 3 19 7 15"></polyline>
                            <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                        </svg>
                        <span><?= esc($act['text']) ?></span>
                        <span style="opacity:0.7;">&bull; <?= esc($act['formatted_date']) ?></span>
                    </div>
                <?php else: ?>
                    <!-- Comment Message Bubble -->
                    <div class="timeline-reply-card activity-item-node <?= !empty($act['is_internal']) ? 'is-internal-bubble' : '' ?>" 
                         data-type="comment" 
                         data-is-internal="<?= !empty($act['is_internal']) ? '1' : '0' ?>">
                        <div class="reply-avatar <?= esc($act['avatar_bg']) ?>"><?= esc($act['initials']) ?></div>
                        <div class="reply-bubble-box">
                            <div class="reply-header-line">
                                <div class="reply-author-group">
                                    <span class="reply-author-name"><?= esc($act['author']) ?></span>
                                    <span class="reply-role-badge <?= esc($act['badge_class']) ?>"><?= esc($act['role_badge']) ?></span>
                                    <?php if (!empty($act['is_internal'])): ?>
                                        <span class="reply-role-badge badge-internal" title="Catatan hanya bisa dilihat oleh Admin/Staff">🔒 Internal Only</span>
                                    <?php endif; ?>
                                </div>
                                <span class="reply-timestamp"><?= esc($act['formatted_date']) ?></span>
                            </div>
                            <div class="reply-body-text">
                                <?= nl2br(esc($act['text'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="settings-empty-state" style="padding:24px 0;">
                <p>No activity or responses yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
