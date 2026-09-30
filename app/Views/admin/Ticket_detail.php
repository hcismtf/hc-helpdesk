<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#<?= esc($ticket['id']) ?> - <?= esc($ticket['subject'] ?? 'Ticket Detail') ?> | Helpdesk</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/Ticket_detail.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php 
        if (session('isLoggedIn')) {
            $active = 'tickets'; 
            include('navbar.php'); 
        }
        $uniqueUrl = basename(current_url());
    ?>

    <div class="main-content" id="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => 'Ticket #' . $ticket['id'],
            'pageTitle'        => 'Ticket Details',
            'showCreateTicket' => false,
            'showNotif'        => true,
        ]) ?>

        <div class="ticket-detail-page">
            <!-- Command & Breadcrumb Strip -->
            <div class="detail-top-bar">
                <div class="detail-breadcrumb-group">
                    <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="btn-back-queue">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Back to Ticket Queue</span>
                        <span class="kbd-esc">ESC</span>
                    </a>
                </div>

                <div class="detail-actions-right">
                    <!-- Quick Assign Me -->
                    <button type="button" class="btn-command" onclick="quickAssignToMe('<?= session('user_id') ?>')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                        <span>Assign to Me</span>
                    </button>

                    <!-- Status Pill Trigger -->
                    <?php 
                        $statusKey = strtolower($ticket['ticket_status'] ?? 'open');
                    ?>
                    <div class="status-select-pill status-<?= esc($statusKey) ?>">
                        <span class="status-dot"></span>
                        <span>Status: <strong><?= ucwords(str_replace('_', ' ', $statusKey)) ?></strong></span>
                    </div>

                    <!-- Escalate Button -->
                    <button type="button" class="btn-command btn-escalate" onclick="openEscalateModal()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                        <span>Escalate</span>
                    </button>

                    <!-- Reusable Action Dropdown Menu -->
                    <?= view('components/action_dropdown', [
                        'id' => 'detail_top_action',
                        'items' => [
                            [
                                'label' => 'Change Ticket Status',
                                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>',
                                'onClick' => 'showReplyStatusModal()'
                            ],
                            [
                                'label' => 'Print / Export PDF',
                                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>',
                                'onClick' => 'window.print()'
                            ]
                        ]
                    ]) ?>
                </div>
            </div>

            <!-- 2-Column Main Layout Grid -->
            <div class="ticket-detail-grid">
                <!-- =============================================================
                     LEFT MAIN COLUMN
                     ============================================================= -->
                <div class="ticket-main-column">
                    <!-- 1. Ticket Headline Banner -->
                    <div class="ticket-headline-card">
                        <div class="ticket-meta-top">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="meta-dev-tag">DEV-<?= esc($ticket['id']) ?></span>
                                <span class="meta-source-text">Created via Helpdesk Portal</span>
                            </div>
                            <span class="meta-time-text">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Updated <?= esc($lifecycle['updated_ago']) ?>
                            </span>
                        </div>

                        <h1 class="ticket-subject-title">#<?= esc($ticket['id']) ?> - <?= esc($ticket['subject'] ?? 'No Subject') ?></h1>

                        <div class="ticket-badge-strip">
                            <!-- Status Badge -->
                            <span class="pill-badge status-select-pill status-<?= esc($statusKey) ?>">
                                <?= ucwords(str_replace('_', ' ', $statusKey)) ?>
                            </span>

                            <!-- Priority Badge -->
                            <?php 
                                $prioKey = strtolower($ticket['ticket_priority'] ?? 'medium');
                            ?>
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
                            <span class="pill-badge pill-<?= esc($sla['badge_class']) ?>">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <?= esc($sla['badge_text']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- 2. Original Incident Description Card -->
                    <div class="incident-description-card">
                        <div class="incident-card-header">
                            <div class="incident-header-left">
                                <div class="incident-icon-pill">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="incident-title-text">Original Incident Description</h3>
                                    <span class="incident-sub-text"><?= esc($lifecycle['created']) ?> WIB &bull; Reported via Web Portal</span>
                                </div>
                            </div>
                            <span class="incident-tag-code">INC-<?= esc($ticket['id']) ?></span>
                        </div>

                        <div class="incident-body-content">
                            <?= !empty($ticket['message']) ? nl2br(esc($ticket['message'])) : '<span style="color:#94a3b8; font-style:italic;">No description provided.</span>' ?>
                        </div>

                        <!-- Attachments Block -->
                        <?php if (!empty($attachments)): ?>
                        <div class="attachments-wrapper">
                            <div class="attachments-header-row">
                                <span class="attachments-count-title">Attachments (<?= count($attachments) ?> Files)</span>
                            </div>
                            <div class="attachments-cards-grid">
                                <?php foreach ($attachments as $att): ?>
                                    <div class="attachment-file-card">
                                        <div class="attachment-file-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                <polyline points="13 2 13 9 20 9"></polyline>
                                            </svg>
                                        </div>
                                        <div class="attachment-file-info">
                                            <div class="attachment-file-name" title="<?= esc($att['file_name']) ?>"><?= esc($att['file_name']) ?></div>
                                            <div class="attachment-file-meta">Attachment File</div>
                                            <div class="attachment-card-links">
                                                <a href="javascript:void(0);" onclick="showAttachmentModal('<?= base_url('uploads/images-attachment/' . $att['file_path']) ?>')">Preview</a>
                                                <span>&bull;</span>
                                                <a href="<?= base_url('uploads/images-attachment/' . $att['file_path']) ?>" download="<?= esc($att['file_name']) ?>">Download</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Activity & Chat Timeline -->
                    <div>
                        <div class="activity-section-header">
                            <div class="activity-tabs-pill">
                                <button type="button" class="activity-tab-btn active" onclick="filterActivity('all', this)">All Activity (<?= count($activities) ?>)</button>
                                <button type="button" class="activity-tab-btn" onclick="filterActivity('comments', this)">Comments (<?= $commentsCount ?>)</button>
                                <button type="button" class="activity-tab-btn" onclick="filterActivity('audit', this)">Audit Logs (<?= $auditCount ?>)</button>
                            </div>
                        </div>

                        <div class="activity-timeline" id="activityTimeline">
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $act): ?>
                                    <?php if ($act['type'] === 'audit'): ?>
                                        <!-- Audit Log Event -->
                                        <div class="timeline-event-system activity-item-node" data-type="audit">
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
                                        <div class="timeline-reply-card activity-item-node" data-type="comment">
                                            <div class="reply-avatar <?= esc($act['avatar_bg']) ?>"><?= esc($act['initials']) ?></div>
                                            <div class="reply-bubble-box">
                                                <div class="reply-header-line">
                                                    <div class="reply-author-group">
                                                        <span class="reply-author-name"><?= esc($act['author']) ?></span>
                                                        <span class="reply-role-badge <?= esc($act['badge_class']) ?>"><?= esc($act['role_badge']) ?></span>
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

                    <!-- 4. Modern Reply Composer Card -->
                    <?php 
                        $isClosed = ($ticket['ticket_status'] === 'closed');
                        $isAdmin = session('isLoggedIn');
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
                                    <button type="button" class="composer-tab-btn active" id="tab-public-reply">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        <span>Public Reply</span>
                                    </button>
                                </div>

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
                </div>

                <!-- =============================================================
                     RIGHT SIDEBAR: Ticket Properties & Requester Profile
                     ============================================================= -->
                <div class="ticket-sidebar-column">
                    <!-- Card 1: Ticket Properties -->
                    <div class="sidebar-property-card">
                        <div class="sidebar-card-title">
                            <span>Ticket Properties</span>
                            <button type="button" class="btn-icon-action" onclick="showReplyStatusModal()" title="Edit Properties">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Assigned Specialist -->
                        <div class="property-group-block">
                            <div class="property-label-small">Assigned Specialist</div>
                            <div class="specialist-profile-row">
                                <div class="specialist-user-info">
                                    <div class="specialist-avatar"><?= esc($assignedSpecialist['initials']) ?></div>
                                    <div>
                                        <div class="specialist-name"><?= esc($assignedSpecialist['name']) ?></div>
                                        <div class="specialist-sub"><?= esc($assignedSpecialist['role_title']) ?></div>
                                    </div>
                                </div>
                                <button type="button" class="btn-reassign-link" onclick="showReplyStatusModal()">Reassign</button>
                            </div>
                        </div>

                        <!-- Priority Level -->
                        <div class="property-group-block">
                            <div class="property-label-small">Priority Level</div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span class="status-dot" style="background:#ea580c;"></span>
                                <strong style="font-size:13px; color:#0f172a;"><?= ucfirst(esc($ticket['ticket_priority'] ?? 'Medium')) ?></strong>
                            </div>
                        </div>

                        <!-- Classification / Req Type -->
                        <div class="property-group-block">
                            <div class="property-label-small">Classification</div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                </svg>
                                <span style="font-size:13px; font-weight:600; color:#334155;"><?= esc($ticket['req_type'] ?? 'General Support') ?></span>
                            </div>
                        </div>

                        <!-- SLA Resolution Progress Card -->
                        <div class="property-group-block">
                            <div class="sla-progress-card-block">
                                <div class="sla-progress-top-row">
                                    <span class="sla-resolution-title">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        SLA Resolution
                                    </span>
                                    <span class="sla-percent-badge"><?= $sla['is_compliant'] ? '100% COMPLIANT' : 'BREACHED' ?></span>
                                </div>
                                <div class="sla-progress-track">
                                    <div class="sla-progress-fill" style="width: <?= esc($sla['percentage']) ?>%; background: <?= $sla['is_compliant'] ? '#10b981' : '#ef4444' ?>;"></div>
                                </div>
                                <div class="sla-progress-footer-row">
                                    <span>Elapsed: <strong><?= esc($sla['elapsed_formatted']) ?></strong></span>
                                    <span>Target: <strong><?= esc($sla['target_formatted']) ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Lifecycle Tracking -->
                        <div class="property-group-block">
                            <div class="property-label-small">Lifecycle Tracking</div>
                            <div class="lifecycle-list">
                                <div class="lifecycle-item">
                                    <span class="lifecycle-item-label">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        Created
                                    </span>
                                    <span class="lifecycle-item-val"><?= esc($lifecycle['created']) ?></span>
                                </div>
                                <div class="lifecycle-item">
                                    <span class="lifecycle-item-label">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        First Response
                                    </span>
                                    <span class="lifecycle-item-val"><?= esc($lifecycle['first_response']) ?></span>
                                </div>
                                <div class="lifecycle-item">
                                    <span class="lifecycle-item-label">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Resolved & Closed
                                    </span>
                                    <span class="lifecycle-item-val"><?= esc($lifecycle['resolved']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Requester Profile -->
                    <div class="sidebar-property-card">
                        <div class="sidebar-card-title">
                            <span>Requester Profile</span>
                            <span class="pill-badge" style="background:#eef2ff; color:#4f46e5; font-size:11px;">Internal Staff</span>
                        </div>

                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
                            <div class="requester-avatar-large"><?= esc($requester['initials']) ?></div>
                            <div>
                                <h4 style="margin:0 0 2px 0; font-size:15px; font-weight:800; color:#0f172a;"><?= esc($requester['name']) ?></h4>
                                <span style="font-size:12px; color:#64748b;"><?= esc($requester['position']) ?></span>
                                <div style="font-size:11px; font-family:monospace; color:#4f46e5; margin-top:2px;">ID: NIP-<?= esc($requester['nip']) ?></div>
                            </div>
                        </div>

                        <!-- Email Box -->
                        <div class="requester-email-box">
                            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($requester['email']) ?></span>
                            <button type="button" style="border:none; background:transparent; cursor:pointer; color:#64748b;" onclick="navigator.clipboard.writeText('<?= esc($requester['email']) ?>')" title="Copy Email">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </div>

                        <div class="lifecycle-list" style="margin-bottom:14px;">
                            <div class="lifecycle-item">
                                <span class="lifecycle-item-label">Department</span>
                                <span class="lifecycle-item-val"><?= esc($requester['department']) ?></span>
                            </div>
                            <div class="lifecycle-item">
                                <span class="lifecycle-item-label">Location</span>
                                <span class="lifecycle-item-val"><?= esc($requester['location']) ?></span>
                            </div>
                        </div>

                        <div style="display:flex; gap:8px;">
                            <a href="mailto:<?= esc($requester['email']) ?>" class="btn-contact-action">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span>Send Email</span>
                            </a>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $requester['phone']) ?>" target="_blank" class="btn-contact-action">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODALS: Status / Reassign Update Modal & Image Preview Modal
         ========================================================================= -->
    <!-- Modal Update Properties / Reassign -->
    <div id="replyStatusModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Update Ticket Properties</span>
                <button type="button" class="modal-close-btn" onclick="closeReplyStatusModal()">&times;</button>
            </div>

            <form id="replyStatusForm" method="post" action="<?= base_url('admin/send_reply/' . $uniqueUrl) ?>">
                <?php if (session('isLoggedIn')): ?>
                    <!-- Status -->
                    <div class="modal-form-group">
                        <label class="modal-form-label">Ticket Status <span style="color:#ef4444">*</span></label>
                        <?= view('components/basic_select', [
                            'name'          => 'status',
                            'id'            => 'modal_status_select',
                            'placeholder'   => 'Select Ticket Status',
                            'selectedValue' => $ticket['ticket_status'] ?? 'open',
                            'options'       => [
                                'open'        => 'Open',
                                'in_progress' => 'In Progress',
                                'closed'      => 'Closed'
                            ],
                            'required'      => true
                        ]) ?>
                    </div>

                    <!-- Priority -->
                    <div class="modal-form-group">
                        <label class="modal-form-label">Priority Level <span style="color:#ef4444">*</span></label>
                        <?= view('components/basic_select', [
                            'name'          => 'priority',
                            'id'            => 'modal_priority_select',
                            'placeholder'   => 'Select Priority Level',
                            'selectedValue' => $ticket['ticket_priority'] ?? 'medium',
                            'options'       => [
                                'low'    => 'Low Priority',
                                'medium' => 'Medium Priority',
                                'high'   => 'High Priority',
                                'urgent' => 'Urgent Priority'
                            ],
                            'required'      => true
                        ]) ?>
                    </div>

                    <!-- Assigned Specialist -->
                    <div class="modal-form-group">
                        <label class="modal-form-label">Assign To Specialist <span style="color:#ef4444">*</span></label>
                        <?php
                            $userOptions = [];
                            if (!empty($users)) {
                                foreach ($users as $u) {
                                    $userOptions[$u['id']] = $u['name'];
                                }
                            }
                        ?>
                        <?= view('components/basic_select', [
                            'name'          => 'assigned_to',
                            'id'            => 'modal_assigned_select',
                            'placeholder'   => 'Select Specialist',
                            'selectedValue' => $ticket['assigned_to'] ?? '',
                            'options'       => $userOptions,
                            'required'      => true
                        ]) ?>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="status" value="<?= esc($ticket['ticket_status']) ?>">
                    <input type="hidden" name="priority" value="<?= esc($ticket['ticket_priority']) ?>">
                <?php endif; ?>

                <div class="modal-form-group">
                    <label class="modal-form-label">Activity Note / Update Message <span style="color:#ef4444">*</span></label>
                    <textarea name="reply" class="modal-form-textarea" required placeholder="Describe reason for property update or action note..."></textarea>
                </div>

                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeReplyStatusModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Update & Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Popup Attachment Preview -->
    <div id="attachmentModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal" style="max-width:850px; text-align:center;">
            <div class="modal-header-clean">
                <span class="modal-title-clean" id="attachmentModalFilename">Attachment Preview</span>
                <button type="button" class="modal-close-btn" onclick="closeAttachmentModal()">&times;</button>
            </div>
            <img id="attachmentModalImg" src="" alt="Attachment" style="max-width:100%; max-height:70vh; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        </div>
    </div>

    <!-- JavaScript Handlers -->
    <script>
        function showReplyStatusModal() {
            document.getElementById('replyStatusModal').style.display = 'flex';
        }
        function closeReplyStatusModal() {
            document.getElementById('replyStatusModal').style.display = 'none';
        }

        function showAttachmentModal(src) {
            document.getElementById('attachmentModalImg').src = src;
            document.getElementById('attachmentModal').style.display = 'flex';
        }
        function closeAttachmentModal() {
            document.getElementById('attachmentModal').style.display = 'none';
        }

        function toggleCloseCheckbox(cb) {
            var composerStatus = document.getElementById('composerStatus');
            if (cb.checked) {
                composerStatus.value = 'closed';
            } else {
                composerStatus.value = 'in_progress';
            }
        }

        function filterActivity(type, btn) {
            document.querySelectorAll('.activity-tab-btn').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var items = document.querySelectorAll('.activity-item-node');
            items.forEach(function(item) {
                if (type === 'all') {
                    item.style.display = 'flex';
                } else if (item.getAttribute('data-type') === type) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function quickAssignToMe(myUserId) {
            if (!myUserId) return;
            var assignedInput = document.getElementById('modal_assigned_select');
            if (assignedInput) assignedInput.value = myUserId;
            showReplyStatusModal();
        }

        function openEscalateModal() {
            var prioInput = document.getElementById('modal_priority_select');
            if (prioInput) prioInput.value = 'urgent';
            showReplyStatusModal();
        }

        // Action Dropdown Handler (Reusable Dropdown Component)
        function toggleActionDropdown(e, id) {
            e.stopPropagation();
            var wrapper = document.getElementById('wrapper_' + id);
            if (!wrapper) return;
            var wasOpen = wrapper.classList.contains('is-open');
            closeAllActionDropdowns();
            if (!wasOpen) wrapper.classList.add('is-open');
        }

        function closeAllActionDropdowns() {
            document.querySelectorAll('.action-dropdown-wrapper.is-open').forEach(function(el) {
                el.classList.remove('is-open');
            });
        }

        // Reusable Custom Single Select Dropdown Handler
        function toggleCustomSelect(e, id) {
            e.stopPropagation();
            var wrapper = document.getElementById('select_wrapper_' + id);
            if (!wrapper) return;
            var wasOpen = wrapper.classList.contains('is-open');
            closeAllCustomSelects();
            if (!wasOpen) wrapper.classList.add('is-open');
        }

        function selectCustomOption(id, val, label, onChangeCallback) {
            var input = document.getElementById(id);
            var labelEl = document.getElementById('select_label_' + id);
            var trigger = document.getElementById('select_trigger_' + id);
            var menu = document.getElementById('select_menu_' + id);

            if (input) input.value = val;
            if (labelEl) labelEl.innerText = label;

            if (trigger) {
                if (val !== '') trigger.classList.add('has-value');
                else trigger.classList.remove('has-value');
            }

            if (menu) {
                menu.querySelectorAll('.custom-select-option').forEach(function(opt) {
                    if (opt.getAttribute('data-value') === val) opt.classList.add('is-selected');
                    else opt.classList.remove('is-selected');
                });
            }

            closeAllCustomSelects();

            if (onChangeCallback && typeof window[onChangeCallback] === 'function') {
                window[onChangeCallback](val, label);
            }
        }

        function closeAllCustomSelects() {
            document.querySelectorAll('.custom-select-wrapper.is-open').forEach(function(el) {
                el.classList.remove('is-open');
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-wrapper')) closeAllActionDropdowns();
            if (!e.target.closest('.custom-select-wrapper')) closeAllCustomSelects();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                closeAllActionDropdowns();
                closeAllCustomSelects();
                closeReplyStatusModal();
                closeAttachmentModal();
            }
        });
    </script>
</body>
</html>