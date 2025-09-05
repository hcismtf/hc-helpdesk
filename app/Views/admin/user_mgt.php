<?php $active = 'user_mgt'; include(APPPATH . 'Views/admin/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <style>
        body { background: #f5f6fa; font-family: 'Montserrat', Arial, sans-serif; margin: 0; }
        .main-content {
            margin-left: 70px; /* sesuai lebar navbar kiri */
            padding: 30px 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .settings-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 32px;
        }
        .settings-header-row .page-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            
        }
        .settings-header-row .breadcrumb {
            font-size: 15px;
            color: #444;
            margin-bottom: 0;
        }
        .settings-header-right {
            text-align: right;
        }
        .user-welcome {
            font-size: 20px;
            font-weight: 500;
            color: #222;
            margin-bottom: 2px;
        }
        .user-role {
            font-size: 13px;
            color: #444;
        }
        .settings-tabs {
            display: flex;
            position: relative;
            background: transparent;
            border-radius: 40px;
            margin-bottom: 24px;
            gap: 0;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 32px;
            box-sizing: border-box;
        }
        .settings-tab {
            flex: 1;
            background: #ededed;
            border: none;
            outline: none;
            font-size: 1.25rem;
            font-weight: 600;
            color: #222;
            padding: 18px 0;
            border-radius: 40px;
            cursor: pointer;
            transition: background 0.2s;
            position: relative;
            z-index: 2;
            margin: 0 2px;
        }
        .settings-tab.active {
            background: #FFD600;
            color: #222;
        }
        .settings-slider {
            position: absolute;
            top: 0;
            left: 32px; /* sesuai padding kiri settings-tabs */
            height: 100%;
            background: #FFD600;
            border-radius: 40px;
            z-index: 1;
            transition: left 0.2s, width 0.2s;
        }
        .settings-content {
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .tab-content {
            display: none;
            padding: 0;
        }
        .tab-content.active {
            display: block;
        }
        .user-frame {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px 32px 24px 32px;
            margin-bottom: 18px;
            margin-top: 8px;
            width: 100%;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }
        .user-frame-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .user-title {
            font-weight:600; font-size:18px;
        }
        .user-actions {
            display:flex; align-items:center; gap:12px;
        }
        .btn-add-user {
            background: #234be7;
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 8px 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-add-user:hover {
            background: #1a3bb3;
        }
        .user-pagination {
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 4px 12px;
            margin-left: 8px;
            margin-right: 8px;
        }
        table.user-table {
            width:100%; border-collapse:collapse;
        }
        table.user-table th, table.user-table td {
            padding: 14px 10px;
            text-align: left;
        }
        table.user-table th {
            background:#f7f7f7; font-weight:600;
        }
        table.user-table td {
            background:#fff;
            vertical-align: middle;
        }
        .user-edit-btn {
            background: #234be7;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .user-delete-btn {
            background: #7A161C;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
        }
        @media (max-width: 900px) {
            .main-content { margin-left: 54px; padding: 18px 8px; }
            .settings-header-row, .settings-tabs, .settings-content, .user-frame { max-width: 100vw; }
        }
        @media (max-width: 500px) {
            .main-content { margin-left: 0; padding: 4px 2px; }
            .settings-header-row, .settings-tabs, .settings-content, .user-frame { max-width: 100vw; }
            .settings-tabs { padding: 0 4px; }
            .settings-slider { left: 4px; }
        }
        .modal-user {
            position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center;
        }
        .modal-user-content {
            background: #fff; border-radius: 28px; box-shadow: 0 4px 32px rgba(0,0,0,0.10);
            padding: 32px 36px 24px 36px; width: 95vw; max-width: 600px; position: relative;
        }
        .modal-user-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;
        }
        .modal-user-title {
            font-size: 1.35rem; font-weight: 700;
        }
        .modal-user-close {
            font-size: 2rem; font-weight: 400; cursor: pointer; color: #444;
        }
        #addUserForm label {
            display: block; font-weight: 500; margin-bottom: 4px; margin-top: 18px;
        }
        #addUserForm input, #addUserForm select {
            width: 100%; padding: 12px 18px; border-radius: 22px; border: 1px solid #bbb;
            font-size: 1rem; margin-bottom: 2px; background: #fff;
        }
        .modal-user-footer {
            display: flex; justify-content: flex-end; margin-top: 24px;
        }
        .modal-user-submit {
            background: #234be7; color: #fff; border: none; border-radius: 22px;
            padding: 10px 32px; font-size: 1rem; font-weight: 600; cursor: pointer;
        }
        .modal-user-submit:hover {
            background: #1a3bb3;
        }
        .modal-form-group {
            margin-bottom: 12px;
        }
        .modal-form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 15px;
        }
        .modal-user-frame {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            width: 750px;
            padding: 10px;
            border-radius: 20px;
            background: #FFF;
            box-shadow: 0 4px 10px 2px rgba(0, 0, 0, 0.25);
        }

        .modal-form-group {
            width: 100%;
        }

        .modal-label {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 15px;
            display: block;
        }

        .modal-textbox {
            display: flex;
            width: 100%; 
            padding: 15px 10px 15px 15px;
            align-items: center;
            gap: 10px;
            border-radius: 30px;
            border: 1px solid #82868C;
            background: #FFF;
            font-size: 1rem;
            margin-bottom: 2px;
            box-sizing: border-box;
        }
    </style>
