<h1>Daftar User</h1>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created At</th>
    </tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= esc($u['id']) ?></td>
            <td><?= esc($u['username']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['role']) ?></td>
            <td><?= esc($u['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
