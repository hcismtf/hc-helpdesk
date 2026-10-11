<?php
/**
 * Ticket Detail Component: Properties Sidebar
 *
 * Menampilkan informasi properties tiket (Assigned Specialist, Priority, Classification, SLA Resolution, Lifecycle Tracking)
 * dan profil pemohon tiket (Requester Profile, kontak email & whatsapp).
 *
 * @var array $ticket             Data tiket utama
 * @var array $assignedSpecialist Data specialist yang ditugaskan
 * @var array $sla                Data metrik SLA
 * @var array $lifecycle          Data riwayat lifecycle
 * @var array $requester          Data profil requester
 */
?>
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

        <!-- Assigned PIC Helpdesk -->
        <div class="property-group-block">
            <div class="property-label-small">PIC Helpdesk</div>
            <div class="specialist-profile-row">
                <div class="specialist-user-info">
                    <div class="specialist-avatar"><?= esc($picHelpdesk['initials'] ?? $assignedSpecialist['initials']) ?></div>
                    <div>
                        <div class="specialist-name"><?= esc($picHelpdesk['name'] ?? $assignedSpecialist['name']) ?></div>
                        <div class="specialist-sub"><?= esc($picHelpdesk['role_title'] ?? 'PIC Helpdesk') ?></div>
                    </div>
                </div>
                <button type="button" class="btn-reassign-link" onclick="showReplyStatusModal()">Reassign</button>
            </div>
        </div>

        <!-- Assigned PIC Developer / BA (Optional) -->
        <div class="property-group-block">
            <div class="property-label-small">PIC Developer / BA <span style="font-weight:400; color:#94a3b8; font-size:11px;">(Opsional)</span></div>
            <div class="specialist-profile-row">
                <div class="specialist-user-info">
                    <div class="specialist-avatar" style="background:#0284c7;"><?= esc($picDevBa['initials'] ?? '--') ?></div>
                    <div>
                        <div class="specialist-name"><?= esc($picDevBa['name'] ?? 'Unassigned') ?></div>
                        <div class="specialist-sub"><?= esc($picDevBa['role_title'] ?? 'Developer / BA') ?></div>
                    </div>
                </div>
                <button type="button" class="btn-reassign-link" onclick="showReplyStatusModal()"><?= !empty($picDevBa['is_assigned']) ? 'Reassign' : 'Assign' ?></button>
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
            <div class="sla-progress-card-block" 
                 id="slaProgressCard"
                 data-created-ts="<?= esc($sla['created_ts'] ?? 0) ?>"
                 data-target-ts="<?= esc($sla['target_ts'] ?? 0) ?>"
                 data-resolved-ts="<?= esc($sla['resolved_ts'] ?? 0) ?>"
                 data-is-finished="<?= !empty($sla['is_finished']) ? '1' : '0' ?>"
                 data-resolution-hours="<?= esc($sla['resolution_hours'] ?? 24) ?>">
                <div class="sla-progress-top-row">
                    <span class="sla-resolution-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        SLA Resolution
                    </span>
                    <span class="sla-percent-badge" id="slaPercentBadge"><?= $sla['is_compliant'] ? '100% COMPLIANT' : 'BREACHED' ?></span>
                </div>
                <div class="sla-progress-track">
                    <div class="sla-progress-fill" id="slaProgressFill" style="width: <?= esc($sla['percentage']) ?>%; background: <?= $sla['is_compliant'] ? '#10b981' : '#ef4444' ?>;"></div>
                </div>
                <div class="sla-progress-footer-row">
                    <span>Elapsed: <strong id="slaElapsedText" class="<?= $sla['is_compliant'] ? 'sla-time-compliant' : 'sla-time-overdue' ?>"><?= esc($sla['elapsed_formatted']) ?></strong></span>
                    <span>Target: <strong class="sla-time-compliant"><?= esc($sla['target_formatted']) ?></strong></span>
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
