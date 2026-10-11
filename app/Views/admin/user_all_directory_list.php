<div class="table-responsive">
    <table class="settings-modern-table">
        <thead>
            <tr>
                <th style="width: 25%;">NAMA LENGKAP & EMPLOYEE NO</th>
                <th style="width: 20%;">EMAIL</th>
                <th style="width: 20%;">POSISI</th>
                <th style="width: 14%;">POSITION LEVEL</th>
                <th style="width: 11%;">ROLE SYSTEM</th>
                <th style="width: 10%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($allUsers)): ?>
                <?php foreach ($allUsers as $u): ?>
                    <tr>
                        <td>
                            <div class="role-scope-cell">
                                <div class="role-title-row">
                                    <span class="user-username role-name-text" style="font-weight:600; color:#0f2b5b;"><?= esc($u['name'] ?? '-') ?></span>
                                    <?php if (!empty($u['employee_no'])): ?>
                                        <span class="nip-badge-small" style="font-size:11px; font-weight:700; color:#4f46e5; background:#eef2ff; padding:2px 8px; border-radius:6px; margin-left:6px;">NIP: <?= esc($u['employee_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="role-desc-text" style="font-size:11.5px; color:#64748b; margin-top:2px;">
                                    Last login: <?= !empty($u['last_login_time']) ? esc(date('d M Y H:i', strtotime($u['last_login_time']))) : '<span style="color:#94a3b8; font-style:italic;">Never</span>' ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: #475569; font-weight: 500;">
                                <?= esc($u['email'] ?? '-') ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 13px; font-weight: 600; color: #1e293b;">
                                <?= esc($u['position'] ?? '-') ?>
                            </div>
                            <?php if (!empty($u['job_title']) && $u['job_title'] !== ($u['position'] ?? '')): ?>
                                <div style="font-size: 11.5px; color: #64748b;">
                                    <?= esc($u['job_title']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($u['position_level'])): ?>
                                <span style="display:inline-block; font-size: 11.5px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.3px;">
                                    <?= esc($u['position_level']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-style:italic; font-size:12px;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($u['role_name'])): ?>
                                <span class="role-badge role-policy user-role">
                                    <?= esc($u['role_name']) ?>
                                </span>
                            <?php else: ?>
                                <span style="display:inline-block; font-size:11px; color:#94a3b8; background:#f1f5f9; padding:2px 7px; border-radius:4px; font-style:italic;">
                                    No Role Assigned
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $isActive = (strtolower($u['status'] ?? '') === 'active'); ?>
                            <span class="status-pill <?= $isActive ? 'status-active' : 'status-inactive' ?> user-status">
                                <span class="status-dot"></span>
                                <?= esc(ucfirst($u['status'] ?? 'Active')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="settings-empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <p>Belum ada data user di database.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modern Pagination -->
<?php if (!empty($allUsersPaginationHTML)): ?>
    <div class="settings-table-pagination">
        <?= $allUsersPaginationHTML ?>
    </div>
<?php endif; ?>
