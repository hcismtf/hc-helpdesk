
    <?php if (!empty($roles)): ?>
        <table style="width:100%; border-collapse:collapse; margin-top:18px;">
            <thead>
                <tr style="background:#f7f7f7;">
                    <th style="padding:12px 8px; font-weight:600;">Role Name</th>
                    <th style="padding:12px 8px; font-weight:600;">Menu Access</th>
                    <th style="padding:12px 8px; font-weight:600;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td style="padding:10px 8px; text-align:center;"><?= esc($role['name']) ?></td>
                    <td style="padding:10px 8px; text-align:center;"><?= esc($role['menu_access']) ?></td>
                    <td style="padding:10px 8px; text-align:center;">
                        <button class="role-edit-btn" 
                            data-id="<?= $role['id'] ?>" 
                            data-name="<?= htmlspecialchars($role['name'], ENT_QUOTES) ?>" 
                            data-permissions="<?= htmlspecialchars(json_encode($role['permission_ids'] ?? []), ENT_QUOTES) ?>"
                            onclick="openRoleEditModalFromButton(this)">
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
        <!-- Pagination -->
        <div class="pagination" style="margin-top: 20px;">
            <?= isset($paginationHTML) ? $paginationHTML : '' ?>
        </div>
    <?php else: ?>
        <div style="text-align:center; color:#888; margin:32px 0;">Belum ada User Role.</div>
    <?php endif ?>
</div>