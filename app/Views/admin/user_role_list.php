<?php if (!empty($roles)): ?>
    <div class="table-responsive">
        <table class="settings-modern-table">
            <thead>
                <tr>
                    <th style="width: 28%;">ROLE & SCOPE</th>
                    <th style="width: 38%;">AUTHORIZED MODULES</th>
                    <th style="width: 22%;">ASSIGNED MEMBERS</th>
                    <th style="width: 12%; text-align: center;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td>
                        <div class="role-scope-cell">
                            <div class="role-title-row">
                                <span class="role-name-text"><?= esc($role['name']) ?></span>
                                <span class="role-badge <?= esc(strtolower(str_replace(' ', '-', $role['badge_type'] ?? 'custom-role'))) ?>">
                                    <?= esc($role['badge_type'] ?? 'CUSTOM ROLE') ?>
                                </span>
                            </div>
                            <div class="role-desc-text">
                                <?= esc($role['scope_desc'] ?? 'Custom access controls.') ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="modules-chip-container">
                            <?php if (!empty($role['permissions'])): ?>
                                <?php foreach ($role['permissions'] as $p): ?>
                                    <span class="module-chip chip-<?= esc(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p['name']))) ?>">
                                        <?= esc($p['name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="module-chip chip-none">No Access</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="assigned-members-cell">
                            <?php if (!empty($role['members'])): ?>
                                <div class="member-avatar-stack">
                                    <?php 
                                    $displayed = array_slice($role['members'], 0, 3);
                                    $overflow = count($role['members']) - count($displayed);
                                    $palette = ['avatar-indigo', 'avatar-teal', 'avatar-emerald', 'avatar-amber'];
                                    foreach ($displayed as $idx => $m): 
                                        $colorClass = $palette[$idx % count($palette)];
                                    ?>
                                        <div class="member-avatar <?= $colorClass ?>" title="<?= esc($m['name']) ?>">
                                            <?= esc($m['initials']) ?>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if ($overflow > 0): ?>
                                        <div class="member-avatar avatar-overflow" title="<?= $overflow ?> more members">
                                            +<?= $overflow ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="member-count-label"><?= $role['members_count'] ?> <?= $role['members_count'] === 1 ? 'member' : 'members' ?></span>
                            <?php else: ?>
                                <span class="member-empty-label">0 members</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="row-action-buttons">
                            <!-- Quick Edit Button (Matches Reference Icon) -->
                            <button type="button" class="btn-icon-action btn-edit-role" 
                                data-id="<?= $role['id'] ?>" 
                                data-name="<?= htmlspecialchars($role['name'], ENT_QUOTES) ?>" 
                                data-permissions="<?= htmlspecialchars(json_encode($role['permission_ids'] ?? []), ENT_QUOTES) ?>"
                                onclick="openRoleEditModalFromButton(this)"
                                title="Edit Role">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>

                            <!-- Reusable Action Dropdown Component (Matches 3-Dots Menu) -->
                            <?= view('components/action_dropdown', [
                                'id' => 'role_action_' . $role['id'],
                                'items' => [
                                    [
                                        'label' => 'Edit Permissions',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                        'attributes' => 'data-id="' . $role['id'] . '" data-name="' . htmlspecialchars($role['name'], ENT_QUOTES) . '" data-permissions="' . htmlspecialchars(json_encode($role['permission_ids'] ?? []), ENT_QUOTES) . '"',
                                        'onClick' => 'openRoleEditModalFromButton(this)'
                                    ],
                                    [
                                        'label' => 'Delete Role',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
                                        'class' => 'danger',
                                        'onClick' => "openRoleDeleteModal('" . $role['id'] . "')"
                                    ]
                                ]
                            ]) ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-footer-wrapper">
        <?= isset($paginationHTML) ? $paginationHTML : '' ?>
    </div>
<?php else: ?>
    <div class="settings-empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <p>Belum ada data User Role yang terdaftar.</p>
    </div>
<?php endif; ?>