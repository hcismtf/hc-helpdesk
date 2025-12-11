<div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; margin:28px 0 18px 0;">
        <thead>
            <tr style="background:#fff;">
                
                <th style="padding:12px 8px; font-weight:600; text-align:center;">Priority Level</th>
                <th style="padding:12px 8px; font-weight:600; text-align:center;">Response Time (Hour)</th>
                <th style="padding:12px 8px; font-weight:600; text-align:center;">Resolution Time (Hour)</th>
                <th style="padding:12px 8px; font-weight:600; text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($slas)): ?>
                <?php foreach ($slas as $sla): ?>
                    <tr style="background:#fff; border-bottom:1px solid #eee;">
                        
                        <td style="padding:12px 8px; text-align:center;"><?= esc($sla['priority']) ?></td>
                        <td style="padding:12px 8px; text-align:center;"><?= esc($sla['response_time']) ?></td>
                        <td style="padding:12px 8px; text-align:center;"><?= esc($sla['resolution_time']) ?></td>
                        <td style="padding:12px 8px; text-align:center;">
                            <button class="faq-edit-btn"
                                onclick="openSlaEditModal(
                                    '<?= $sla['id'] ?>',
                                    '<?= htmlspecialchars($sla['priority'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($sla['response_time'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($sla['resolution_time'], ENT_QUOTES) ?>'
                                )">
                                Edit
                            </button>
                            <button class="faq-delete-btn"
                                onclick="openSlaDeleteModal('<?= $sla['id'] ?>')">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:#888; padding:32px 0;">Belum ada SLA.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
    <div class="faq-pagination-row" style="text-align:center;">
        <button class="faq-page-btn" <?= $page <= 1 ? 'disabled' : '' ?> onclick="loadSlaList(<?= $page-1 ?>, <?= $perPage ?>)">&lt;</button>
        <?php for ($i=1; $i<=$totalPages; $i++): ?>
            <button class="faq-page-btn <?= $i == $page ? 'active' : '' ?>" onclick="loadSlaList(<?= $i ?>, <?= $perPage ?>)"><?= $i ?></button>
        <?php endfor ?>
        <button class="faq-page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?> onclick="loadSlaList(<?= $page+1 ?>, <?= $perPage ?>)">&gt;</button>
    </div>
    <?php endif ?>
</div>