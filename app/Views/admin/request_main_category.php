<?php if (!empty($categories)): ?>
    <div class="table-responsive">
        <table class="settings-modern-table">
            <thead>
                <tr>
                    <th style="width: 25%;">MAIN CATEGORY (TOPIK HC)</th>
                    <th style="width: 12%;">CODE</th>
                    <th style="width: 28%;">DESCRIPTION</th>
                    <th style="width: 12%; text-align: center;">SUB CATEGORIES</th>
                    <th style="width: 8%; text-align: center;">ORDER</th>
                    <th style="width: 10%;">STATUS</th>
                    <th style="width: 15%; text-align: center;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:10px; background:<?= esc($cat['color'] ?? '#2563eb') ?>15; color:<?= esc($cat['color'] ?? '#2563eb') ?>; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                                <i class="<?= esc($cat['icon'] ?? 'fa-solid fa-folder') ?>"></i>
                            </div>
                            <div>
                                <span style="display:block; font-weight:600; color:#0f2b5b; font-size:13.5px;"><?= esc($cat['name']) ?></span>
                                <span style="font-size:11px; color:#64748b;">ID: <?= esc(substr($cat['id'], 0, 8)) ?>...</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:11px; font-weight:700; color:#334155; background:#f1f5f9; padding:3px 8px; border-radius:6px; letter-spacing:0.5px;">
                            <?= esc($cat['code'] ?? '-') ?>
                        </span>
                    </td>
                    <td>
                        <span style="font-size:12px; color:#64748b; line-height:1.4; display:block;">
                            <?= !empty($cat['description']) ? esc($cat['description']) : '<span style="color:#94a3b8; font-style:italic;">No description</span>' ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="display:inline-block; font-size:11px; font-weight:600; padding:2px 8px; border-radius:999px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;">
                            <?= (int) ($cat['sub_category_count'] ?? 0) ?> types
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-size:12px; font-weight:600; color:#475569;">
                            <?= (int) ($cat['sort_order'] ?? 0) ?>
                        </span>
                    </td>
                    <td>
                        <?php $isActive = (int) ($cat['is_active'] ?? 1) === 1; ?>
                        <span class="status-pill <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                            <span class="status-dot"></span>
                            <?= $isActive ? 'Active' : 'In Active' ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div class="row-action-buttons">
                            <button type="button" class="btn-icon-action btn-edit-role"
                                onclick="openCategoryEditModal(
                                    '<?= $cat['id'] ?>',
                                    '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($cat['code'] ?? '', ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($cat['color'] ?? '', ENT_QUOTES) ?>',
                                    '<?= (int) ($cat['sort_order'] ?? 0) ?>',
                                    '<?= (int) ($cat['is_active'] ?? 1) ?>',
                                    '<?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES) ?>'
                                )"
                                title="Edit Main Category">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </button>

                            <?= view('components/common/_action_dropdown', [
                                'id' => 'category_action_' . $cat['id'],
                                'items' => [
                                    [
                                        'label' => 'Edit Category',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                        'onClick' => "openCategoryEditModal('" . $cat['id'] . "', '" . htmlspecialchars($cat['name'], ENT_QUOTES) . "', '" . htmlspecialchars($cat['code'] ?? '', ENT_QUOTES) . "', '" . htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES) . "', '" . htmlspecialchars($cat['color'] ?? '', ENT_QUOTES) . "', " . (int) ($cat['sort_order'] ?? 0) . ", " . (int) ($cat['is_active'] ?? 1) . ", '" . htmlspecialchars($cat['description'] ?? '', ENT_QUOTES) . "')"
                                    ],
                                    [
                                        'label' => 'Delete Category',
                                        'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2v2"></path></svg>',
                                        'class' => 'danger',
                                        'onClick' => "openCategoryDeleteModal('" . $cat['id'] . "', " . (int) ($cat['sub_category_count'] ?? 0) . ")"
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

    <!-- Reusable Clean Pagination Footer -->
    <div class="pagination-footer-wrapper">
        <?= isset($paginationHTML) ? $paginationHTML : '' ?>
    </div>

<?php else: ?>
    <div style="padding: 40px; text-align: center; color: #94a3b8;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px;">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
        </svg>
        <p style="font-size: 14px; margin: 0; font-weight: 500;">Belum ada data Main Category (Topik HC).</p>
        <p style="font-size: 12px; margin-top: 4px;">Klik tombol "Add Main Category" untuk membuat pilar kategori baru.</p>
    </div>
<?php endif; ?>
