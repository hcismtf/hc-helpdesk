<?php if (!empty($faqs)): ?>
    <?php foreach ($faqs as $faq): ?>
        <div class="faq-card">
            <div class="faq-question"><?= esc($faq['question']) ?></div>
            <div class="faq-answer"><?= esc($faq['answer']) ?></div>
            <div class="faq-actions-row">
                <button class="faq-edit-btn" onclick="openFaqEditModal('<?= esc($faq['id']) ?>', '<?= htmlspecialchars($faq['question'], ENT_QUOTES) ?>', '<?= htmlspecialchars($faq['answer'], ENT_QUOTES) ?>')">
                    Edit
                </button>
                <button class="faq-delete-btn" onclick="openFaqDeleteModal('<?= $faq['id'] ?>')">Delete</button>
            </div>
        </div>
    <?php endforeach ?>
    <!-- Pagination -->
    <div class="pagination" style="margin-top: 20px;">
        <?= isset($paginationHTML) ? $paginationHTML : '' ?>
    </div>
<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada FAQ.</div>
<?php endif ?>