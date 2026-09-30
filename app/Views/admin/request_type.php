<?php if (!empty($types)): ?>
    <div class="table-responsive">
        <table class="settings-modern-table">
            <thead>
                <tr>
                    <th style="width: 25%;">REQUEST TYPE NAME</th>
                    <th style="width: 35%;">DESCRIPTION</th>
                    <th style="width: 15%;">STATUS</th>
                    <th style="width: 15%;">CREATED AT</th>
                    <th style="width: 10%; text-align: center;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                <tr>
                    <td>
                        <div class="req-type-title">
                            <span class="type-name"><?= esc($type['name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="type-desc"><?= !empty($type['description']) ? esc($type['description']) : '<span style="color:#94a3b8; font-style:italic;">No description provided</span>' ?></span>
                    </td>
                    <td>
                        <?php $isActive = (strtolower($type['status'] ?? '') === 'active'); ?>
                        <span class="status-pill <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                            <span class="status-dot"></span>
                            <?= esc($type['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-cell">
                            <span class="date-text"><?= esc(date('d M Y', strtotime($type['created_date'] ?? 'now'))) ?></span>
                            <span class="by-text">by <?= esc($type['created_by'] ?? 'Admin') ?></span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="row-action-buttons">
                            <button type="button" class="btn-icon-action btn-edit-role"
                                onclick="openRequestTypeEditModal(
                                    '<?= $type['id'] ?>',
                                    '<?= htmlspecialchars($type['name'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($type['description'] ?? '', ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($type['status'], ENT_QUOTES) ?>'
                                )"
                                title="Edit Request Type">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>

                            <?= view('components/action_dropdown', [
                                'id' => 'reqtype_action_' . $type['id'],
                                'items' => [
                                    [
                                        'label' => 'Edit Request Type',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                        'onClick' => "openRequestTypeEditModal('" . $type['id'] . "', '" . htmlspecialchars($type['name'], ENT_QUOTES) . "', '" . htmlspecialchars($type['description'] ?? '', ENT_QUOTES) . "', '" . htmlspecialchars($type['status'], ENT_QUOTES) . "')"
                                    ],
                                    [
                                        'label' => 'Delete Request Type',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2v2"></path></svg>',
                                        'class' => 'danger',
                                        'onClick' => "openRequestTypeDeleteModal('" . $type['id'] . "')"
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
            <path d="M4 6h16M4 12h16M4 18h7"></path>
        </svg>
        <p>Belum ada data Request Type yang terdaftar.</p>
    </div>
<?php endif; ?>