<?php if (!empty($faqs)): ?>
        <?php foreach ($faqs as $faq): ?>
            <div style="background:#fff; border-radius:22px; box-shadow:0 2px 16px rgba(0,0,0,0.08); padding:38px 32px; margin-bottom:32px;">
                <div style="font-size:1.35rem; font-weight:600; text-align:center; margin-bottom:18px;">
                    <?= esc($faq['question']) ?>
                </div>
                <div style="font-size:1rem; color:#222; text-align:center;">
                    <?= esc($faq['answer']) ?>
                </div>
            </div>
        <?php endforeach ?>

<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada FAQ.</div>
<?php endif ?>