<?php include(APPPATH . 'Views/components/success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings - HC Helpdesk</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php $active = 'settings'; include('navbar.php'); ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => 'System Settings',
            'pageTitle'        => 'System Settings',
            'showCreateTicket' => false,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- Top Pill-Shaped Navigation Bar (Matches Reference Image) -->
            <div class="settings-nav-bar" id="settingsNavBar">
                <button type="button" class="settings-nav-btn active" onclick="switchSettingsTab('roles')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Roles & Permissions</span>
                    <span class="nav-counter-pill"><?= $counts['roles'] ?? 0 ?> Roles</span>
                </button>

                <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('sla')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>SLA Policies</span>
                    <span class="nav-counter-pill"><?= $counts['slas'] ?? 0 ?> Tiers</span>
                </button>

                <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('request_type')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Request Type</span>
                    <span class="nav-counter-pill"><?= $counts['requestTypes'] ?? 0 ?> Types</span>
                </button>

                <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('faq')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>FAQ Management</span>
                    <span class="nav-counter-pill"><?= $counts['faqs'] ?? 0 ?> Articles</span>
                </button>
            </div>

            <!-- TAB 1: ROLES & PERMISSIONS -->
            <div id="tab-content-roles" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">User Roles & Permissions</h2>
                            <span class="headline-badge badge-rbac">RBAC</span>
                        </div>
                        <p class="headline-desc">Define access levels, module permissions, and assigned personnel across your workspace.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="role-search-input" class="settings-search-input" placeholder="Filter roles..." onkeyup="filterRolesTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-user-role">
                            <span style="font-size:16px;">+</span>
                            <span>Add New Role</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="user-role-list">
                    <?= view('admin/user_role_list', ['roles' => $rolesData['roles'], 'paginationHTML' => $paginationHTML ?? '']) ?>
                </div>
            </div>

            <!-- TAB 2: SLA POLICIES -->
            <div id="tab-content-sla" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">SLA Response & Resolution Policies</h2>
                            <span class="headline-badge badge-engine">Global Engine</span>
                        </div>
                        <p class="headline-desc">Configure target response times, resolution deadlines, and automatic escalation pathways by priority tier.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-sla">
                            <span style="font-size:16px;">+</span>
                            <span>New SLA Rule</span>
                        </button>
                    </div>
                </div>

                <div id="sla-list">
                    <?= view('admin/sla_settings', ['slas' => $slasData['slas'], 'totalPages' => $slasData['totalPages'], 'page' => 1, 'perPage' => 10]) ?>
                </div>
            </div>

            <!-- TAB 3: REQUEST TYPE -->
            <div id="tab-content-request_type" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Request Type Catalog</h2>
                            <span class="headline-badge badge-catalog">Catalog</span>
                        </div>
                        <p class="headline-desc">Manage categories and submission classifications available for employee ticket filing.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-request-type">
                            <span style="font-size:16px;">+</span>
                            <span>Add Request Type</span>
                        </button>
                    </div>
                </div>

                <div id="request-type-list">
                    <?= view('admin/request_type', ['types' => $requestTypes]) ?>
                </div>
            </div>

            <!-- TAB 4: FAQ MANAGEMENT -->
            <div id="tab-content-faq" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Knowledge Base & FAQ Articles</h2>
                            <span class="headline-badge badge-rbac">Articles</span>
                        </div>
                        <p class="headline-desc">Publish self-service guidance and troubleshooting answers for employees.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-faq">
                            <span style="font-size:16px;">+</span>
                            <span>Add New FAQ</span>
                        </button>
                    </div>
                </div>

                <div id="faq-list">
                    <?= view('admin/faq_list', ['faqs' => $faqs, 'page' => $page, 'totalPages' => $totalPages, 'perPage' => $perPage, 'paginationHTML' => $paginationHTML ?? '']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODALS (Clean Architecture & Validation Preserved)
         ========================================================================= -->

    <!-- Modal Add User Role -->
    <div id="userRoleModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Add New User Role</span>
                <button type="button" class="modal-close-btn" onclick="closeUserRoleModal()">&times;</button>
            </div>
            <form id="user-role-form">
                <div class="modal-form-group">
                    <label for="role-name" class="modal-form-label">Role Name <span style="color:#ef4444">*</span></label>
                    <input id="role-name" type="text" placeholder="e.g. Senior Support Lead" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">Authorized Permissions <span style="color:#ef4444">*</span></label>
                    <div id="permission-container" style="display: flex; flex-direction: column; gap: 8px;">
                        <div class="permission-row" style="display:flex; align-items:center; gap:8px;">
                            <div style="flex:1;">
                                <?php
                                    $permOptions = [];
                                    if (!empty($permissions)) {
                                        foreach ($permissions as $perm) {
                                            $permOptions[$perm['id']] = $perm['name'];
                                        }
                                    }
                                ?>
                                <?= view('components/basic_select', [
                                    'name'        => 'permissions[]',
                                    'id'          => 'perm_initial_add',
                                    'placeholder' => 'Select Permission Module',
                                    'options'     => $permOptions
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-permission-btn" style="margin-top:6px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:12.5px; font-weight:600; cursor:pointer; align-self: flex-start;">
                        + Add Another Permission
                    </button>
                </div>

                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeUserRoleModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User Role -->
    <div id="userRoleEditModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Edit User Role</span>
                <button type="button" class="modal-close-btn" onclick="closeUserRoleEditModal()">&times;</button>
            </div>
            <form id="user-role-edit-form">
                <input type="hidden" id="edit-role-id">
                <div class="modal-form-group">
                    <label for="edit-role-name" class="modal-form-label">Role Name <span style="color:#ef4444">*</span></label>
                    <input id="edit-role-name" type="text" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">Authorized Permissions <span style="color:#ef4444">*</span></label>
                    <div id="edit-permission-container" style="display: flex; flex-direction: column; gap: 8px;"></div>
                    <button type="button" id="add-edit-permission-btn" style="margin-top:6px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:12.5px; font-weight:600; cursor:pointer; align-self: flex-start;">
                        + Add Another Permission
                    </button>
                </div>

                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeUserRoleEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete User Role -->
    <div id="userRoleDeleteModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal" style="max-width:400px; text-align:center;">
            <div class="modal-title-clean" style="margin-bottom:8px;">Hapus User Role?</div>
            <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus User Role ini? Tindakan ini tidak dapat dibatalkan.</p>
            <input type="hidden" id="delete-role-id">
            <div style="display:flex; gap:10px; justify-content:center;">
                <button type="button" class="btn-modal-cancel" onclick="closeUserRoleDeleteModal()">Batal</button>
                <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteUserRole()">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Add SLA -->
    <div id="slaModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Add New SLA Policy</span>
                <button type="button" class="modal-close-btn" onclick="closeSlaModal()">&times;</button>
            </div>
            <form id="sla-form">
                <div class="modal-form-group">
                    <label class="modal-form-label">Priority Level <span style="color:#ef4444">*</span></label>
                    <?= view('components/basic_select', [
                        'name'        => 'priority',
                        'id'          => 'sla-priority',
                        'placeholder' => 'Select Priority Level',
                        'options'     => [
                            'Low'    => 'Low (P4)',
                            'Medium' => 'Medium (P3)',
                            'High'   => 'High (P2)',
                            'Urgent' => 'Urgent (P1)'
                        ],
                        'required'    => true
                    ]) ?>
                </div>
                <div class="modal-form-group">
                    <label for="sla-response" class="modal-form-label">Target 1st Response (Hours) <span style="color:#ef4444">*</span></label>
                    <input id="sla-response" type="text" placeholder="e.g. 2" class="modal-form-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="modal-form-group">
                    <label for="sla-resolution" class="modal-form-label">Target Resolution (Hours) <span style="color:#ef4444">*</span></label>
                    <input id="sla-resolution" type="text" placeholder="e.g. 8" class="modal-form-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeSlaModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Create Policy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit SLA -->
    <div id="slaEditModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Edit SLA Policy</span>
                <button type="button" class="modal-close-btn" onclick="closeSlaEditModal()">&times;</button>
            </div>
            <form id="sla-edit-form">
                <input type="hidden" id="edit-sla-id">
                <div class="modal-form-group">
                    <label class="modal-form-label">Priority Level <span style="color:#ef4444">*</span></label>
                    <?= view('components/basic_select', [
                        'name'        => 'priority',
                        'id'          => 'edit-sla-priority',
                        'placeholder' => 'Select Priority Level',
                        'options'     => [
                            'Low'    => 'Low (P4)',
                            'Medium' => 'Medium (P3)',
                            'High'   => 'High (P2)',
                            'Urgent' => 'Urgent (P1)'
                        ],
                        'required'    => true
                    ]) ?>
                </div>
                <div class="modal-form-group">
                    <label for="edit-sla-response" class="modal-form-label">Target 1st Response (Hours) <span style="color:#ef4444">*</span></label>
                    <input id="edit-sla-response" type="text" class="modal-form-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="modal-form-group">
                    <label for="edit-sla-resolution" class="modal-form-label">Target Resolution (Hours) <span style="color:#ef4444">*</span></label>
                    <input id="edit-sla-resolution" type="text" class="modal-form-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeSlaEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Save Policy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete SLA -->
    <div id="slaDeleteModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal" style="max-width:400px; text-align:center;">
            <div class="modal-title-clean" style="margin-bottom:8px;">Hapus SLA?</div>
            <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus konfigurasi SLA ini?</p>
            <input type="hidden" id="delete-sla-id">
            <div style="display:flex; gap:10px; justify-content:center;">
                <button type="button" class="btn-modal-cancel" onclick="closeSlaDeleteModal()">Batal</button>
                <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteSla()">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Add Request Type -->
    <div id="requestTypeModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Add New Request Type</span>
                <button type="button" class="modal-close-btn" onclick="closeRequestTypeModal()">&times;</button>
            </div>
            <form id="request-type-form">
                <div class="modal-form-group">
                    <label for="request-type-name" class="modal-form-label">Request Type Name <span style="color:#ef4444">*</span></label>
                    <input id="request-type-name" type="text" placeholder="e.g. Employee Benefits Request" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="request-type-desc" class="modal-form-label">Description</label>
                    <textarea id="request-type-desc" placeholder="Scope and purpose of this request type" class="modal-form-textarea"></textarea>
                </div>
                <div class="modal-form-group">
                    <label for="request-type-status" class="modal-form-label">Status</label>
                    <select id="request-type-status" class="modal-form-select">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Create Type</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Request Type -->
    <div id="requestTypeEditModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Edit Request Type</span>
                <button type="button" class="modal-close-btn" onclick="closeRequestTypeEditModal()">&times;</button>
            </div>
            <form id="request-type-edit-form">
                <input type="hidden" id="edit-request-type-id">
                <div class="modal-form-group">
                    <label for="edit-request-type-name" class="modal-form-label">Request Type Name <span style="color:#ef4444">*</span></label>
                    <input id="edit-request-type-name" type="text" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="edit-request-type-desc" class="modal-form-label">Description</label>
                    <textarea id="edit-request-type-desc" class="modal-form-textarea"></textarea>
                </div>
                <div class="modal-form-group">
                    <label for="edit-request-type-status" class="modal-form-label">Status</label>
                    <select id="edit-request-type-status" class="modal-form-select">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Request Type -->
    <div id="requestTypeDeleteModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal" style="max-width:400px; text-align:center;">
            <div class="modal-title-clean" style="margin-bottom:8px;">Hapus Request Type?</div>
            <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus kategori Request Type ini?</p>
            <input type="hidden" id="delete-request-type-id">
            <div style="display:flex; gap:10px; justify-content:center;">
                <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeDeleteModal()">Batal</button>
                <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteRequestType()">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Add FAQ -->
    <div id="faqModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Add New FAQ Article</span>
                <button type="button" class="modal-close-btn" onclick="closeFaqModal()">&times;</button>
            </div>
            <form id="faq-form">
                <div class="modal-form-group">
                    <label for="faq-title" class="modal-form-label">Question / Title <span style="color:#ef4444">*</span></label>
                    <input id="faq-title" type="text" placeholder="e.g. How do I request maternity leave?" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="faq-desc" class="modal-form-label">Answer / Content <span style="color:#ef4444">*</span></label>
                    <textarea id="faq-desc" placeholder="Provide clear step-by-step guidance..." class="modal-form-textarea" style="min-height:110px;" required></textarea>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeFaqModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Publish Article</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit FAQ -->
    <div id="faqEditModal" class="faq-modal-bg" style="display:none;">
        <div class="faq-modal">
            <div class="modal-header-clean">
                <span class="modal-title-clean">Edit FAQ Article</span>
                <button type="button" class="modal-close-btn" onclick="closeFaqEditModal()">&times;</button>
            </div>
            <form id="faq-edit-form">
                <input type="hidden" id="edit-faq-id">
                <div class="modal-form-group">
                    <label for="edit-faq-title" class="modal-form-label">Question / Title <span style="color:#ef4444">*</span></label>
                    <input id="edit-faq-title" type="text" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="edit-faq-desc" class="modal-form-label">Answer / Content <span style="color:#ef4444">*</span></label>
                    <textarea id="edit-faq-desc" class="modal-form-textarea" style="min-height:110px;" required></textarea>
                </div>
                <div class="modal-footer-clean">
                    <button type="button" class="btn-modal-cancel" onclick="closeFaqEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================================
         JAVASCRIPT LOGIC (Async Loaders, Validation, Search, and Event Handlers)
         ========================================================================= -->
    <script>
        // Tab switching
        function switchSettingsTab(tabName) {
            const tabs = ['roles', 'sla', 'request_type', 'faq'];
            tabs.forEach(t => {
                const el = document.getElementById('tab-content-' + t);
                if (el) el.style.display = (t === tabName) ? 'block' : 'none';
            });

            const buttons = document.querySelectorAll('.settings-nav-btn');
            buttons.forEach(btn => {
                const onclickText = btn.getAttribute('onclick') || '';
                btn.classList.toggle('active', onclickText.includes(tabName));
            });

            // Trigger fresh ajax reload if needed
            if (tabName === 'roles') loadRoleList(1, 10);
            if (tabName === 'sla') loadSlaList(1, 10);
            if (tabName === 'request_type') loadRequestTypeList(1, 10);
            if (tabName === 'faq') loadFaqList(1, 10);
        }

        // Live Role Filter by Text
        function filterRolesTable(query) {
            const q = query.toLowerCase();
            const rows = document.querySelectorAll('#user-role-list tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // --- User Role Handlers ---
        document.getElementById('btn-add-user-role').onclick = function() {
            document.getElementById('userRoleModal').style.display = 'flex';
        };
        function closeUserRoleModal() {
            document.getElementById('userRoleModal').style.display = 'none';
        }
        function closeUserRoleEditModal() {
            document.getElementById('userRoleEditModal').style.display = 'none';
        }
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
        function loadRoleList(page = 1, perPage = 10) {
            fetch('<?= base_url('admin/get_user_role_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => {
                document.getElementById('user-role-list').innerHTML = html;
            });
        }

        // Form Submit Add Role
        document.getElementById('user-role-form').onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('role-name').value.trim();
            var selects = document.querySelectorAll('#permission-container select');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) { showGlobalInvalid('Role Name wajib diisi!'); return; }
            if (permissions.length === 0) { showGlobalInvalid('Minimal satu Permission wajib dipilih!'); return; }

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
                    closeUserRoleModal();
                    showGlobalSuccess('User Role berhasil ditambahkan!');
                    loadRoleList();
                } else {
                    showGlobalInvalid(data.message || 'Gagal menambah User Role!');
                }
            });
        };

        // Helper to create basic_select DOM dynamically for permissions
        function createPermissionSelectElement(selectedId = '', showRemove = false) {
            var row = document.createElement('div');
            row.className = 'permission-row';
            row.style.display = 'flex';
            row.style.alignItems = 'center';
            row.style.gap = '8px';
            row.style.width = '100%';

            var uid = 'perm_' + Math.random().toString(36).substring(2, 9);
            const permsMaster = <?= json_encode($permissions ?? []) ?>;

            var selectedLabel = 'Select Permission Module';
            permsMaster.forEach(function(p) {
                if (String(p.id) === String(selectedId)) {
                    selectedLabel = p.name;
                }
            });

            var optionsHtml = `<div class="custom-select-option option-placeholder ${!selectedId ? 'is-selected' : ''}" onclick="selectCustomOption('${uid}', '', 'Select Permission Module')"><span>Select Permission Module</span></div>`;
            permsMaster.forEach(function(p) {
                var isSel = (String(p.id) === String(selectedId));
                optionsHtml += `<div class="custom-select-option ${isSel ? 'is-selected' : ''}" data-value="${p.id}" onclick="selectCustomOption('${uid}', '${p.id}', '${p.name.replace(/'/g, "\\'")}')">
                    <span>${p.name}</span>
                    <svg class="select-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>`;
            });

            row.innerHTML = `
                <div class="custom-select-wrapper" id="select_wrapper_${uid}" style="flex:1;">
                    <input type="hidden" name="permissions[]" id="${uid}" value="${selectedId}">
                    <button type="button" class="custom-select-trigger ${selectedId ? 'has-value' : ''}" id="select_trigger_${uid}" onclick="toggleCustomSelect(event, '${uid}')">
                        <span class="select-trigger-text" id="select_label_${uid}">${selectedLabel}</span>
                        <svg class="select-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="custom-select-menu" id="select_menu_${uid}">
                        ${optionsHtml}
                    </div>
                </div>
                ${showRemove ? `<button type="button" class="remove-permission-btn" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:10px; width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; cursor:pointer;" onclick="this.parentElement.remove()" title="Remove">&times;</button>` : ''}
            `;
            return row;
        }

        // Edit Role Dynamic Setup
        function openRoleEditModalFromButton(button) {
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var permissionsJson = button.getAttribute('data-permissions');
            var permissionIds = [];
            try { permissionIds = JSON.parse(permissionsJson || '[]'); } catch(e) { permissionIds = []; }

            document.getElementById('edit-role-id').value = id;
            document.getElementById('edit-role-name').value = name;
            var container = document.getElementById('edit-permission-container');
            container.innerHTML = '';

            if (permissionIds.length === 0) permissionIds = [''];

            permissionIds.forEach(function(pId, index) {
                var row = createPermissionSelectElement(pId, index > 0);
                container.appendChild(row);
            });

            document.getElementById('userRoleEditModal').style.display = 'flex';
        }

        document.getElementById('add-edit-permission-btn').onclick = function() {
            var container = document.getElementById('edit-permission-container');
            var row = createPermissionSelectElement('', true);
            container.appendChild(row);
        };

        document.getElementById('add-permission-btn').onclick = function() {
            var container = document.getElementById('permission-container');
            var row = createPermissionSelectElement('', true);
            container.appendChild(row);
        };

        document.getElementById('user-role-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-role-id').value;
            var name = document.getElementById('edit-role-name').value.trim();
            var selects = document.querySelectorAll('#edit-permission-container select');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) { showGlobalInvalid('Role Name wajib diisi!'); return; }
            if (permissions.length === 0) { showGlobalInvalid('Minimal satu Permission wajib dipilih!'); return; }

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

        // --- SLA Handlers ---
        document.getElementById('btn-add-sla').onclick = function() {
            document.getElementById('slaModal').style.display = 'flex';
        };
        function closeSlaModal() { document.getElementById('slaModal').style.display = 'none'; }
        function openSlaEditModal(id, priority, response, resolution) {
            document.getElementById('edit-sla-id').value = id;
            
            // Set basic_select value & UI state
            var priorityMap = {
                'Low': 'Low (P4)',
                'Medium': 'Medium (P3)',
                'High': 'High (P2)',
                'Urgent': 'Urgent (P1)'
            };
            var priorityInput = document.getElementById('edit-sla-priority');
            var priorityLabel = document.getElementById('select_label_edit-sla-priority');
            var priorityTrigger = document.getElementById('select_trigger_edit-sla-priority');
            var priorityMenu = document.getElementById('select_menu_edit-sla-priority');

            if (priorityInput) priorityInput.value = priority;
            if (priorityLabel) priorityLabel.innerText = priorityMap[priority] || priority || 'Select Priority Level';
            if (priorityTrigger) {
                if (priority) priorityTrigger.classList.add('has-value');
                else priorityTrigger.classList.remove('has-value');
            }
            if (priorityMenu) {
                priorityMenu.querySelectorAll('.custom-select-option').forEach(function(opt) {
                    if (opt.getAttribute('data-value') === priority) opt.classList.add('is-selected');
                    else opt.classList.remove('is-selected');
                });
            }

            document.getElementById('edit-sla-response').value = response;
            document.getElementById('edit-sla-resolution').value = resolution;
            document.getElementById('slaEditModal').style.display = 'flex';
        }
        function closeSlaEditModal() { document.getElementById('slaEditModal').style.display = 'none'; }
        function openSlaDeleteModal(id) {
            document.getElementById('delete-sla-id').value = id;
            document.getElementById('slaDeleteModal').style.display = 'flex';
        }
        function closeSlaDeleteModal() { document.getElementById('slaDeleteModal').style.display = 'none'; }

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
                }
            });
        }

        function loadSlaList(page = 1, perPage = 10) {
            fetch('<?= base_url('admin/get_sla_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => { document.getElementById('sla-list').innerHTML = html; });
        }

        document.getElementById('sla-form').onsubmit = function(e) {
            e.preventDefault();
            var priority = document.getElementById('sla-priority').value.trim();
            var response = document.getElementById('sla-response').value.trim();
            var resolution = document.getElementById('sla-resolution').value.trim();

            if (!priority || !response || !resolution) {
                showGlobalInvalid('Harap lengkapi semua kolom SLA!');
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
                if (data.success) {
                    closeSlaModal();
                    showGlobalSuccess('SLA Policy berhasil ditambahkan!');
                    loadSlaList();
                }
            });
        };

        document.getElementById('sla-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-sla-id').value.trim();
            var priority = document.getElementById('edit-sla-priority').value.trim();
            var response = document.getElementById('edit-sla-response').value.trim();
            var resolution = document.getElementById('edit-sla-resolution').value.trim();

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
                if (data.success) {
                    closeSlaEditModal();
                    showGlobalSuccess('SLA Policy berhasil diupdate!');
                    loadSlaList();
                }
            });
        };

        // --- Request Type Handlers ---
        document.getElementById('btn-add-request-type').onclick = function() {
            document.getElementById('requestTypeModal').style.display = 'flex';
        };
        function closeRequestTypeModal() { document.getElementById('requestTypeModal').style.display = 'none'; }
        function openRequestTypeEditModal(id, name, desc, status) {
            document.getElementById('edit-request-type-id').value = id;
            document.getElementById('edit-request-type-name').value = name;
            document.getElementById('edit-request-type-desc').value = desc;
            document.getElementById('edit-request-type-status').value = status;
            document.getElementById('requestTypeEditModal').style.display = 'flex';
        }
        function closeRequestTypeEditModal() { document.getElementById('requestTypeEditModal').style.display = 'none'; }
        function openRequestTypeDeleteModal(id) {
            document.getElementById('delete-request-type-id').value = id;
            document.getElementById('requestTypeDeleteModal').style.display = 'flex';
        }
        function closeRequestTypeDeleteModal() { document.getElementById('requestTypeDeleteModal').style.display = 'none'; }

        function confirmDeleteRequestType() {
            var id = document.getElementById('delete-request-type-id').value;
            fetch('<?= base_url('admin/delete_request_type') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeRequestTypeDeleteModal();
                    showGlobalSuccess('Request Type berhasil dihapus!');
                    loadRequestTypeList();
                } else if(data.invalid) {
                    showGlobalInvalid(data.message || 'Request Type sudah dipakai di SLA.');
                }
            });
        }

        function loadRequestTypeList(page = 1, perPage = 10) {
            fetch('<?= base_url('admin/get_request_type_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => { document.getElementById('request-type-list').innerHTML = html; });
        }

        document.getElementById('request-type-form').onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('request-type-name').value.trim();
            if (!name) { showGlobalInvalid('Request Type Name wajib diisi!'); return; }

            fetch('<?= base_url('admin/add_request_type') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'name=' + encodeURIComponent(name)
                    + '&description=' + encodeURIComponent(document.getElementById('request-type-desc').value)
                    + '&status=' + encodeURIComponent(document.getElementById('request-type-status').value)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('request-type-name').value = '';
                    document.getElementById('request-type-desc').value = '';
                    closeRequestTypeModal();
                    showGlobalSuccess('Request Type berhasil ditambahkan!');
                    loadRequestTypeList();
                }
            });
        };

        document.getElementById('request-type-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-request-type-id').value;
            var name = document.getElementById('edit-request-type-name').value.trim();
            if (!name) { showGlobalInvalid('Request Type Name wajib diisi!'); return; }

            fetch('<?= base_url('admin/edit_request_type') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
                    + '&name=' + encodeURIComponent(name)
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

        // --- FAQ Handlers ---
        document.getElementById('btn-add-faq').onclick = function() {
            document.getElementById('faqModal').style.display = 'flex';
        };
        function closeFaqModal() { document.getElementById('faqModal').style.display = 'none'; }
        function openFaqEditModal(id, title, desc) {
            document.getElementById('edit-faq-id').value = id;
            document.getElementById('edit-faq-title').value = title;
            document.getElementById('edit-faq-desc').value = desc;
            document.getElementById('faqEditModal').style.display = 'flex';
        }
        function closeFaqEditModal() { document.getElementById('faqEditModal').style.display = 'none'; }

        function openFaqDeleteModal(id) {
            showGlobalWarning(
                'Apakah Anda yakin ingin menghapus artikel FAQ ini?',
                function() {
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
            );
        }

        function loadFaqList(page = 1, perPage = 10) {
            fetch('<?= base_url('admin/get_faq_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => { document.getElementById('faq-list').innerHTML = html; });
        }

        document.getElementById('faq-form').onsubmit = function(e) {
            e.preventDefault();
            var title = document.getElementById('faq-title').value.trim();
            var desc = document.getElementById('faq-desc').value.trim();
            if (!title || !desc) { showGlobalInvalid('Title dan Description wajib diisi!'); return; }

            fetch('<?= base_url('admin/add_faq') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'question=' + encodeURIComponent(title) + '&answer=' + encodeURIComponent(desc)
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

        document.getElementById('faq-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-faq-id').value;
            var title = document.getElementById('edit-faq-title').value.trim();
            var desc = document.getElementById('edit-faq-desc').value.trim();
            if (!title || !desc) { showGlobalInvalid('Title dan Description wajib diisi!'); return; }

            fetch('<?= base_url('admin/edit_faq') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id) + '&question=' + encodeURIComponent(title) + '&answer=' + encodeURIComponent(desc)
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

        // Action Dropdown Handler (Reusable Dropdown Component)
        function toggleActionDropdown(e, id) {
            e.stopPropagation();
            var wrapper = document.getElementById('wrapper_' + id);
            if (!wrapper) return;
            var wasOpen = wrapper.classList.contains('is-open');
            closeAllActionDropdowns();
            if (!wasOpen) {
                wrapper.classList.add('is-open');
            }
        }

        function closeAllActionDropdowns() {
            document.querySelectorAll('.action-dropdown-wrapper.is-open').forEach(function(el) {
                el.classList.remove('is-open');
            });
        }

        // Reusable Custom Single Select Dropdown Handler
        function toggleCustomSelect(e, id) {
            e.stopPropagation();
            var wrapper = document.getElementById('select_wrapper_' + id);
            if (!wrapper) return;
            var wasOpen = wrapper.classList.contains('is-open');
            closeAllCustomSelects();
            if (!wasOpen) {
                wrapper.classList.add('is-open');
            }
        }

        function selectCustomOption(id, val, label, onChangeCallback) {
            var input = document.getElementById(id);
            var labelEl = document.getElementById('select_label_' + id);
            var trigger = document.getElementById('select_trigger_' + id);
            var menu = document.getElementById('select_menu_' + id);

            if (input) input.value = val;
            if (labelEl) labelEl.innerText = label;

            if (trigger) {
                if (val !== '') {
                    trigger.classList.add('has-value');
                } else {
                    trigger.classList.remove('has-value');
                }
            }

            if (menu) {
                menu.querySelectorAll('.custom-select-option').forEach(function(opt) {
                    if (opt.getAttribute('data-value') === val) {
                        opt.classList.add('is-selected');
                    } else {
                        opt.classList.remove('is-selected');
                    }
                });
            }

            closeAllCustomSelects();

            if (onChangeCallback && typeof window[onChangeCallback] === 'function') {
                window[onChangeCallback](val, label);
            } else if (onChangeCallback) {
                try {
                    eval(onChangeCallback);
                } catch(err){}
            }
        }

        function closeAllCustomSelects() {
            document.querySelectorAll('.custom-select-wrapper.is-open').forEach(function(el) {
                el.classList.remove('is-open');
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-wrapper')) {
                closeAllActionDropdowns();
            }
            if (!e.target.closest('.custom-select-wrapper')) {
                closeAllCustomSelects();
            }
        });

        // ESC handler to close any active modal or dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                closeAllActionDropdowns();
                closeUserRoleModal();
                closeUserRoleEditModal();
                closeUserRoleDeleteModal();
                closeSlaModal();
                closeSlaEditModal();
                closeSlaDeleteModal();
                closeRequestTypeModal();
                closeRequestTypeEditModal();
                closeRequestTypeDeleteModal();
                closeFaqModal();
                closeFaqEditModal();
            }
        });
    </script>
    <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
    <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
</body>
</html>