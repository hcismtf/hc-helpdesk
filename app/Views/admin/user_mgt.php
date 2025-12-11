<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/user_mgt.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/error_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/invalid_confirm.php'); ?>
<body>
    <?php $active = 'user_mgt'; include(APPPATH . 'Views/admin/navbar.php'); ?>
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
                            <form id="addUserForm" method="post" action="<?= base_url('admin/add_user') ?>" novalidate>
                                <div class="modal-form-group">
                                    <label class="modal-label">Name</label>
                                    <input type="text" name="name" class="modal-input modal-textbox" placeholder="Input real name here" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Email</label>
                                    <input type="email" name="email" class="modal-input modal-textbox" placeholder="Input active email here" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Password <span style="font-weight:normal;color:#888;font-size:0.98em;">(Min 16 karakter, 1 huruf kapital, 1 special character)</span></label>
                                    <div class="password-input-group">
                                        <input type="password" name="password" id="add-user-password" class="modal-input modal-textbox"
                                            placeholder="Input password here" required minlength="16"
                                            pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$"
                                            title="Minimal 16 karakter, harus mengandung setidaknya 1 huruf kapital dan 1 karakter spesial">
                                        <span class="password-eye" onclick="toggleAddUserPassword()">
                                            <i id="add-eye-icon" class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Role</label>
                                    <select name="role" class="modal-input modal-textbox" required>
                                        <option value="">Select User Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Status</label>
                                    <select name="status" class="modal-input modal-textbox" required>
                                        <option value="">Select user active / in active</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
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
                            <form id="editUserForm" method="post" action="<?= base_url('admin/edit_user') ?>" style="width:100%;" novalidate>
                                <input type="hidden" name="id" id="edit-user-id">
                                <div class="modal-form-group">
                                    <label class="modal-label">Username</label>
                                    <input type="text" name="username" id="edit-user-username" class="modal-input modal-textbox" required>
                                </div>
                                <div class="modal-form-group">
                                    <label class="modal-label">Password <span style="font-weight:normal;color:#888;font-size:0.98em;">(Min 16 karakter, 1 huruf kapital, 1 special character, kosongkan jika tidak ingin ganti)</span></label>
                                    <div class="password-input-group">
                                        <input type="password" name="password" id="edit-user-password" class="modal-input modal-textbox"
                                            placeholder="Input password here"
                                            minlength="16" pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$"
                                            title="Minimal 16 karakter, harus mengandung setidaknya 1 huruf kapital dan 1 karakter spesial">
                                        <span class="password-eye" onclick="toggleEditUserPassword()">
                                            <i id="edit-eye-icon" class="fas fa-eye"></i>
                                        </span>
                                    </div>
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
        function toggleAddUserPassword() {
            const input = document.getElementById('add-user-password');
            const eyeIcon = document.getElementById('add-eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        function toggleEditUserPassword() {
            const input = document.getElementById('edit-user-password');
            const eyeIcon = document.getElementById('edit-eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        const roleMap = {
            <?php foreach ($roles as $role): ?>
                "<?= $role['id'] ?>": "<?= esc($role['name']) ?>",
            <?php endforeach; ?>
        };
        // Buka modal edit
        function editPermission(id) {
            if (!id || id === '' || isNaN(id)) {
                console.error('Invalid permission ID:', id);
                showGlobalInvalid('ID permission tidak valid!');
                return;
            }
            
            fetch('<?= base_url('admin/get_permission') ?>?id=' + encodeURIComponent(id))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.permission) {
                    document.getElementById('edit-permission-id').value = data.permission.id;
                    document.getElementById('edit-permission-name').value = data.permission.name;
                    document.getElementById('editPermissionModal').style.display = 'flex';
                } else {
                    console.error('API returned success=false:', data);
                    showGlobalInvalid('Gagal mengambil data permission!');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
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
            const id = document.getElementById('edit-permission-id').value;
            
            // Validasi
            if (!name) {
                showGlobalInvalid('Permission name tidak boleh kosong!');
                return;
            }
            if (!id || id === '' || isNaN(id)) {
                showGlobalInvalid('ID permission tidak valid!');
                return;
            }

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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.permission) {
                    closeEditPermissionModal();
                    updatePermissionRow(data.permission);
                    showSuccessConfirm('Permission berhasil diupdate!');
                } else {
                    console.error('API returned success=false:', data);
                    showGlobalInvalid('Gagal update permission!');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showGlobalInvalid('Gagal update permission: ' + error.message);
            });
        };

        // Buka modal delete
        let deletePermissionId = null;
        function deletePermission(id) {
            if (!id || id === '' || isNaN(id)) {
                console.error('Invalid permission ID:', id);
                showGlobalInvalid('ID permission tidak valid!');
                return;
            }
            
            fetch('<?= base_url('admin/get_permission') ?>?id=' + encodeURIComponent(id))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.permission) {
                    deletePermissionId = id;
                    document.getElementById('delete-permission-name').innerText = data.permission.name;
                    document.getElementById('deletePermissionModal').style.display = 'flex';
                } else {
                    console.error('API returned success=false:', data);
                    showGlobalInvalid('Gagal mengambil data permission!');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
            });
        }
        function closeDeletePermissionModal() {
            document.getElementById('deletePermissionModal').style.display = 'none';
        }

        // Confirm delete
        function confirmDeletePermission() {
            if (!deletePermissionId || isNaN(deletePermissionId)) {
                showGlobalInvalid('ID permission tidak valid!');
                return;
            }
            
            fetch('<?= base_url('admin/delete_permission') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(deletePermissionId)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeDeletePermissionModal();
                    removePermissionRow(deletePermissionId);
                    showSuccessConfirm('Permission berhasil dihapus!');
                } else {
                    console.error('API returned success=false:', data);
                    showGlobalInvalid('Gagal menghapus permission!');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showGlobalInvalid('Gagal menghapus permission: ' + error.message);
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
            
            if (!name) {
                showGlobalInvalid('Permission name tidak boleh kosong!');
                return;
            }

            // Generate code: lowercase, replace space with underscore, remove non-alphanumeric
            const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

            const formData = new FormData();
            formData.append('name', name);
            formData.append('code', code);

            fetch('<?= base_url('admin/add_permission') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.permission) {
                    closeAddPermissionModal();
                    addPermissionRow(data.permission);
                    form.reset();
                    showSuccessConfirm('Permission berhasil ditambahkan!');
                } else {
                    console.error('API returned success=false:', data);
                    showGlobalInvalid('Gagal menambah permission!');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showGlobalInvalid('Gagal menambah permission: ' + error.message);
            });
        };

        // Tambahkan row permission ke tabel
        function addPermissionRow(permission) {
            const tbody = document.querySelector('.user-table-permissions tbody');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.setAttribute('data-permission-id', permission.id);
            tr.innerHTML = `
                <td class="permission-code">${escapeHtml(permission.code || '')}</td>
                <td class="permission-name">${escapeHtml(permission.name || '')}</td>
                <td class="permission-created-by">${escapeHtml(permission.created_by || '-')}</td>
                <td class="permission-created-date">${escapeHtml(permission.created_date || '-')}</td>
                <td>
                    <button class="user-edit-btn" onclick="editPermission(${permission.id})">Edit</button>
                    <button class="user-delete-btn" onclick="deletePermission(${permission.id})">Delete</button>
                </td>
            `;
            tbody.prepend(tr);
        }
        
        // Escape HTML untuk prevent XSS
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
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
            // Client-side password validation: if password provided, enforce rules
            const pwd = form.password.value || '';
            if (pwd) {
                const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
                if (!pwdPattern.test(pwd)) {
                    if (typeof showGlobalError === 'function') {
                        showGlobalError('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                    } else {
                        alert('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                    }
                    return;
                }
            }
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
            const pwd = form.password.value || '';
            const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
            if (!pwdPattern.test(pwd)) {
                if (typeof showGlobalError === 'function') {
                    showGlobalError('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                } else {
                    alert('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                }
                return;
            }
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
                    // Tampilkan generated UUID
                    const message = 'User berhasil ditambahkan!\n\nGenerated UUID:\n' + data.user.generated_uuid + '\n\nUser dapat login dengan password yang telah diatur.';
                    showSuccessConfirm(message);
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