</head>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<body>
    <div class="main-content">
        <div class="settings-header-row">
            <div>
                <div class="page-title">User Management</div>
                <div class="breadcrumb">Home &gt; User Management</div>
            </div>
            <div class="settings-header-right">
                <div class="settings-welcome">Welcome, <?= esc(session('username')) ?></div>
            </div>
        </div>
        <div class="settings-tabs" id="settingsTabs">
            <div class="settings-slider" id="settingsSlider"></div>
            <button class="settings-tab active" onclick="showTab(0)">User Management</button>
            <button class="settings-tab" onclick="showTab(1)">User Permissions</button>
        </div>

        <div class="settings-content">
            <div class="tab-content active" id="tab-user-mgt">
                <!-- Konten User Management -->
                <div class="user-frame">
                    <div class="user-frame-header">
                        <div class="user-title">User Management</div>
                        <div class="user-actions">
                            <span style="font-size:15px;">Showing Data</span>
                            <select class="user-pagination">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            <button class="btn-add-user" onclick="openAddUserModal()">Add New User</button>
                        </div>
                    </div>
                    <!-- Modal Add User -->
                    <div id="addUserModal" class="modal-user" style="display:none;">
                        <div class="modal-user-content">
                            <div class="modal-user-header">
                                <span class="modal-user-title">Add New User</span>
                                <span class="modal-user-close" onclick="closeAddUserModal()">&times;</span>
                            </div>
                            <form id="addUserForm" method="post" action="<?= base_url('admin/add_user') ?>">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="Input real name here" required>
                                <label>Email</label>
                                <input type="email" name="email" placeholder="Input active email here" required>
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Input password here" required>
                                <label>Role</label>
                                <select name="role" required>
                                    <option value="">Select User Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="">Select user active / in active</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div class="modal-user-footer">
                                    <button type="submit" class="modal-user-submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal Edit User -->
                    <div id="editUserModal" class="modal-user" style="display:none;">
                        <div class="modal-user-content modal-user-frame">
                            <div class="modal-user-header" style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <span class="modal-user-title">Edit User</span>
                                <span class="modal-user-close" onclick="closeEditUserModal()">&times;</span>
                            </div>
                            <form id="editUserForm" method="post" action="<?= base_url('admin/edit_user') ?>" style="width:100%;">
                                <input type="hidden" name="id" id="edit-user-id">
                                <div class="modal-form-group">
                                    <label class="modal-label">Username</label>
                                    <input type="text" name="username" id="edit-user-username" class="modal-input modal-textbox" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Password <span style="font-weight:normal">(Kosongkan jika tidak ingin ganti)</span></label>
                                    <input type="password" name="password" id="edit-user-password" class="modal-input modal-textbox" placeholder="Input password here">
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Name</label>
                                    <input type="text" name="name" id="edit-user-name" class="modal-input modal-textbox" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Email</label>
                                    <input type="email" name="email" id="edit-user-email" class="modal-input modal-textbox" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Role</label>
                                    <select name="role" id="edit-user-role" class="modal-input modal-textbox" required>
                                        <option value="">Select User Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Status</label>
                                    <select name="status" id="edit-user-status" class="modal-input modal-textbox" required>
                                        <option value="">Select user active / in active</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="modal-user-footer" style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="modal-user-submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php include(APPPATH . 'Views/admin/user_mgt_list.php'); ?>
                </div>
            </div>
            <div class="tab-content" id="tab-user-perm">
                <!-- Konten User Permissions -->
                <div class="user-frame">
                    <div class="user-frame-header">
                        <div class="user-title">User Permissions</div>
                        <div class="user-actions">
                            <span style="font-size:15px;">Showing Data</span>
                            <select class="user-pagination">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            <button class="btn-add-user" onclick="openAddPermissionModal()">Add New Permissions</button>
                        </div>
                        <!-- Modal Add Permission -->
                        <div id="addPermissionModal" class="modal-user" style="display:none;">
                            <div class="modal-user-content">
                                <div class="modal-user-header">
                                    <span class="modal-user-title">Add New Permission</span>
                                    <span class="modal-user-close" onclick="closeAddPermissionModal()">&times;</span>
                                </div>
                                <form id="addPermissionForm" method="post" action="<?= base_url('admin/add_permission') ?>">
                                    <label for="permission-name" style="display:block; font-weight:500; margin-bottom:4px; margin-top:18px;">Permission Name</label>
                                    <input type="text" name="name" id="permission-name" placeholder="Input permission name" required
                                        style="width:100%; padding:12px 18px; border-radius:22px; border:1px solid #bbb; font-size:1rem; margin-bottom:2px; background:#fff;">
                                    <div class="modal-user-footer">
                                        <button type="submit" class="modal-user-submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Modal Edit Permission -->
                        <div id="editPermissionModal" class="modal-user" style="display:none;">
                            <div class="modal-user-content">
                                <div class="modal-user-header">
                                    <span class="modal-user-title">Edit Permission</span>
                                    <span class="modal-user-close" onclick="closeEditPermissionModal()">&times;</span>
                                </div>
                                <form id="editPermissionForm" method="post" action="<?= base_url('admin/edit_permission') ?>">
                                    <input type="hidden" name="id" id="edit-permission-id">
                                    <label for="edit-permission-name" style="display:block; font-weight:500; margin-bottom:4px; margin-top:18px;">Permission Name</label>
                                    <input type="text" name="name" id="edit-permission-name" placeholder="Input permission name" required
                                        style="width:100%; padding:12px 18px; border-radius:22px; border:1px solid #bbb; font-size:1rem; margin-bottom:2px; background:#fff;">
                                    <div class="modal-user-footer">
                                        <button type="submit" class="modal-user-submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Delete Permission -->
                        <div id="deletePermissionModal" class="modal-user" style="display:none;">
                            <div class="modal-user-content">
                                <div class="modal-user-header">
                                    <span class="modal-user-title">Delete Permission</span>
                                    <span class="modal-user-close" onclick="closeDeletePermissionModal()">&times;</span>
                                </div>
                                <div style="margin:24px 0; font-size:1.1rem;">
                                    Apakah Anda yakin ingin menghapus permission <span id="delete-permission-name" style="font-weight:600;"></span>?
                                </div>
                                <div class="modal-user-footer">
                                    <button type="button" class="modal-user-submit" style="background:#7A161C;" onclick="confirmDeletePermission()">Delete</button>
                                    <button type="button" class="modal-user-submit" style="background:#bbb; color:#222; margin-left:12px;" onclick="closeDeletePermissionModal()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabel/daftar permissions di sini -->
                     <?php include(APPPATH . 'Views/admin/user_permissions_list.php'); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        const roleMap = {
            <?php foreach ($roles as $role): ?>
                "<?= $role['id'] ?>": "<?= esc($role['name']) ?>",
            <?php endforeach; ?>
        };
        // Buka modal edit
        function editPermission(id) {
            fetch('<?= base_url('admin/get_permission') ?>?id=' + encodeURIComponent(id))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit-permission-id').value = data.permission.id;
                    document.getElementById('edit-permission-name').value = data.permission.name;
                    document.getElementById('editPermissionModal').style.display = 'flex';
                } else {
                    alert('Gagal mengambil data permission!');
                }
            });
        }
        function closeEditPermissionModal() {
            document.getElementById('editPermissionModal').style.display = 'none';
        }

        // Submit edit
        document.getElementById('editPermissionForm').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const name = form.name.value.trim();
            const id = form.id.value;
            if (!name) return;

            // Generate code baru dari name
            const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', name);
            formData.append('code', code);

            fetch('<?= base_url('admin/edit_permission') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.permission) {
                    closeEditPermissionModal();
                    updatePermissionRow(data.permission);
                    showSuccessConfirm('Permission berhasil diupdate!');
                } else {
                    alert('Gagal update permission!');
                }
            });
        };

        // Buka modal delete
        let deletePermissionId = null;
        function deletePermission(id) {
            fetch('<?= base_url('admin/get_permission') ?>?id=' + encodeURIComponent(id))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deletePermissionId = id;
                    document.getElementById('delete-permission-name').innerText = data.permission.name;
                    document.getElementById('deletePermissionModal').style.display = 'flex';
                } else {
                    alert('Gagal mengambil data permission!');
                }
            });
        }
        function closeDeletePermissionModal() {
            document.getElementById('deletePermissionModal').style.display = 'none';
        }

        // Confirm delete
        function confirmDeletePermission() {
            if (!deletePermissionId) return;
            fetch('<?= base_url('admin/delete_permission') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(deletePermissionId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeletePermissionModal();
                    removePermissionRow(deletePermissionId);
                    showSuccessConfirm('Permission berhasil dihapus!');
                } else {
                    alert('Gagal menghapus permission!');
                }
            });
        }

        // Helper update & remove row
        function updatePermissionRow(permission) {
            const row = document.querySelector(`tr[data-permission-id="${permission.id}"]`);
            if (row) {
                row.querySelector('.permission-code').innerText = permission.code;
                row.querySelector('.permission-name').innerText = permission.name;
                row.querySelector('.permission-created-by').innerText = permission.created_by ?? '-';
                row.querySelector('.permission-created-date').innerText = permission.created_date ?? '-';
            }
        }
        function removePermissionRow(id) {
            const row = document.querySelector(`tr[data-permission-id="${id}"]`);
            if (row) row.remove();
        }
        function openAddPermissionModal() {
            document.getElementById('addPermissionModal').style.display = 'flex';
        }
        function closeAddPermissionModal() {
            document.getElementById('addPermissionModal').style.display = 'none';
        }
        // Optional: close modal on outside click/Escape
        window.onclick = function(event) {
            var modal = document.getElementById('addPermissionModal');
            if (event.target == modal) closeAddPermissionModal();
        };
        window.addEventListener('keydown', function(event) {
            var modal = document.getElementById('addPermissionModal');
            if (event.key === "Escape" && modal.style.display === 'flex') closeAddPermissionModal();
        });

        // Submit Add Permission
        document.getElementById('addPermissionForm').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const name = form.name.value.trim();
            if (!name) return;

            // Generate code: lowercase, replace space with underscore, remove non-alphanumeric
            const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

            const formData = new FormData();
            formData.append('name', name);
            formData.append('code', code);

            fetch('<?= base_url('admin/add_permission') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.permission) {
                    closeAddPermissionModal();
                    addPermissionRow(data.permission);
                    showSuccessConfirm('Permission berhasil ditambahkan!');
                } else {
                    alert('Gagal menambah permission!');
                }
            });
        };

        // Tambahkan row permission ke tabel
        function addPermissionRow(permission) {
            const tbody = document.querySelector('.user-table-permissions tbody');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${permission.code}</td>
                <td>${permission.name}</td>
                <td>${permission.created_by ?? '-'}</td>
                <td>${permission.created_date ?? '-'}</td>
                <td>
                    <button class="user-edit-btn" onclick="editPermission(${permission.id})">Edit</button>
                    <button class="user-delete-btn" onclick="deletePermission(${permission.id})">Delete</button>
                </td>
            `;
            tbody.prepend(tr);
        }
        function showTab(idx) {
            let tabs = document.querySelectorAll('.settings-tab');
            let contents = document.querySelectorAll('.tab-content');
            tabs.forEach((tab, i) => {
                tab.classList.toggle('active', i === idx);
                contents[i].classList.toggle('active', i === idx);
            });
            moveSlider(idx);
        }
        function moveSlider(idx) {
            let tabs = document.querySelectorAll('.settings-tab');
            let slider = document.getElementById('settingsSlider');
            let tab = tabs[idx];
            // Ambil posisi tab relatif terhadap parent (settings-tabs)
            slider.style.width = tab.offsetWidth + 'px';
            slider.style.left = tab.offsetLeft + 'px';
        }
        window.onload = function() { showTab(0); };
        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }
        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }
        // Optional: close modal on outside click
        window.onclick = function(event) {
            var modal = document.getElementById('addUserModal');
            if (event.target == modal) {
                closeAddUserModal();
            }
        }
        window.addEventListener('keydown', function(event) {
            var modal = document.getElementById('addUserModal');
            if (event.key === "Escape" && modal.style.display === 'flex') {
                closeAddUserModal();
            }
        });
        function deleteUser(id) {
            showWarningConfirm('Yakin ingin menghapus user ini?', function() {
                fetch('<?= base_url('admin/delete_user') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessConfirm('User berhasil dihapus!', function() {
                            location.reload();
                        });
                    } else {
                        alert('Gagal menghapus user.');
                    }
                });
            });
        }

        // Helper untuk warning_confirm
        function showWarningConfirm(message, onOk) {
            document.getElementById('global-warning-message').innerText = message;
            document.getElementById('global-warning-confirm-bg').style.display = 'block';
            document.getElementById('global-warning-confirm').style.display = 'block';

            // Remove previous listeners
            let okBtn = document.getElementById('global-warning-ok-btn');
            let cancelBtn = document.getElementById('global-warning-cancel-btn');
            okBtn.onclick = function() {
                document.getElementById('global-warning-confirm-bg').style.display = 'none';
                document.getElementById('global-warning-confirm').style.display = 'none';
                if (onOk) onOk();
            };
            cancelBtn.onclick = function() {
                document.getElementById('global-warning-confirm-bg').style.display = 'none';
                document.getElementById('global-warning-confirm').style.display = 'none';
            };
        }

        // Helper untuk global_success_confirm
        function showSuccessConfirm(message, onOk) {
            document.getElementById('global-success-message').innerText = message;
            document.getElementById('global-success-confirm-bg').style.display = 'block';
            document.getElementById('global-success-confirm').style.display = 'block';

            // Tutup modal dan reload jika OK diklik
            document.getElementById('global-success-confirm-bg').onclick =
            document.getElementById('global-success-confirm').onclick = function() {
                document.getElementById('global-success-confirm-bg').style.display = 'none';
                document.getElementById('global-success-confirm').style.display = 'none';
                if (onOk) onOk();
            };
        }
        function openEditUserModal(id, name, email, roleId, status, username) {
            document.getElementById('edit-user-id').value = id;
            document.getElementById('edit-user-username').value = username || name;
            document.getElementById('edit-user-name').value = name;
            document.getElementById('edit-user-email').value = email;
            document.getElementById('edit-user-role').value = roleId;
            document.getElementById('edit-user-status').value = status;
            document.getElementById('edit-user-password').value = '';
            document.getElementById('editUserModal').style.display = 'flex';
        }
        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
        window.onclick = function(event) {
            var modal = document.getElementById('editUserModal');
            if (event.target == modal) {
                closeEditUserModal();
            }
        }
        window.addEventListener('keydown', function(event) {
            var modal = document.getElementById('editUserModal');
            if (event.key === "Escape" && modal.style.display === 'flex') {
                closeEditUserModal();
            }
        });
        document.getElementById('editUserForm').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch('<?= base_url('admin/edit_user') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update row di tabel user_mgt_list.php
                    updateUserRow(
                        formData.get('id'),
                        formData.get('username'),
                        formData.get('name'),
                        formData.get('email'),
                        formData.get('role'),
                        formData.get('status')
                    );
                    closeEditUserModal();
                    showSuccessConfirm('User berhasil diupdate!');
                } else {
                    alert('Gagal update user!');
                }
            });
        };

        function updateUserRow(id, username, name, email, roleId, status) {
            const row = document.querySelector(`tr[data-user-id="${id}"]`);
            if (row) {
                row.querySelector('.user-username').innerText = username;
                row.querySelector('.user-email').innerText = email;
                row.querySelector('.user-role').innerText =
                    document.getElementById('edit-user-role').selectedOptions[0].text;
                row.querySelector('.user-status').innerText = status;
                // Jika ingin update last_login_time, tambahkan di sini jika datanya tersedia
                // row.querySelector('.user-last-login').innerText = lastLoginTime;
            }
        }
        function openEditUserModal(id) {
            fetch('<?= base_url('admin/get_user') ?>?id=' + encodeURIComponent(id))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    document.getElementById('edit-user-id').value = user.id;
                    document.getElementById('edit-user-username').value = user.username || user.name;
                    document.getElementById('edit-user-name').value = user.name;
                    document.getElementById('edit-user-email').value = user.email;
                    document.getElementById('edit-user-role').value = user.role_id || '';
                    document.getElementById('edit-user-status').value = user.status;
                    document.getElementById('edit-user-password').value = '';
                    document.getElementById('editUserModal').style.display = 'flex';
                } else {
                    alert('Gagal mengambil data user!');
                }
            });
        }
        document.getElementById('addUserForm').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch('<?= base_url('admin/add_user') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddUserModal();
                    addUserRow(data.user); // Tambahkan user baru ke tabel
                    showSuccessConfirm('User berhasil ditambahkan!');
                } else {
                    alert('Gagal menambah user!');
                }
            });
        };
        function addUserRow(user) {
            const tbody = document.querySelector('.user-table tbody');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.setAttribute('data-user-id', user.id);
            tr.innerHTML = `
                <td class="user-username">${user.name}</td>
                <td class="user-email">${user.email}</td>
                <td class="user-role">${roleMap[user.role_id] ?? '-'}</td>
                <td class="user-status">${user.status}</td>
                <td class="user-last-login">${user.last_login_time ?? '-'}</td>
                <td>
                    <button class="user-edit-btn" onclick="openEditUserModal(${user.id})">Edit</button>
                    <button class="user-delete-btn" onclick="deleteUser(${user.id})">Delete</button>
                </td>
            `;
            tbody.prepend(tr);
        }
    </script>
</body>
</html>