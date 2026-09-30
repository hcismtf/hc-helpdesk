<?php if (!empty($faqs)): ?>
    <div class="faq-accordion-grid">
        <?php foreach ($faqs as $faq): ?>
            <div class="faq-modern-card">
                <div class="faq-card-header">
                    <div class="faq-q-indicator">
                        <span class="faq-q-badge">Q</span>
                        <h4 class="faq-question-text"><?= esc($faq['question']) ?></h4>
                    </div>
                    <div class="row-action-buttons">
                        <button type="button" class="btn-icon-action btn-edit-role"
                            onclick="openFaqEditModal('<?= $faq['id'] ?>', '<?= htmlspecialchars($faq['question'], ENT_QUOTES) ?>', '<?= htmlspecialchars($faq['answer'], ENT_QUOTES) ?>')"
                            title="Edit FAQ">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>

                        <?= view('components/action_dropdown', [
                            'id' => 'faq_action_' . $faq['id'],
                            'items' => [
                                [
                                    'label' => 'Edit FAQ',
                                    'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                                    'onClick' => "openFaqEditModal('" . $faq['id'] . "', '" . htmlspecialchars($faq['question'], ENT_QUOTES) . "', '" . htmlspecialchars($faq['answer'], ENT_QUOTES) . "')"
                                ],
                                [
                                    'label' => 'Delete FAQ',
                                    'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
                                    'class' => 'danger',
                                    'onClick' => "openFaqDeleteModal('" . $faq['id'] . "')"
                                ]
                            ]
                        ]) ?>
                    </div>
                </div>
                <div class="faq-card-body">
                    <div class="faq-answer-text"><?= nl2br(esc($faq['answer'])) ?></div>
                </div>
                <div class="faq-card-footer">
                    <span class="faq-meta-author">By <?= esc($faq['created_by'] ?? 'Admin') ?> &bull; <?= !empty($faq['created_date']) ? esc(date('d M Y', strtotime($faq['created_date']))) : '' ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination-footer-wrapper">
        <?= isset($paginationHTML) ? $paginationHTML : '' ?>
    </div>
<?php else: ?>
    <div class="settings-empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <p>Belum ada artikel FAQ yang terdaftar.</p>
    </div>
<?php endif; ?>