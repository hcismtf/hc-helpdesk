<?php if (!empty($roles)): ?>
    <table style="width:100%; border-collapse:collapse; margin-top:18px;">
        <thead>
            <tr style="background:#f7f7f7;">
                <th style="padding:12px 8px; font-weight:600;">Role Name</th>
                <th style="padding:12px 8px; font-weight:600;">Menu Access</th>
                <th style="padding:12px 8px; font-weight:600;">Users</th>
                <th style="padding:12px 8px; font-weight:600;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
            <tr>
                <td style="padding:10px 8px;"><?= esc($role['name']) ?></td>
                <td style="padding:10px 8px;"><?= esc($role['menu_access']) ?></td>
                <td style="padding:10px 8px;">
                    <?php if (!empty($role['users'])): ?>
                        <?php foreach ($role['users'] as $user): ?>
                            <span style="background:#eee; border-radius:8px; padding:2px 8px; margin-right:4px;">
                                User ID: <?= esc($user['user_id']) ?>
                            </span>
                        <?php endforeach ?>
                    <?php else: ?>
                        <span style="color:#888;">-</span>
                    <?php endif ?>
                </td>
                <td style="padding:10px 8px;">
                    <button class="role-edit-btn"
                        onclick="openRoleEditModal('<?= $role['id'] ?>', <?= json_encode($role['name']) ?>, <?= json_encode($role['menu_access']) ?>)">
                        Edit
                    </button>
                    <button class="role-delete-btn"
                        onclick="openRoleDeleteModal('<?= $role['id'] ?>')">
                        Delete
                    </button>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="faq-pagination-row">
        <button class="faq-page-btn" <?= $page <= 1 ? 'disabled' : '' ?> onclick="loadRoleList(<?= $page-1 ?>, <?= $perPage ?>)">&lt;</button>
        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <button class="faq-page-btn <?= $i==$page ? 'active' : '' ?>" onclick="loadRoleList(<?= $i ?>, <?= $perPage ?>)"><?= $i ?></button>
        <?php endfor ?>
        <button class="faq-page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?> onclick="loadRoleList(<?= $page+1 ?>, <?= $perPage ?>)">&gt;</button>
    </div>
<?php else: ?>
    <div style="text-align:center; color:#888; margin:32px 0;">Belum ada User Role.</div>
<?php endif ?>