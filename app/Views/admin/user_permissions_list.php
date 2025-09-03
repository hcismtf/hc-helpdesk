<?php if (empty($permissions) || count($permissions) === 0): ?>
    <div style="text-align:center; color:#888; padding:32px 0;">
        Belum ada data permissions
    </div>
<?php else: ?>
    <table class="user-table user-table-permissions">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Created By</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissions as $perm): ?>
                <tr data-permission-id="<?= $perm['id'] ?>">
                    <td class="permission-code"><?= esc($perm['code']) ?></td>
                    <td class="permission-name"><?= esc($perm['name']) ?></td>
                    <td class="permission-created-by"><?= esc($perm['created_by']) ?></td>
                    <td class="permission-created-date"><?= esc($perm['created_date']) ?></td>
                    <td>
                        <button class="user-edit-btn" onclick="editPermission(<?= $perm['id'] ?>)">Edit</button>
                        <button class="user-delete-btn" onclick="deletePermission(<?= $perm['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>