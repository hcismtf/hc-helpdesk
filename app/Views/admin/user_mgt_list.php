<?php
// Buat mapping id ke name
$roleMap = [];
if (!empty($roles)) {
    foreach ($roles as $role) {
        $roleMap[$role['id']] = $role['name'];
    }
}
?>
<div class="table-responsive">
    <table class="settings-modern-table">
        <thead>
            <tr>
                <th style="width: 28%;">USER PROFILE</th>
                <th style="width: 25%;">EMAIL</th>
                <th style="width: 18%;">ROLE</th>
                <th style="width: 14%;">STATUS</th>
                <th style="width: 15%; text-align: center;">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr data-user-id="<?= $user['id'] ?>">
                        <td>
                            <div class="role-scope-cell">
                                <div class="role-title-row">
                                    <span class="user-username role-name-text"><?= esc($user['name']) ?></span>
                                    <?php if (!empty($user['employee_no'])): ?>
                                        <span class="nip-badge-small" style="font-size:11px; font-weight:700; color:#4f46e5; background:#eef2ff; padding:2px 8px; border-radius:6px; margin-left:6px;">NIP: <?= esc($user['employee_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="role-desc-text">
                                    Last login: <?= !empty($user['last_login_time']) ? esc(date('d M Y H:i', strtotime($user['last_login_time']))) : '<span style="color:#94a3b8; font-style:italic;">Never</span>' ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="user-email" style="font-size: 13px; color: #475569; font-weight: 500;">
                                <?= esc($user['email']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-badge role-policy user-role">
                                <?= esc($user['role_name'] ?? $roleMap[$user['role_id']] ?? 'Standard User') ?>
                            </span>
                        </td>
                        <td>
                            <?php $isActive = (strtolower($user['status'] ?? '') === 'active'); ?>
                            <span class="status-pill <?= $isActive ? 'status-active' : 'status-inactive' ?> user-status">
                                <span class="status-dot"></span>
                                <?= esc(ucfirst($user['status'] ?? 'Active')) ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div class="row-action-buttons">
                                <button type="button" class="btn-icon-action btn-edit-role"
                                    onclick="openEditUserModal(
                                        <?= $user['id'] ?>,
                                        '<?= esc($user['name'], 'js') ?>',
                                        '<?= esc($user['email'], 'js') ?>',
                                        '<?= esc($user['role_id'] ?? '', 'js') ?>',
                                        '<?= esc($user['status'], 'js') ?>'
                                    )"
                                    title="Edit User">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>

                                <?= view('components/common/_action_dropdown', [
                                    'id' => 'user_action_' . $user['id'],
                                    'items' => [
                                        [
                                            'label' => 'Edit User Profile',
                                            'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                            'onClick' => "openEditUserModal(" . $user['id'] . ", '" . esc($user['name'], 'js') . "', '" . esc($user['email'], 'js') . "', '" . esc($user['role_id'] ?? '', 'js') . "', '" . esc($user['status'], 'js') . "')"
                                        ],
                                        [
                                            'label' => 'Delete User',
                                            'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
                                            'class' => 'danger',
                                            'onClick' => "deleteUser(" . $user['id'] . ")"
                                        ]
                                    ]
                                ]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <div class="settings-empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <p>Belum ada user yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modern Pagination -->
<?php if (!empty($paginationHTML)): ?>
    <div class="settings-table-pagination">
        <?= $paginationHTML ?>
    </div>
<?php endif; ?>