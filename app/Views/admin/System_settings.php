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
    <script>
        var baseUrl = '<?= base_url() ?>';
        var permissionOptionsHtml = `
            <option value="">Select Permission</option>
            <?php foreach ($permissions as $perm): ?>
                <option value="<?= esc($perm['id']) ?>"><?= esc($perm['name']) ?></option>
            <?php endforeach ?>
        `;
    </script>
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
                        <button class="btn-add-faq" id="btn-add-faq">Add New FAQ</button>
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
                        <button class="btn-add-faq" style="background:#234be7;" id="btn-add-user-role">Add New User Role</button>
                    </div>
                    <!-- Modal Add User Role -->
                    <div id="userRoleModal" class="faq-modal-bg" style="display:none;">
                        <div class="faq-modal">
                            <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Add New User Role</div>
                                <span onclick="closeUserRoleModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="user-role-form" style="display:flex; flex-direction:column; align-items:flex-start; gap:10px; align-self:stretch; width:100%; padding-top:10px;">
                                <label for="role-name" style="font-weight:500;">Role Name <span style="color:#FF474A">*</span></label>
                                <input id="role-name" type="text" placeholder="Input role name" class="faq-input">

                                <label style="font-weight:500;">Permission <span style="color:#FF474A">*</span></label>
                                <div id="permission-container" style="width:100%;">
                                    <div class="permission-row" style="display:flex; align-items:center; gap:8px; width:100%;">
                                        <select name="permissions[]" class="faq-input permission-select" style="flex:1;">
                                            <option value="">Select Permission</option>
                                            <?php foreach ($permissions as $perm): ?>
                                                <option value="<?= esc($perm['id']) ?>"><?= esc($perm['name']) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <button type="button" class="remove-permission-btn" style="display:none; background:#bbb; color:#fff; border:none; border-radius:12px; padding:6px 12px; font-size:15px; font-weight:600; cursor:pointer;">-</button>
                                    </div>
                                </div>
                                <button type="button" id="add-permission-btn" style="background:#eee; color:#222; border:none; border-radius:22px; padding:6px 18px; font-size:15px; font-weight:600; cursor:pointer;">
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
                        <div class="faq-modal">
                            <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:20px; font-weight:600;">Edit User Role</div>
                                <span onclick="closeUserRoleEditModal()" style="font-size:28px; font-weight:700; cursor:pointer;">&times;</span>
                            </div>
                            <form id="user-role-edit-form" style="display:flex; flex-direction:column; align-items:flex-start; gap:10px; align-self:stretch; width:100%; padding-top:10px;">
                                <input type="hidden" id="edit-role-id">
                                <label for="edit-role-name" style="font-weight:500;">Role Name <span style="color:#FF474A">*</span></label>
                                <input id="edit-role-name" type="text" class="faq-input">
                                <label style="font-weight:500;">Permission <span style="color:#FF474A">*</span></label>
                                <div id="edit-permission-container" style="width:100%;"></div>
                                <button type="button" id="add-edit-permission-btn" style="background:#eee; color:#222; border:none; border-radius:22px; padding:6px 18px; font-size:15px; font-weight:600; cursor:pointer;">
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
                        <button class="btn-add-faq" style="background:#234be7;" id="btn-add-request-type">Add New Request Type</button>
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
                            <select id="sla-priority" class="faq-input">
                                <option value="">Select Priority</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                            <label for="sla-response" style="font-weight:500;">Response Time (hours) <span style="color:#FF474A">*</span></label>
                            <input id="sla-response" type="text" placeholder="Input response time (hours)" class="faq-input" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, ')">

                            <label for="sla-resolution" style="font-weight:500;">Resolution Time (hours) <span style="color:#FF474A">*</span></label>
                            <input id="sla-resolution" type="text" placeholder="Input resolution time (hours)" class="faq-input" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, ')">

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
                            <select id="edit-sla-priority" class="faq-input">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>

                            <label for="edit-sla-response" style="font-weight:500;">Response Time (hours) <span style="color:#FF474A">*</span></label>
                            <input id="edit-sla-response" type="text" placeholder="Input response time (hours)" class="faq-input"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ')">

                            <label for="edit-sla-resolution" style="font-weight:500;">Resolution Time (hours) <span style="color:#FF474A">*</span></label>
                            <input id="edit-sla-resolution" type="text" placeholder="Input resolution time (hours)" class="faq-input"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ')">

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
        <script src="<?= base_url('assets/js/admin/system_settings.js') ?>"></script>
        <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
        <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
    </div>
</body>
</html>