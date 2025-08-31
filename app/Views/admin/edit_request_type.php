<div class="faq-frame" style="margin-bottom:24px;">
    <h2>Edit FAQ</h2>
    <form action="<?= base_url('admin/update_faq_frame') ?>" method="post" style="display:flex; flex-direction:column; gap:14px;" target="_self" onsubmit="setTimeout(function(){window.parent.postMessage('faqEditDone','*');},500);">
        <input type="hidden" name="id" value="<?= esc($faq['id']) ?>">
        <label for="question">Title <span style="color:#FF474A">*</span></label>
        <input type="text" id="question" name="question" value="<?= esc($faq['question']) ?>" required>
        <label for="answer">Description <span style="color:#FF474A">*</span></label>
        <textarea id="answer" name="answer" required><?= esc($faq['answer']) ?></textarea>
        <div style="width:100%; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn-submit-faq">Update</button>
        </div>
    </form>
</div>