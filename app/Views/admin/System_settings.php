<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    <style>
        body { background: #f5f6fa; font-family: 'Montserrat', Arial, sans-serif; margin: 0; }
        .sidebar { width: 70px; position: fixed; left: 0; top: 0; height: 100vh; background: #fff; box-shadow: 2px 0 8px rgba(0,0,0,0.04); }
        .main-content { margin-left: 70px; padding: 30px 40px; min-height: 100vh; }
        .page-title { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .breadcrumb { font-size: 15px; color: #444; margin-bottom: 18px; }
        .settings-header-row {
            display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;
        }
        .settings-welcome {
            font-size: 20px; font-weight: 500; color: #222; margin-bottom: 2px;
        }
        .settings-role {
            font-size: 13px; color: #444; text-align: right;
        }
        .settings-tabs {
            display: flex;
            background: #eaeaea;
            border-radius: 29px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
            height: 56px;
        }
        .settings-tab {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 17px;
            font-weight: 600;
            color: #222;
            cursor: pointer;
            z-index: 2;
            position: relative;
            transition: color 0.2s;
            height: 56px;
        }
        .settings-tab.active {
            color: #222;
        }
        .settings-slider {
            position: absolute;
            top: 0;
            left: 0;
            height: 56px;
            width: 25%;
            background: #FCD116;
            border-radius: 29px;
            z-index: 1;
            transition: left 0.3s cubic-bezier(.4,0,.2,1), width 0.2s;
        }
        @media (max-width: 900px) {
            .main-content { margin-left: 54px; padding: 18px 8px; }
            .sidebar { width: 54px; }
        }
        @media (max-width: 500px) {
            .main-content { margin-left: 0; padding: 4px 2px; }
            .sidebar { position: relative; width: 100vw; height: 48px; box-shadow: none; }
        }
        .faq-frame {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px 32px 24px 32px;
            margin-bottom: 18px;
            margin-top: 8px;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }
        .faq-frame-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .faq-title {
            font-weight:600; font-size:18px;
        }
        .faq-actions {
            display:flex; align-items:center; gap:12px;
        }
        .btn-add-faq {
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
        .btn-add-faq:hover {
            background: #1a3bb3;
        }
        .faq-pagination {
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 4px 12px;
            margin-left: 8px;
            margin-right: 8px;
        }
        @media (max-width: 700px) {
            .faq-frame { padding: 12px 4px; }
            .faq-frame-header { flex-direction: column; gap: 12px; }
            .faq-actions { width: 100%; justify-content: flex-start; }
        }
        .faq-modal-bg {
            position: fixed;
            left: 0; top: 0;
            width: 100vw; height: 100vh;
            background: rgba(255,255,255,0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
        }
        .faq-modal {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 10px 2px rgba(0,0,0,0.25);
            padding: 32px 24px 24px 24px;
            min-width: 500px;
            max-width: 900px;
            width: 60vw;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
            align-self: center;
        }
        .faq-input {
            width: 100%;
            padding: 14px;
            border-radius: 29px;
            border: 1px solid #bbb;
            font-size: 16px;
            margin-bottom: 8px;
            box-sizing: border-box;
        }
        .faq-textarea {
            width: 100%;
            min-height: 120px;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid #bbb;
            font-size: 16px;
            margin-bottom: 8px;
            box-sizing: border-box;
            resize: vertical;
        }
        .btn-submit-faq {
            background: #234be7;
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 8px 32px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit-faq:hover {
            background: #1a3bb3;
        }
        .faq-card {
            background: #f7f7f7;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 18px 24px;
            margin-bottom: 18px;
        }
        .faq-question {
            font-weight: 600;
            font-size: 17px;
            margin-bottom: 8px;
        }
        .faq-answer {
            font-size: 15px;
            color: #444;
            margin-bottom: 12px;
            word-break: break-word;
            white-space: pre-line;
        }
        .faq-actions-row {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .faq-edit-btn {
            background: #234be7;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .faq-delete-btn {
            background: #7A161C;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .faq-pagination-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 18px 0 0 0;
        }
        .faq-page-btn {
            background: #fff;
            border: none;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 16px;
            font-weight: 600;
            color: #222;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .faq-page-btn.active {
            background: #2940D3;
            color: #fff;
        }
        #faq-success {
            display:none; position:fixed; top:32px; right:32px; z-index:99999; background:#22c55e; color:#fff; padding:16px 32px; border-radius:14px; font-size:17px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,0.13);
        }
        .role-edit-btn {
            background: #234be7;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .role-delete-btn {
            background: #7A161C;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php $active = 'settings'; include('navbar.php'); ?>
    <div class="main-content">
        <div class="page-title">System Settings</div>
        <div class="breadcrumb">Home &gt; System Settings</div>
        <div class="settings-header-row">
            <div></div>
            <div>
                <div class="settings-welcome">Welcome, [user name]</div>
                <div class="settings-role">Superadmin</div>
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

                                <label for="role-permission-1" style="font-weight:500;">Permission</label>
                                <select id="role-permission-1" class="faq-input">
                                    <option value="">Select Menu</option>
                                    <!-- Tambahkan opsi menu di sini -->
                                </select>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <select id="role-permission-2" class="faq-input" style="flex:1;">
                                        <option value="">Select Menu</option>
                                        <!-- Tambahkan opsi menu di sini -->
                                    </select>
                                    <span style="color:#888; font-size:15px;">Add Permission</span>
                                </div>
                                <div style="width:100%; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn-submit-faq">Submit</button>
                                </div>
                            </form>
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
                            <label for="sla-request-type" style="font-weight:500;">Request Type Name <span style="color:#FF474A">*</span></label>
                            <select id="sla-request-type" class="faq-input" required>
                                <option value="">Select Request Type</option>
                                <?php foreach ($requestTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>"
                                        <?php if (in_array($type['id'], $usedRequestTypeIds)) echo 'disabled'; ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                        <?php if (in_array($type['id'], $usedRequestTypeIds)) echo ' '; ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
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

                var requestTypeId = document.getElementById('sla-request-type').value;
                var priority = document.getElementById('sla-priority').value;
                var response = document.getElementById('sla-response').value;
                var resolution = document.getElementById('sla-resolution').value;

                if (!requestTypeId || !priority || !response || !resolution) {
                    showGlobalWarning('Semua field wajib diisi!', null, null);
                    return;
                }

                fetch('<?= base_url('admin/add_sla') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'request_type_id=' + encodeURIComponent(requestTypeId)
                        + '&priority=' + encodeURIComponent(priority)
                        + '&response_time=' + encodeURIComponent(response)
                        + '&resolution_time=' + encodeURIComponent(resolution)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('sla-request-type').value = '';
                        document.getElementById('sla-priority').value = '';
                        document.getElementById('sla-response').value = '';
                        document.getElementById('sla-resolution').value = '';
                        closeSlaModal();
                        showGlobalSuccess('SLA berhasil ditambahkan!');
                        loadSlaList();
                        updateSlaRequestTypeDropdown();
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
            function updateSlaRequestTypeDropdown() {
                fetch('<?= base_url('admin/get_used_request_types') ?>')
                .then(res => res.json())
                .then(data => {
                    const used = data.used || [];
                    const dropdown = document.getElementById('sla-request-type');
                    Array.from(dropdown.options).forEach(opt => {
                        if (opt.value === "") return;
                        if (used.includes(opt.value)) {
                            opt.disabled = true;
                            opt.textContent = opt.textContent.replace(/ \( \)$/, '') + ' ';
                        } else {
                            opt.disabled = false;
                            opt.textContent = opt.textContent.replace(/ \(\)$/, '');
                        }
                    });
                });
            }
        </script>
        <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
        <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
    </div>
</body>
</html>