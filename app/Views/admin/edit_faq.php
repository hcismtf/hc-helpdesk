<div class="faq-frame" style="margin-bottom:24px;">
    <h2>Edit FAQ</h2>
    <form action="<?= base_url('admin/edit_faq') ?>" method="post" style="display:flex; flex-direction:column; gap:14px;">
        <input type="hidden" name="id" value="<?= esc($editFaq['id']) ?>">
        <label for="question">Title <span style="color:#FF474A">*</span></label>
        <input type="text" id="question" name="question" value="<?= esc($editFaq['question']) ?>" required>
        <label for="answer">Description <span style="color:#FF474A">*</span></label>
        <textarea id="answer" name="answer" required><?= esc($editFaq['answer']) ?></textarea>
        <div style="width:100%; display:flex; justify-content:flex-end; gap:12px;">
            <button type="button" onclick="window.location.href='<?= base_url('admin/System_settings?tab=faq-management') ?>'" class="btn-submit-faq" style="background:#bbb;">Cancel</button>
            <button type="submit" class="btn-submit-faq">Update</button>
        </div>
    </form>
</div>