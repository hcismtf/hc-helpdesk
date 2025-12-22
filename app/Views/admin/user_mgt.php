<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/user_mgt.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        var baseUrl = '<?= base_url() ?>';
        var roleMap = {
            <?php foreach ($roles as $role): ?>
                "<?= $role['id'] ?>": "<?= esc($role['name']) ?>",
            <?php endforeach; ?>
        };
    </script>
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
                                    <div class="modal-form-group">
                                        <label for="permission-name" class="modal-label">Permission Name</label>
                                        <input type="text" name="name" id="permission-name" placeholder="Input permission name" required class="modal-input modal-textbox">
                                    </div>
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
                                    <div class="modal-form-group">
                                        <label for="edit-permission-name" class="modal-label">Permission Name</label>
                                        <input type="text" name="name" id="edit-permission-name" placeholder="Input permission name" required class="modal-input modal-textbox">
                                    </div>
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
    <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
    <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin/user_mgt_clean.js') ?>"></script>
</body>
</html>