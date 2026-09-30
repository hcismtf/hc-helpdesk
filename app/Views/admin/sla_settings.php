<div class="table-responsive">
    <table class="settings-modern-table">
        <thead>
            <tr>
                <th style="width: 18%;">PRIORITY TIER</th>
                <th style="width: 18%;">TARGET 1ST RESPONSE</th>
                <th style="width: 18%;">TARGET RESOLUTION</th>
                <th style="width: 24%;">ESCALATION TRIGGER</th>
                <th style="width: 12%; text-align: center;">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($slas)): ?>
                <?php foreach ($slas as $sla): ?>
                    <tr>
                        <td>
                            <div class="sla-priority-pill <?= esc($sla['badge_class'] ?? 'tier-medium') ?>">
                                <span class="priority-dot"></span>
                                <span class="priority-title"><?= esc($sla['priority_clean'] ?? $sla['priority']) ?></span>
                                <span class="priority-tier-tag"><?= esc($sla['tier_code'] ?? 'P3') ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="sla-time-cell">
                                <span class="sla-time-val"><?= esc($sla['resp_formatted'] ?? ($sla['response_time'] . ' hours')) ?></span>
                                <span class="sla-time-sub">Response max</span>
                            </div>
                        </td>
                        <td>
                            <div class="sla-time-cell">
                                <span class="sla-time-val"><?= esc($sla['res_formatted'] ?? ($sla['resolution_time'] . ' hours')) ?></span>
                                <span class="sla-time-sub">Resolution max</span>
                            </div>
                        </td>
                        <td>
                            <div class="sla-trigger-cell">
                                <span class="trigger-label"><?= esc($sla['escalation_trigger'] ?? 'Standard ticket queue handling') ?></span>
                                <span class="trigger-sub"><?= esc($sla['business_hours'] ?? 'Standard Workdays') ?></span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div class="row-action-buttons">
                                <button type="button" class="btn-icon-action btn-edit-role"
                                    onclick="openSlaEditModal(
                                        '<?= $sla['id'] ?>',
                                        '<?= htmlspecialchars($sla['priority'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($sla['response_time'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($sla['resolution_time'], ENT_QUOTES) ?>'
                                    )"
                                    title="Edit Policy">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>

                                <?= view('components/action_dropdown', [
                                    'id' => 'sla_action_' . $sla['id'],
                                    'items' => [
                                        [
                                            'label' => 'Edit SLA Policy',
                                            'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                            'onClick' => "openSlaEditModal('" . $sla['id'] . "', '" . htmlspecialchars($sla['priority'], ENT_QUOTES) . "', '" . htmlspecialchars($sla['response_time'], ENT_QUOTES) . "', '" . htmlspecialchars($sla['resolution_time'], ENT_QUOTES) . "')"
                                        ],
                                        [
                                            'label' => 'Delete Policy',
                                            'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
                                            'class' => 'danger',
                                            'onClick' => "openSlaDeleteModal('" . $sla['id'] . "')"
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
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <p>Belum ada konfigurasi SLA Rule yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (isset($totalPages) && $totalPages > 1): ?>
<div class="pagination-footer-wrapper">
    <div class="faq-pagination-row">
        <button class="faq-page-btn" <?= ($page ?? 1) <= 1 ? 'disabled' : '' ?> onclick="loadSlaList(<?= ($page ?? 1) - 1 ?>, <?= $perPage ?? 10 ?>)">&lt;</button>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button class="faq-page-btn <?= $i == ($page ?? 1) ? 'active' : '' ?>" onclick="loadSlaList(<?= $i ?>, <?= $perPage ?? 10 ?>)"><?= $i ?></button>
        <?php endfor; ?>
        <button class="faq-page-btn" <?= ($page ?? 1) >= $totalPages ? 'disabled' : '' ?> onclick="loadSlaList(<?= ($page ?? 1) + 1 ?>, <?= $perPage ?? 10 ?>)">&gt;</button>
    </div>
</div>
<?php endif; ?>