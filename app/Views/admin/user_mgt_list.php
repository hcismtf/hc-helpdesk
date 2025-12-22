<?php
// Buat mapping id ke name
$roleMap = [];
if (!empty($roles)) {
    foreach ($roles as $role) {
        $roleMap[$role['id']] = $role['name'];
    }
}
?>
<table class="user-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr data-user-id="<?= $user['id'] ?>">
                    <td class="user-username"><?= esc($user['name']) ?></td>
                    <td class="user-email"><?= esc($user['email']) ?></td>
                    <td class="user-role"><?= esc($roleMap[$user['role_id']] ?? '-') ?></td>
                    <td class="user-status"><?= esc($user['status']) ?></td>
                    <td class="user-last-login"><?= esc($user['last_login_time'] ?? '-') ?></td>
                    <td>
                        <button class="user-edit-btn" onclick="openEditUserModal('<?= esc($user['id'], 'js') ?>')">Edit</button>
                        <button class="user-delete-btn" onclick="deleteUser('<?= esc($user['id'], 'js') ?>')">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; color:#888;">Belum ada list users</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>