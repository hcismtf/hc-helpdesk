<?php if (!empty($types)): ?>
    <table style="width:100%; border-collapse:collapse; margin-top:18px;">
        <thead>
            <tr style="background:#f7f7f7;">
                <th style="padding:16px 8px; font-weight:600; text-align:left;">Name</th>
                <th style="padding:16px 8px; font-weight:600; text-align:left;">Status</th>
                <th style="padding:16px 8px; font-weight:600; text-align:left;">Created By</th>
                <th style="padding:16px 8px; font-weight:600; text-align:left;">Created Date</th>
                <th style="padding:16px 8px; font-weight:600; text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($types as $type): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:14px 8px;"><?= esc($type['name']) ?></td>
                <td style="padding:14px 8px;"><?= esc($type['status']) ?></td>
                <td style="padding:14px 8px;"><?= esc($type['created_by']) ?></td>
                <td style="padding:14px 8px;"><?= esc($type['created_date']) ?></td>
                <td style="padding:14px 8px; text-align:center;">
                    <button class="role-edit-btn"
                        onclick="openRequestTypeEditModal(
                            '<?= $type['id'] ?>',
                            '<?= htmlspecialchars($type['name'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($type['description'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($type['status'], ENT_QUOTES) ?>'
                        )">
                        Edit
                    </button>
                    <button class="role-delete-btn"
                        onclick="openRequestTypeDeleteModal('<?= $type['id'] ?>')">
                        Delete
                    </button>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="pagination" style="margin-top: 20px;">
        <?= isset($paginationHTML) ? $paginationHTML : '' ?>
    </div>
<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada Request Type.</div>
<?php endif ?>