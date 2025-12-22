<?php if (!empty($faqs)): ?>
        <?php foreach ($faqs as $faq): ?>
            <div style="background:#fff; border-radius:20px; box-shadow:0 2px 16px rgba(0,0,0,0.08); padding:30px; margin-bottom:32px; word-wrap: break-word; overflow-wrap: break-word;">
                <div style="font-size:1.35rem; font-weight:600; text-align:left; margin-bottom:18px; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">
                    <?= esc($faq['question']) ?>
                </div>
                <div style="font-size:1rem; color:#222; text-align:left; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; line-height: 1.5;">
                    <?= esc($faq['answer']) ?>
                </div>
            </div>
        <?php endforeach ?>

<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada FAQ.</div>
<?php endif ?>