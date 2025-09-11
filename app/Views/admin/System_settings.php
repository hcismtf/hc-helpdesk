<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/invalid_confirm.php'); ?>
<?php
$menuAccessRaw = session('menu_access');
$menuAccess = [];
if (is_string($menuAccessRaw) && strlen($menuAccessRaw) > 0) {
    $menuAccess = array_map('trim', explode(',', $menuAccessRaw));
}
function hasMenuAccess($menuName) {
    global $menuAccess;
    return in_array($menuName, $menuAccess) || session('role') === 'superadmin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>">
</head>
<body>
    <?php $active = 'settings'; include('navbar.php'); ?>
    <div class="main-content">
        <div class="page-title">System Settings</div>
        <div class="breadcrumb">Home &gt; System Settings</div>
        <div class="settings-header-row">
            <div></div>
            <div>
                <div class="settings-welcome">Welcome, <?= esc(session('username')) ?></div>
            </div>
        </div>
        <div class="settings-tabs" id="settingsTabs">
            <div class="settings-slider" id="settingsSlider"></div>
            <button class="settings-tab active" onclick="moveSlider(0)">FAQ Management</button>
            <button class="settings-tab" onclick="moveSlider(1)">User Roles</button>
            <button class="settings-tab" onclick="moveSlider(2)">Request Type</button>
            <button class="settings-tab" onclick="moveSlider(3)">SLA Settings</button>
        </div>
        <div id="faq-management-content" style="display:block;">
            <div class="faq-frame">
                <div class="faq-frame-header">
                    <div class="faq-title">FAQ Management</div>
                    <div class="faq-actions">
                        <span style="font-size:15px;">Showing Data</span>
                        <select class="faq-pagination">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <button class="btn-add-faq">Add New FAQ</button>
                    </div>
                </div>
                <!-- Modal Add FAQ -->
                <div id="faqModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-size:20px; font-weight:600;">Add New FAQ</div>
                            <span onclick="closeFaqModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                        </div>
                        <form id="faq-form" style="display:flex; flex-direction:column; align-items:flex-start; gap:10px; align-self:stretch; width:100%; padding-top:10px;">
                            <label for="faq-title" style="font-weight:500;">Title <span style="color:#FF474A">*</span></label>
                            <input id="faq-title" type="text" placeholder="Input Title Here" class="faq-input">
                            <label for="faq-desc" style="font-weight:500;">Description <span style="color:#FF474A">*</span></label>
                            <textarea id="faq-desc" placeholder="Input Description Here" class="faq-textarea"></textarea>
                            <div style="width:100%; display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn-submit-faq">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Modal Delete FAQ -->
                <div id="faqDeleteModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal" style="max-width:400px; align-items:center;">
                        <div style="font-size:20px; font-weight:600; margin-bottom:12px;">Hapus FAQ?</div>
                        <div style="font-size:15px; color:#444; margin-bottom:18px; text-align:center;">
                            Apakah Anda yakin ingin menghapus FAQ ini?<br>Data yang dihapus tidak dapat dikembalikan.
                        </div>
                        <input type="hidden" id="delete-faq-id">
                        <div style="display:flex; gap:12px; justify-content:center; width:100%;">
                            <button onclick="closeFaqDeleteModal()" style="background:#bbb; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Batal</button>
                            <button onclick="confirmDeleteFaq()" style="background:#7A161C; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Hapus</button>
                        </div>
                    </div>
                </div>
                <!-- Modal Edit FAQ -->
                <div id="faqEditModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-size:20px; font-weight:600;">Edit FAQ</div>
                            <span onclick="closeFaqEditModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                        </div>
                        <form id="faq-edit-form" style="display:flex; flex-direction:column; align-items:flex-start; gap:10px; align-self:stretch; width:100%; padding-top:10px;">
                            <input type="hidden" id="edit-faq-id">
                            <label for="edit-faq-title" style="font-weight:500;">Title <span style="color:#FF474A">*</span></label>
                            <input id="edit-faq-title" type="text" placeholder="Input Title Here" class="faq-input">
                            <label for="edit-faq-desc" style="font-weight:500;">Description <span style="color:#FF474A">*</span></label>
                            <textarea id="edit-faq-desc" placeholder="Input Description Here" class="faq-textarea"></textarea>
                            <div style="width:100%; display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn-submit-faq">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="faq-success">FAQ berhasil ditambahkan!</div>
                <div id="faq-list">
                    <?php if (!empty($faqs)): ?>
                        <?php foreach ($faqs as $faq): ?>
                            <div class="faq-card">
                                <div class="faq-question"><?= esc($faq['question']) ?></div>
                                <div class="faq-answer"><?= esc($faq['answer']) ?></div>
                                <div class="faq-actions-row">
                                    <button class="faq-edit-btn"
                                        onclick="openFaqEditModal('<?= $faq['id'] ?>', '<?= htmlspecialchars($faq['question'], ENT_QUOTES) ?>', '<?= htmlspecialchars($faq['answer'], ENT_QUOTES) ?>')">
                                        Edit
                                    </button>
                                    <button class="faq-delete-btn" onclick="openFaqDeleteModal('<?= $faq['id'] ?>')">Delete</button>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="faq-pagination-row">
                            <button class="faq-page-btn" <?= $page <= 1 ? 'disabled' : '' ?> onclick="loadFaqList(<?= $page-1 ?>, <?= $perPage ?>)">&lt;</button>
                            <?php for ($i=1; $i<=$totalPages; $i++): ?>
                                <button class="faq-page-btn <?= $i == $page ? 'active' : '' ?>" onclick="loadFaqList(<?= $i ?>, <?= $perPage ?>)"><?= $i ?></button>
                            <?php endfor ?>
                            <button class="faq-page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?> onclick="loadFaqList(<?= $page+1 ?>, <?= $perPage ?>)">&gt;</button>
                        </div>
                    <?php else: ?>
                        <div style="text-align:center; color:#888; margin:32px 0;">Belum ada FAQ.</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <!-- User Roles -->
        <div id="user-roles-content" style="display:none;">
            <div class="faq-frame">
                <div class="faq-frame-header">
                    <div class="faq-title">User Role Management</div>
                    <div class="faq-actions">
                        <span style="font-size:15px;">Showing Data</span>
                        <select class="faq-pagination" onchange="loadRoleList(1, this.value)">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <button class="btn-add-faq" style="background:#234be7;">Add New User Role</button>
                    </div>
                    <!-- Modal Add User Role -->
                    <div id="userRoleModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:600px;">
                            <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Add New User Role</div>
                                <span onclick="closeUserRoleModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="user-role-form" style="display:flex; flex-direction:column; gap:14px; width:100%; padding-top:10px;">
                                <label for="role-name" style="font-weight:500;">Role Name <span style="color:#FF474A">*</span></label>
                                <input id="role-name" type="text" placeholder="Input Title Here" class="faq-input">

                                <label style="font-weight:500;">Permission</label>
                                <div id="permission-container">
                                    <div class="permission-row" style="display:flex; align-items:center; gap:8px;">
                                        <select name="permissions[]" class="faq-input permission-select" style="flex:1; margin-bottom:8px;">
                                            <option value="">Select Permission</option>
                                            <?php foreach ($permissions as $perm): ?>
                                                <option value="<?= esc($perm['id']) ?>"><?= esc($perm['name']) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <button type="button" class="remove-permission-btn" style="display:none; background:#bbb; color:#fff; border:none; border-radius:12px; padding:6px 12px; font-size:15px; font-weight:600; cursor:pointer; margin-bottom:8px;">-</button>
                                    </div>
                                </div>
                                <button type="button" id="add-permission-btn" style="background:#eee; color:#222; border:none; border-radius:22px; padding:6px 18px; font-size:15px; font-weight:600; cursor:pointer; margin-bottom:8px;">
                                    + Add Permission
                                </button>
                                <div style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn-submit-faq">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal Edit User Role -->
                    <div id="userRoleEditModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:600px;">
                            <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Edit User Role</div>
                                <span onclick="closeUserRoleEditModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="user-role-edit-form" style="display:flex; flex-direction:column; gap:14px; width:100%; padding-top:10px;">
                                <input type="hidden" id="edit-role-id">
                                <label for="edit-role-name" style="font-weight:500;">Role Name <span style="color:#FF474A">*</span></label>
                                <input id="edit-role-name" type="text" class="faq-input">
                                <label style="font-weight:500;">Permission</label>
                                <div id="edit-permission-container"></div>
                                <button type="button" id="add-edit-permission-btn" style="background:#eee; color:#222; border:none; border-radius:22px; padding:6px 18px; font-size:15px; font-weight:600; cursor:pointer; margin-bottom:8px;">
                                    + Add Permission
                                </button>
                                <div style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn-submit-faq">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal Delete User Role -->
                    <div id="userRoleDeleteModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:400px; align-items:center;">
                            <div style="font-size:20px; font-weight:600; margin-bottom:12px;">Hapus User Role?</div>
                            <div style="font-size:15px; color:#444; margin-bottom:18px; text-align:center;">
                                Apakah Anda yakin ingin menghapus User Role ini?<br>Data yang dihapus tidak dapat dikembalikan.
                            </div>
                            <input type="hidden" id="delete-role-id">
                            <div style="display:flex; gap:12px; justify-content:center; width:100%;">
                                <button onclick="closeUserRoleDeleteModal()" style="background:#bbb; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Batal</button>
                                <button onclick="confirmDeleteUserRole()" style="background:#7A161C; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="user-role-list"></div>
            </div>
        </div>
        <div id="request-type-content" style="display:none;">
            <!-- Request Type content here -->
             <div class="faq-frame">
                <div class="faq-frame-header">
                    <div class="faq-title">Request Type</div>
                    <div class="faq-actions">
                        <span style="font-size:15px;">Showing Data</span>
                        <select class="faq-pagination" onchange="loadRequestTypeList(1, this.value)">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <button class="btn-add-faq" style="background:#234be7;">Add New Request Type</button>
                    </div>
                    <!-- Modal Add New Request Type -->
                    <div id="requestTypeModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:600px;">
                            <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Add New Request Type</div>
                                <span onclick="closeRequestTypeModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="request-type-form" style="display:flex; flex-direction:column; gap:14px; width:100%; padding-top:10px;">
                                <label for="request-type-name" style="font-weight:500;">Request Type Name <span style="color:#FF474A">*</span></label>
                                <input id="request-type-name" type="text" placeholder="Input request type name" class="faq-input">

                                <label for="request-type-desc" style="font-weight:500;">Description</label>
                                <textarea id="request-type-desc" placeholder="Input description" class="faq-textarea"></textarea>

                                <label for="request-type-status" style="font-weight:500;">Status</label>
                                <select id="request-type-status" class="faq-input">
                                    <option value="Active">Active</option>
                                    <option value="In Active">In Active</option>
                                </select>
                                <div style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn-submit-faq">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal Edit Request Type -->
                    <div id="requestTypeEditModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:600px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Edit Request Type</div>
                                <span onclick="closeRequestTypeEditModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="request-type-edit-form" style="display:flex; flex-direction:column; gap:14px; width:100%; padding-top:10px;">
                                <input type="hidden" id="edit-request-type-id">
                                <label for="edit-request-type-name">Request Type Name</label>
                                <input id="edit-request-type-name" type="text" class="faq-input">
                                <label for="edit-request-type-desc">Description</label>
                                <textarea id="edit-request-type-desc" class="faq-textarea"></textarea>
                                <label for="edit-request-type-status">Status</label>
                                <select id="edit-request-type-status" class="faq-input">
                                    <option value="Active">Active</option>
                                    <option value="In Active">In Active</option>
                                </select>
                                <div style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn-submit-faq">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal Delete Request Type -->
                    <div id="requestTypeDeleteModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal" style="max-width:400px; align-items:center;">
                            <div style="font-size:20px; font-weight:600; margin-bottom:12px;">Hapus Request Type?</div>
                            <div style="font-size:15px; color:#444; margin-bottom:18px; text-align:center;">
                                Apakah Anda yakin ingin menghapus Request Type ini?<br>Data yang dihapus tidak dapat dikembalikan.
                            </div>
                            <input type="hidden" id="delete-request-type-id">
                            <div style="display:flex; gap:12px; justify-content:center; width:100%;">
                                <button onclick="closeRequestTypeDeleteModal()" style="background:#bbb; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Batal</button>
                                <button onclick="confirmDeleteRequestType()" style="background:#7A161C; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="request-type-list"></div>
            </div>
        </div>
        <!-- SLA Settings -->
        <div id="sla-settings-content" style="display:none;">
            <div class="faq-frame">
                <div class="faq-frame-header">
                    <div class="faq-title">SLA Settings</div>
                    <div class="faq-actions">
                        <span style="font-size:15px;">Showing Data</span>
                        <select class="faq-pagination">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <button class="btn-add-faq" style="background:#FCD116; color:#222;" id="btn-add-sla">Add New SLA</button>
                    </div>
                </div>
                <!-- Modal Add SLA -->
                <div id="slaModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-size:20px; font-weight:600;">Add New SLA</div>
                            <span onclick="closeSlaModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                        </div>
                        <form id="sla-form" style="display:flex; flex-direction:column; gap:10px; width:100%; padding-top:10px;">
                            
                            <label for="sla-priority" style="font-weight:500;">Priority Level <span style="color:#FF474A">*</span></label>
                            <select id="sla-priority" class="faq-input" required>
                                <option value="">Select Priority</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                            <label for="sla-response" style="font-weight:500;">Response Time (hours) <span style="color:#FF474A">*</span></label>
                            <select id="sla-response" class="faq-input" required>
                                <option value="">Select Response Time</option>
                                <?php for($i=1; $i<=48; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor ?>
                            </select>
                            <label for="sla-resolution" style="font-weight:500;">Resolution Time (hours) <span style="color:#FF474A">*</span></label>
                            <select id="sla-resolution" class="faq-input" required>
                                <option value="">Select Resolution Time</option>
                                <?php for($i=1; $i<=168; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor ?>
                            </select>
                            <div style="width:100%; display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn-submit-faq" style="background:#FCD116; color:#222;">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Modal Edit SLA -->
                <div id="slaEditModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal" style="max-width:600px;">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-size:20px; font-weight:600;">Edit SLA</div>
                            <span onclick="closeSlaEditModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                        </div>
                        <form id="sla-edit-form" style="display:flex; flex-direction:column; gap:14px; width:100%; padding-top:10px;">
                            <input type="hidden" id="edit-sla-id">
                            <label for="edit-sla-priority" style="font-weight:500;">Priority Level <span style="color:#FF474A">*</span></label>
                            <select id="edit-sla-priority" class="faq-input" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                            <label for="edit-sla-response" style="font-weight:500;">Response Time (hours) <span style="color:#FF474A">*</span></label>
                            <select id="edit-sla-response" class="faq-input" required>
                                <?php for($i=1; $i<=48; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor ?>
                            </select>
                            <label for="edit-sla-resolution" style="font-weight:500;">Resolution Time (hours) <span style="color:#FF474A">*</span></label>
                            <select id="edit-sla-resolution" class="faq-input" required>
                                <?php for($i=1; $i<=168; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor ?>
                            </select>
                            <div style="width:100%; display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn-submit-faq" style="background:#234be7;">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Delete SLA -->
                <div id="slaDeleteModal" class="faq-modal-bg" style="display:none;">
                    <div class="faq-modal" style="max-width:400px; align-items:center;">
                        <div style="font-size:20px; font-weight:600; margin-bottom:12px;">Hapus SLA?</div>
                        <div style="font-size:15px; color:#444; margin-bottom:18px; text-align:center;">
                            Apakah Anda yakin ingin menghapus SLA ini?<br>Data yang dihapus tidak dapat dikembalikan.
                        </div>
                        <input type="hidden" id="delete-sla-id">
                        <div style="display:flex; gap:12px; justify-content:center; width:100%;">
                            <button onclick="closeSlaDeleteModal()" style="background:#bbb; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Batal</button>
                            <button onclick="confirmDeleteSla()" style="background:#7A161C; color:#fff; border:none; border-radius:12px; padding:8px 24px; font-size:15px; font-weight:600; cursor:pointer;">Hapus</button>
                        </div>
                    </div>
                </div>
                <div id="sla-list"></div>
            </div>
        </div>
        <script>
            function validateRoleForm(formId, nameId, permissionContainerId, submitBtnSelector) {
                var name = document.getElementById(nameId).value.trim();
                var selects = document.querySelectorAll('#' + permissionContainerId + ' select');
                var permissions = [];
                selects.forEach(function(sel) {
                    if (sel.value) permissions.push(sel.value);
                });

                // Regex hanya huruf dan spasi
                var nameValid = /^[A-Za-z ]+$/.test(name);

                // Enable/disable submit button
                var submitBtn = document.querySelector(submitBtnSelector);
                if (!name || !nameValid || permissions.length === 0) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            }

            // Event listener untuk Add User Role
            document.getElementById('role-name').addEventListener('input', function() {
                validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
            });
            document.getElementById('permission-container').addEventListener('change', function() {
                validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
            });
            document.getElementById('add-permission-btn').addEventListener('click', function() {
                setTimeout(function() {
                    validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
                }, 100);
            });

            // Event listener untuk Edit User Role
            document.getElementById('edit-role-name').addEventListener('input', function() {
                validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
            });
            document.getElementById('edit-permission-container').addEventListener('change', function() {
                validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
            });
            document.getElementById('add-edit-permission-btn').addEventListener('click', function() {
                setTimeout(function() {
                    validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
                }, 100);
            });

            // Validasi sebelum submit (Add)
            document.getElementById('user-role-form').onsubmit = function(e) {
                e.preventDefault();
                var name = document.getElementById('role-name').value.trim();
                var selects = document.querySelectorAll('#permission-container select');
                var permissions = [];
                selects.forEach(function(sel) {
                    if (sel.value) permissions.push(sel.value);
                });
                var nameValid = /^[A-Za-z ]+$/.test(name);

                if (!name || permissions.length === 0) {
                    showGlobalWarning('Role name dan minimal satu permission wajib diisi!', null, null);
                    return;
                }
                if (!nameValid) {
                    showGlobalInvalid('Role Name hanya boleh huruf dan spasi!');
                    return;
                }

                fetch('<?= base_url('admin/add_user_role') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'name=' + encodeURIComponent(name)
                        + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('role-name').value = '';
                        document.querySelectorAll('#permission-container select').forEach(function(sel, idx){
                            if(idx === 0) sel.value = '';
                            else sel.remove();
                        });
                        closeUserRoleModal();
                        showGlobalSuccess('User Role berhasil ditambahkan!');
                        loadRoleList();
                    }
                });
            };

            // Validasi sebelum submit (Edit)
            document.getElementById('user-role-edit-form').onsubmit = function(e) {
                e.preventDefault();
                var id = document.getElementById('edit-role-id').value;
                var name = document.getElementById('edit-role-name').value.trim();
                var selects = document.querySelectorAll('#edit-permission-container select');
                var permissions = [];
                selects.forEach(function(sel) {
                    if (sel.value) permissions.push(sel.value);
                });
                var nameValid = /^[A-Za-z ]+$/.test(name);

                if (!name || permissions.length === 0) {
                    showGlobalWarning('Role name dan minimal satu permission wajib diisi!', null, null);
                    return;
                }
                if (!nameValid) {
                    showGlobalInvalid('Role Name hanya boleh huruf dan spasi!');
                    return;
                }

                fetch('<?= base_url('admin/edit_user_role') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                        + '&name=' + encodeURIComponent(name)
                        + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeUserRoleEditModal();
                        showGlobalSuccess('User Role berhasil diupdate!');
                        loadRoleList();
                    }
                });
            };

            // Inisialisasi validasi awal
            validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
            validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
            // Open Edit Modal
            function openRoleEditModal(id, name, permissionIdsRaw) {
                var permissionIds = Array.isArray(permissionIdsRaw)
                    ? permissionIdsRaw
                    : (typeof permissionIdsRaw === 'string' && permissionIdsRaw.length > 0
                        ? permissionIdsRaw.split(',').map(function(x){return x.trim();})
                        : []);
                document.getElementById('edit-role-id').value = id;
                document.getElementById('edit-role-name').value = name;
                var container = document.getElementById('edit-permission-container');
                container.innerHTML = '';
                permissionIds.forEach(function(pId) {
                    var select = document.createElement('select');
                    select.name = "permissions[]";
                    select.className = "faq-input";
                    select.style.marginBottom = "8px";
                    select.innerHTML = `
                        <option value="">Select Permission</option>
                        <?php foreach ($permissions as $perm): ?>
                            <option value="<?= esc($perm['id']) ?>" ${pId == <?= esc($perm['id']) ?> ? "selected" : ""}>
                                <?= esc($perm['name']) ?>
                            </option>
                        <?php endforeach ?>
                    `;
                    container.appendChild(select);
                });
                document.getElementById('userRoleEditModal').style.display = 'flex';
            }
            document.getElementById('add-edit-permission-btn').onclick = function() {
                var container = document.getElementById('edit-permission-container');
                var select = document.createElement('select');
                select.name = "permissions[]";
                select.className = "faq-input";
                select.style.marginBottom = "8px";
                select.innerHTML = `
                    <option value="">Select Permission</option>
                    <?php foreach ($permissions as $perm): ?>
                        <option value="<?= esc($perm['id']) ?>"><?= esc($perm['name']) ?></option>
                    <?php endforeach ?>
                `;
                container.appendChild(select);
            };
            function closeUserRoleEditModal() {
                document.getElementById('userRoleEditModal').style.display = 'none';
            }
            document.getElementById('user-role-edit-form').onsubmit = function(e) {
                e.preventDefault();
                var id = document.getElementById('edit-role-id').value;
                var name = document.getElementById('edit-role-name').value;
                var selects = document.querySelectorAll('#edit-permission-container select');
                var permissions = [];
                selects.forEach(function(sel) {
                    if (sel.value) permissions.push(sel.value);
                });
                if (!name || permissions.length === 0) {
                    showGlobalWarning('Role name dan minimal satu permission wajib diisi!', null, null);
                    return;
                }
                fetch('<?= base_url('admin/edit_user_role') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                        + '&name=' + encodeURIComponent(name)
                        + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeUserRoleEditModal();
                        showGlobalSuccess('User Role berhasil diupdate!');
                        loadRoleList();
                    }
                });
            };

            // Open Delete Modal
            function openRoleDeleteModal(id) {
                document.getElementById('delete-role-id').value = id;
                document.getElementById('userRoleDeleteModal').style.display = 'flex';
            }
            function closeUserRoleDeleteModal() {
                document.getElementById('userRoleDeleteModal').style.display = 'none';
            }
            function confirmDeleteUserRole() {
                var id = document.getElementById('delete-role-id').value;
                fetch('<?= base_url('admin/delete_user_role') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeUserRoleDeleteModal();
                        showGlobalSuccess('User Role berhasil dihapus!');
                        loadRoleList();
                    }
                });
            }
            document.getElementById('user-role-form').onsubmit = function(e) {
                e.preventDefault();
                var name = document.getElementById('role-name').value;
                var selects = document.querySelectorAll('#permission-container select');
                var permissions = [];
                selects.forEach(function(sel) {
                    if (sel.value) permissions.push(sel.value);
                });

                if (!name || permissions.length === 0) {
                    showGlobalWarning('Role name dan minimal satu permission wajib diisi!', null, null);
                    return;
                }

                fetch('<?= base_url('admin/add_user_role') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'name=' + encodeURIComponent(name)
                        + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('role-name').value = '';
                        document.querySelectorAll('#permission-container select').forEach(function(sel, idx){
                            if(idx === 0) sel.value = '';
                            else sel.remove();
                        });
                        closeUserRoleModal();
                        showGlobalSuccess('User Role berhasil ditambahkan!');
                        loadRoleList();
                    }
                });
            };
            document.getElementById('add-permission-btn').onclick = function() {
                var container = document.getElementById('permission-container');
                var div = document.createElement('div');
                div.className = 'permission-row';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.style.gap = '8px';
                div.innerHTML = `
                    <select name="permissions[]" class="faq-input permission-select" style="flex:1; margin-bottom:8px;">
                        <option value="">Select Permission</option>
                        <?php foreach ($permissions as $perm): ?>
                            <option value="<?= esc($perm['id']) ?>"><?= esc($perm['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                    <button type="button" class="remove-permission-btn" style="background:#bbb; color:#fff; border:none; border-radius:12px; padding:6px 12px; font-size:15px; font-weight:600; cursor:pointer; margin-bottom:8px;">-</button>
                `;
                container.appendChild(div);
                updatePermissionDropdowns();
            };
            // Remove permission row
            document.getElementById('permission-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-permission-btn')) {
                    e.target.parentElement.remove();
                    updatePermissionDropdowns();
                }
            });

            // Disable selected permission in other dropdowns
            function updatePermissionDropdowns() {
                var selects = document.querySelectorAll('#permission-container .permission-select');
                // Show remove button except for first row
                selects.forEach(function(sel, idx) {
                    var btn = sel.parentElement.querySelector('.remove-permission-btn');
                    btn.style.display = (idx === 0) ? 'none' : 'inline-block';
                });
                // Get all selected values
                var selected = [];
                selects.forEach(function(sel) {
                    if (sel.value) selected.push(sel.value);
                });
                // Disable selected values in other dropdowns
                selects.forEach(function(sel) {
                    var options = sel.querySelectorAll('option');
                    options.forEach(function(opt) {
                        if (opt.value && selected.includes(opt.value) && opt.value !== sel.value) {
                            opt.disabled = true;
                        } else {
                            opt.disabled = false;
                        }
                    });
                });
            }

            // Update dropdowns on change
            document.getElementById('permission-container').addEventListener('change', function(e) {
                if (e.target.classList.contains('permission-select')) {
                    updatePermissionDropdowns();
                }
            });

            // Inisialisasi saat halaman load
            updatePermissionDropdowns();
            function openSlaEditModal(id, priority, response, resolution) {
                document.getElementById('edit-sla-id').value = id;
                document.getElementById('edit-sla-priority').value = priority;
                document.getElementById('edit-sla-response').value = response;
                document.getElementById('edit-sla-resolution').value = resolution;
                document.getElementById('slaEditModal').style.display = 'flex';
            }
            function closeSlaEditModal() {
                document.getElementById('slaEditModal').style.display = 'none';
            }
            document.getElementById('sla-edit-form').onsubmit = function(e) {
                e.preventDefault();
                var id = document.getElementById('edit-sla-id').value;
                var priority = document.getElementById('edit-sla-priority').value;
                var response = document.getElementById('edit-sla-response').value;
                var resolution = document.getElementById('edit-sla-resolution').value;
                if (!priority || !response || !resolution) {
                    showGlobalWarning('Semua field wajib diisi!', null, null);
                    return;
                }
                fetch('<?= base_url('admin/edit_sla') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                        + '&priority=' + encodeURIComponent(priority)
                        + '&response_time=' + encodeURIComponent(response)
                        + '&resolution_time=' + encodeURIComponent(resolution)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeSlaEditModal();
                        showGlobalSuccess('SLA berhasil diupdate!');
                        loadSlaList();
                    }
                });
            };

            // Delete SLA
            function openSlaDeleteModal(id) {
                document.getElementById('delete-sla-id').value = id;
                document.getElementById('slaDeleteModal').style.display = 'flex';
            }
            function closeSlaDeleteModal() {
                document.getElementById('slaDeleteModal').style.display = 'none';
            }
            function confirmDeleteSla() {
                var id = document.getElementById('delete-sla-id').value;
                fetch('<?= base_url('admin/delete_sla') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeSlaDeleteModal();
                        showGlobalSuccess('SLA berhasil dihapus!');
                        loadSlaList();
                        updateSlaRequestTypeDropdown();
                    }
                });
            }
            function loadSlaList(page = 1, perPage = 10) {
                fetch('<?= base_url('admin/get_sla_list') ?>?page=' + page + '&per_page=' + perPage)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('sla-list').innerHTML = html;
                });
            }
            document.getElementById('btn-add-sla').onclick = function() {
                document.getElementById('slaModal').style.display = 'flex';
            };
            function closeSlaModal() {
                document.getElementById('slaModal').style.display = 'none';
            }
            document.getElementById('sla-form').onsubmit = function(e) {
                e.preventDefault();

                var priority = document.getElementById('sla-priority').value;
                var response = document.getElementById('sla-response').value;
                var resolution = document.getElementById('sla-resolution').value;

                if (!priority || !response || !resolution) {
                    showGlobalWarning('Semua field wajib diisi!', null, null);
                    return;
                }

                fetch('<?= base_url('admin/add_sla') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'priority=' + encodeURIComponent(priority)
                        + '&response_time=' + encodeURIComponent(response)
                        + '&resolution_time=' + encodeURIComponent(resolution)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('sla-priority').value = '';
                        document.getElementById('sla-response').value = '';
                        document.getElementById('sla-resolution').value = '';
                        closeSlaModal();
                        showGlobalSuccess('SLA berhasil ditambahkan!');
                        loadSlaList();
                    }
                });
            };
            function moveSlider(idx) {
                const tabs = document.querySelectorAll('.settings-tab');
                tabs.forEach((tab, i) => tab.classList.toggle('active', i === idx));
                document.getElementById('settingsSlider').style.left = (idx * 25) + '%';

                document.getElementById('faq-management-content').style.display = idx === 0 ? 'block' : 'none';
                document.getElementById('user-roles-content').style.display = idx === 1 ? 'block' : 'none';
                document.getElementById('request-type-content').style.display = idx === 2 ? 'block' : 'none';
                document.getElementById('sla-settings-content').style.display = idx === 3 ? 'block' : 'none';

                if (idx === 1) loadRoleList(1, 10);
                if (idx === 0) loadFaqList(1, 10);
                if (idx === 2) loadRequestTypeList(1, 10);
                if (idx === 3) loadSlaList(1, 10);
            }
            window.addEventListener('DOMContentLoaded', function() {
                // Pastikan tab FAQ aktif saat load awal
                moveSlider(0);
                // Panggil loader FAQ list
                loadFaqList(1, 10);
            });
            document.querySelector('.btn-add-faq').onclick = function() {
                document.getElementById('faqModal').style.display = 'flex';
            }
            function closeFaqModal() {
                document.getElementById('faqModal').style.display = 'none';
            }
            document.addEventListener('keydown', function(e){
                if(e.key === "Escape") closeFaqModal();
            });
            document.getElementById('faq-form').onsubmit = function(e) {
                e.preventDefault();
                fetch('<?= base_url('admin/add_faq') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'question=' + encodeURIComponent(document.getElementById('faq-title').value)
                        + '&answer=' + encodeURIComponent(document.getElementById('faq-desc').value)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('faq-title').value = '';
                        document.getElementById('faq-desc').value = '';
                        closeFaqModal();
                        showGlobalSuccess('FAQ berhasil ditambahkan!');
                        loadFaqList();
                    }
                });
            };
            function showFaqSuccess() {
                var el = document.getElementById('faq-success');
                el.style.display = 'block';
                setTimeout(function(){ el.style.display = 'none'; }, 2000);
            }
            function loadFaqList(page = 1, perPage = 10) {
                fetch('<?= base_url('admin/get_faq_list') ?>?page=' + page + '&per_page=' + perPage)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('faq-list').innerHTML = html;
                });
            }
            document.querySelector('.faq-pagination').onchange = function() {
                loadFaqList(1, this.value);
            };
            function openFaqDeleteModal(id) {
                showGlobalWarning(
                    'Apakah Anda yakin ingin menghapus FAQ ini? Data yang dihapus tidak dapat dikembalikan.',
                    function() { // OK
                        confirmDeleteFaq(id);
                    },
                    function() { // Cancel
                        // Tidak melakukan apa-apa
                    }
                );
            }
            function confirmDeleteFaq(id) {
                fetch('<?= base_url('admin/delete_faq') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showGlobalSuccess('FAQ berhasil dihapus!');
                        loadFaqList();
                    }
                });
            }
            function openFaqEditModal(id, title, desc) {
                document.getElementById('edit-faq-id').value = id;
                document.getElementById('edit-faq-title').value = title;
                document.getElementById('edit-faq-desc').value = desc;
                document.getElementById('faqEditModal').style.display = 'flex';
            }
            function closeFaqEditModal() {
                document.getElementById('faqEditModal').style.display = 'none';
            }
            document.getElementById('faq-edit-form').onsubmit = function(e) {
                e.preventDefault();
                fetch('<?= base_url('admin/edit_faq') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(document.getElementById('edit-faq-id').value)
                        + '&question=' + encodeURIComponent(document.getElementById('edit-faq-title').value)
                        + '&answer=' + encodeURIComponent(document.getElementById('edit-faq-desc').value)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeFaqEditModal();
                        showGlobalSuccess('FAQ berhasil diupdate!');
                        loadFaqList();
                    }
                });
            };
            document.addEventListener('keydown', function(e){
                if(e.key === "Escape") {
                    closeFaqModal();
                    closeFaqEditModal();
                    closeFaqDeleteModal();
                }
            });
            function loadRoleList(page = 1, perPage = 10) {
                fetch('<?= base_url('admin/get_user_role_list') ?>?page=' + page + '&per_page=' + perPage)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('user-role-list').innerHTML = html;
                });
            }
            document.querySelectorAll('.btn-add-faq')[1].onclick = function() {
                document.getElementById('userRoleModal').style.display = 'flex';
            }
            function closeUserRoleModal() {
                document.getElementById('userRoleModal').style.display = 'none';
            }
            document.addEventListener('keydown', function(e){
                if(e.key === "Escape") closeUserRoleModal();
            });
            function loadRequestTypeList(page = 1, perPage = 10) {
                fetch('<?= base_url('admin/get_request_type_list') ?>?page=' + page + '&per_page=' + perPage)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('request-type-list').innerHTML = html;
                });
            }
            document.querySelectorAll('.btn-add-faq')[2].onclick = function() {
                document.getElementById('requestTypeModal').style.display = 'flex';
            }
            function closeRequestTypeModal() {
                document.getElementById('requestTypeModal').style.display = 'none';
            }
            document.addEventListener('keydown', function(e){
                if(e.key === "Escape") closeRequestTypeModal();
            });
            document.getElementById('request-type-form').onsubmit = function(e) {
                e.preventDefault();
                fetch('<?= base_url('admin/add_request_type') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'name=' + encodeURIComponent(document.getElementById('request-type-name').value)
                        + '&description=' + encodeURIComponent(document.getElementById('request-type-desc').value)
                        + '&status=' + encodeURIComponent(document.getElementById('request-type-status').value)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('request-type-name').value = '';
                        document.getElementById('request-type-desc').value = '';
                        document.getElementById('request-type-status').value = 'Active';
                        closeRequestTypeModal();
                        showGlobalSuccess('Request Type berhasil ditambahkan!');
                        loadRequestTypeList();
                    }
                });
            };
            function openRequestTypeEditModal(id, name, desc, status) {
                document.getElementById('edit-request-type-id').value = id;
                document.getElementById('edit-request-type-name').value = name;
                document.getElementById('edit-request-type-desc').value = desc;
                document.getElementById('edit-request-type-status').value = status;
                document.getElementById('requestTypeEditModal').style.display = 'flex';
            }
            function closeRequestTypeEditModal() {
                document.getElementById('requestTypeEditModal').style.display = 'none';
            }
            document.getElementById('request-type-edit-form').onsubmit = function(e) {
                e.preventDefault();
                fetch('<?= base_url('admin/edit_request_type') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(document.getElementById('edit-request-type-id').value)
                        + '&name=' + encodeURIComponent(document.getElementById('edit-request-type-name').value)
                        + '&description=' + encodeURIComponent(document.getElementById('edit-request-type-desc').value)
                        + '&status=' + encodeURIComponent(document.getElementById('edit-request-type-status').value)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        closeRequestTypeEditModal();
                        showGlobalSuccess('Request Type berhasil diupdate!');
                        loadRequestTypeList();
                    }
                });
            };

            function openRequestTypeDeleteModal(id) {
                showGlobalWarning(
                    'Apakah Anda yakin ingin menghapus Request Type ini? Data yang dihapus tidak dapat dikembalikan.',
                    function() { // OK
                        confirmDeleteRequestType(id);
                    },
                    function() { /* Cancel: tidak melakukan apa-apa */ }
                );
            }

            function confirmDeleteRequestType(id) {
                fetch('<?= base_url('admin/delete_request_type') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showGlobalSuccess('Request Type berhasil dihapus!');
                        loadRequestTypeList();
                    } else if(data.invalid) {
                        showGlobalInvalid(data.message || 'Request Type sudah dipakai di SLA dan tidak bisa dihapus.');
                    }
                });
            }
           
        </script>
        <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
        <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
    </div>
</body>
</html>