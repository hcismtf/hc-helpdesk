<?php if (!empty($faqs)): ?>
    <?php foreach ($faqs as $faq): ?>
        <div class="faq-card">
            <div class="faq-question"><?= esc($faq['question']) ?></div>
            <div class="faq-answer"><?= esc($faq['answer']) ?></div>
            <div class="faq-actions-row">
                <button class="faq-edit-btn"
                    onclick="openFaqEditModal('<?= $faq['id'] ?>', '<?= htmlspecialchars($faq['question'], ENT_QUOTES) ?>', '<?= htmlspecialchars($faq['answer'], ENT_QUOTES) ?>')">
                    Edit
                </button>
                <button class="faq-delete-btn" onclick="openFaqDeleteModal('<?= $faq['id'] ?>')">Delete</button>
            </div>
        </div>
    <?php endforeach ?>
    <div class="faq-pagination-row">
        <button class="faq-page-btn" <?= $page <= 1 ? 'disabled' : '' ?> onclick="loadFaqList(<?= $page-1 ?>, <?= $perPage ?>)">&lt;</button>
        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <button class="faq-page-btn <?= $i==$page ? 'active' : '' ?>" onclick="loadFaqList(<?= $i ?>, <?= $perPage ?>)"><?= $i ?></button>
        <?php endfor ?>
        <button class="faq-page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?> onclick="loadFaqList(<?= $page+1 ?>, <?= $perPage ?>)">&gt;</button>
    </div>
<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada FAQ.</div>
<?php endif ?>

<script>
    function loadFaqList(page = 1, perPage = 10) {
        fetch('<?= base_url('admin/get_faq_list') ?>?page=' + page + '&per_page=' + perPage)
        .then(res => res.text())
        .then(html => {
            document.getElementById('faq-list').innerHTML = html;
        });
    }
</script>