/**
 * Admin System Settings Module
 * Handles tab switching, search filtering, dynamic select inputs, and CRUD for:
 * - Roles & Permissions
 * - SLA Policies
 * - Request Types
 * - FAQ Articles
 */

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

// Auto activate initial tab from URL params if present
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var initialTab = urlParams.get('tab') || window.SYSTEM_SETTINGS_ACTIVE_TAB || 'roles';
    if (initialTab && ['roles', 'sla', 'request_type', 'faq'].includes(initialTab)) {
        switchSettingsTab(initialTab);
    }
});

// Live Role Filter by Text
function filterRolesTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#user-role-list tbody tr');
    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// --- User Role Handlers ---
document.addEventListener('DOMContentLoaded', function() {
    var btnAddRole = document.getElementById('btn-add-user-role');
    if (btnAddRole) {
        btnAddRole.onclick = function() {
            var modal = document.getElementById('userRoleModal');
            if (modal) modal.style.display = 'flex';
        };
    }

    var addPermBtn = document.getElementById('add-permission-btn');
    if (addPermBtn) {
        addPermBtn.onclick = function() {
            var container = document.getElementById('permission-container');
            if (container) {
                var row = createPermissionSelectElement('', true);
                container.appendChild(row);
            }
        };
    }

    var addEditPermBtn = document.getElementById('add-edit-permission-btn');
    if (addEditPermBtn) {
        addEditPermBtn.onclick = function() {
            var container = document.getElementById('edit-permission-container');
            if (container) {
                var row = createPermissionSelectElement('', true);
                container.appendChild(row);
            }
        };
    }

    var roleForm = document.getElementById('user-role-form');
    if (roleForm) {
        roleForm.onsubmit = function(e) {
            e.preventDefault();
            var name = (document.getElementById('role-name')?.value || '').trim();
            var selects = document.querySelectorAll('#permission-container select, #permission-container input[name="permissions[]"]');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) { showGlobalInvalid('Role Name wajib diisi!'); return; }
            if (permissions.length === 0) { showGlobalInvalid('Minimal satu Permission wajib dipilih!'); return; }

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.addRole) || '/admin/add_user_role';

            fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'name=' + encodeURIComponent(name)
                    + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    var inputName = document.getElementById('role-name');
                    if (inputName) inputName.value = '';
                    closeUserRoleModal();
                    showGlobalSuccess('User Role berhasil ditambahkan!');
                    loadRoleList();
                } else {
                    showGlobalInvalid(data.message || 'Gagal menambah User Role!');
                }
            });
        };
    }

    var roleEditForm = document.getElementById('user-role-edit-form');
    if (roleEditForm) {
        roleEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-role-id')?.value || '';
            var name = (document.getElementById('edit-role-name')?.value || '').trim();
            var selects = document.querySelectorAll('#edit-permission-container select, #edit-permission-container input[name="permissions[]"]');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) { showGlobalInvalid('Role Name wajib diisi!'); return; }
            if (permissions.length === 0) { showGlobalInvalid('Minimal satu Permission wajib dipilih!'); return; }

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.editRole) || '/admin/edit_user_role';

            fetch(endpoint, {
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
    }
});

function closeUserRoleModal() {
    var modal = document.getElementById('userRoleModal');
    if (modal) modal.style.display = 'none';
}

function closeUserRoleEditModal() {
    var modal = document.getElementById('userRoleEditModal');
    if (modal) modal.style.display = 'none';
}

function openRoleDeleteModal(id) {
    var input = document.getElementById('delete-role-id');
    if (input) input.value = id;
    var modal = document.getElementById('userRoleDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeUserRoleDeleteModal() {
    var modal = document.getElementById('userRoleDeleteModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteUserRole() {
    var id = document.getElementById('delete-role-id')?.value;
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.deleteRole) || '/admin/delete_user_role';

    fetch(endpoint, {
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
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.getRoleList) || '/admin/get_user_role_list';
    fetch(endpoint + '?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        var el = document.getElementById('user-role-list');
        if (el) el.innerHTML = html;
    });
}

// Helper to create basic_select DOM dynamically for permissions
function createPermissionSelectElement(selectedId = '', showRemove = false) {
    var row = document.createElement('div');
    row.className = 'permission-row';
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '8px';
    row.style.width = '100%';

    var uid = 'perm_' + Math.random().toString(36).substring(2, 9);
    var permsMaster = window.SYSTEM_SETTINGS_PERMISSIONS || [];

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

    var editId = document.getElementById('edit-role-id');
    var editName = document.getElementById('edit-role-name');
    if (editId) editId.value = id;
    if (editName) editName.value = name;

    var container = document.getElementById('edit-permission-container');
    if (container) {
        container.innerHTML = '';
        if (permissionIds.length === 0) permissionIds = [''];
        permissionIds.forEach(function(pId, index) {
            var row = createPermissionSelectElement(pId, index > 0);
            container.appendChild(row);
        });
    }

    var modal = document.getElementById('userRoleEditModal');
    if (modal) modal.style.display = 'flex';
}

// --- SLA Handlers ---
document.addEventListener('DOMContentLoaded', function() {
    var btnAddSla = document.getElementById('btn-add-sla');
    if (btnAddSla) {
        btnAddSla.onclick = function() {
            var modal = document.getElementById('slaModal');
            if (modal) modal.style.display = 'flex';
        };
    }

    var slaForm = document.getElementById('sla-form');
    if (slaForm) {
        slaForm.onsubmit = function(e) {
            e.preventDefault();
            var priority = (document.getElementById('sla-priority')?.value || '').trim();
            var response = (document.getElementById('sla-response')?.value || '').trim();
            var resolution = (document.getElementById('sla-resolution')?.value || '').trim();

            if (!priority || !response || !resolution) {
                showGlobalInvalid('Harap lengkapi semua kolom SLA!');
                return;
            }

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.addSla) || '/admin/add_sla';

            fetch(endpoint, {
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
    }

    var slaEditForm = document.getElementById('sla-edit-form');
    if (slaEditForm) {
        slaEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = (document.getElementById('edit-sla-id')?.value || '').trim();
            var priority = (document.getElementById('edit-sla-priority')?.value || '').trim();
            var response = (document.getElementById('edit-sla-response')?.value || '').trim();
            var resolution = (document.getElementById('edit-sla-resolution')?.value || '').trim();

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.editSla) || '/admin/edit_sla';

            fetch(endpoint, {
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
    }
});

function closeSlaModal() {
    var modal = document.getElementById('slaModal');
    if (modal) modal.style.display = 'none';
}

function openSlaEditModal(id, priority, response, resolution) {
    var editId = document.getElementById('edit-sla-id');
    if (editId) editId.value = id;

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

    var respInput = document.getElementById('edit-sla-response');
    var resoInput = document.getElementById('edit-sla-resolution');
    if (respInput) respInput.value = response;
    if (resoInput) resoInput.value = resolution;

    var modal = document.getElementById('slaEditModal');
    if (modal) modal.style.display = 'flex';
}

function closeSlaEditModal() {
    var modal = document.getElementById('slaEditModal');
    if (modal) modal.style.display = 'none';
}

function openSlaDeleteModal(id) {
    var input = document.getElementById('delete-sla-id');
    if (input) input.value = id;
    var modal = document.getElementById('slaDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeSlaDeleteModal() {
    var modal = document.getElementById('slaDeleteModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteSla() {
    var id = document.getElementById('delete-sla-id')?.value;
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.deleteSla) || '/admin/delete_sla';

    fetch(endpoint, {
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
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.getSlaList) || '/admin/get_sla_list';
    fetch(endpoint + '?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        var el = document.getElementById('sla-list');
        if (el) el.innerHTML = html;
    });
}

// --- Request Type Handlers ---
document.addEventListener('DOMContentLoaded', function() {
    var btnAddReq = document.getElementById('btn-add-request-type');
    if (btnAddReq) {
        btnAddReq.onclick = function() {
            var modal = document.getElementById('requestTypeModal');
            if (modal) modal.style.display = 'flex';
        };
    }

    var reqForm = document.getElementById('request-type-form');
    if (reqForm) {
        reqForm.onsubmit = function(e) {
            e.preventDefault();
            var name = (document.getElementById('request-type-name')?.value || '').trim();
            var categoryId = (document.getElementById('request-type-category')?.value || '').trim();
            if (!name) { showGlobalInvalid('Request Type Name wajib diisi!'); return; }
            if (!categoryId) { showGlobalInvalid('Pilih Main Category (Topik HC) terlebih dahulu!'); return; }

            var isPopular = document.getElementById('request-type-popular')?.checked ? 1 : 0;
            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.addRequestType) || '/admin/add_request_type';

            fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'name=' + encodeURIComponent(name)
                    + '&category_id=' + encodeURIComponent(categoryId)
                    + '&sla_id=' + encodeURIComponent(document.getElementById('request-type-sla')?.value || '')
                    + '&badge_tag=' + encodeURIComponent(document.getElementById('request-type-badge')?.value || '')
                    + '&is_popular=' + encodeURIComponent(isPopular)
                    + '&description=' + encodeURIComponent(document.getElementById('request-type-desc')?.value || '')
                    + '&status=' + encodeURIComponent(document.getElementById('request-type-status')?.value || '')
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    var elName = document.getElementById('request-type-name');
                    var elDesc = document.getElementById('request-type-desc');
                    var elBadge = document.getElementById('request-type-badge');
                    var elCat = document.getElementById('request-type-category');
                    var elSla = document.getElementById('request-type-sla');
                    var elPop = document.getElementById('request-type-popular');
                    if (elName) elName.value = '';
                    if (elDesc) elDesc.value = '';
                    if (elBadge) elBadge.value = '';
                    if (elCat) elCat.value = '';
                    if (elSla) elSla.value = '';
                    if (elPop) elPop.checked = false;

                    closeRequestTypeModal();
                    showGlobalSuccess('Request Type berhasil ditambahkan!');
                    loadRequestTypeList();
                }
            });
        };
    }

    var reqEditForm = document.getElementById('request-type-edit-form');
    if (reqEditForm) {
        reqEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-request-type-id')?.value;
            var name = (document.getElementById('edit-request-type-name')?.value || '').trim();
            var categoryId = (document.getElementById('edit-request-type-category')?.value || '').trim();
            if (!name) { showGlobalInvalid('Request Type Name wajib diisi!'); return; }
            if (!categoryId) { showGlobalInvalid('Pilih Main Category (Topik HC) terlebih dahulu!'); return; }

            var isPopular = document.getElementById('edit-request-type-popular')?.checked ? 1 : 0;
            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.editRequestType) || '/admin/edit_request_type';

            fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
                    + '&name=' + encodeURIComponent(name)
                    + '&category_id=' + encodeURIComponent(categoryId)
                    + '&sla_id=' + encodeURIComponent(document.getElementById('edit-request-type-sla')?.value || '')
                    + '&badge_tag=' + encodeURIComponent(document.getElementById('edit-request-type-badge')?.value || '')
                    + '&is_popular=' + encodeURIComponent(isPopular)
                    + '&description=' + encodeURIComponent(document.getElementById('edit-request-type-desc')?.value || '')
                    + '&status=' + encodeURIComponent(document.getElementById('edit-request-type-status')?.value || '')
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
    }
});

function closeRequestTypeModal() {
    var modal = document.getElementById('requestTypeModal');
    if (modal) modal.style.display = 'none';
}

function openRequestTypeEditModal(id, name, categoryId, slaId, badgeTag, isPopular, desc, status) {
    var elId = document.getElementById('edit-request-type-id');
    var elName = document.getElementById('edit-request-type-name');
    var elCat = document.getElementById('edit-request-type-category');
    var elSla = document.getElementById('edit-request-type-sla');
    var elBadge = document.getElementById('edit-request-type-badge');
    var elPop = document.getElementById('edit-request-type-popular');
    var elDesc = document.getElementById('edit-request-type-desc');
    var elStat = document.getElementById('edit-request-type-status');

    if (elId) elId.value = id;
    if (elName) elName.value = name;
    if (elCat) elCat.value = categoryId || '';
    if (elSla) elSla.value = slaId || '';
    if (elBadge) elBadge.value = badgeTag || '';
    if (elPop) elPop.checked = (parseInt(isPopular) === 1);
    if (elDesc) elDesc.value = desc;
    if (elStat) elStat.value = status;

    var modal = document.getElementById('requestTypeEditModal');
    if (modal) modal.style.display = 'flex';
}

function closeRequestTypeEditModal() {
    var modal = document.getElementById('requestTypeEditModal');
    if (modal) modal.style.display = 'none';
}

function openRequestTypeDeleteModal(id) {
    var input = document.getElementById('delete-request-type-id');
    if (input) input.value = id;
    var modal = document.getElementById('requestTypeDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeRequestTypeDeleteModal() {
    var modal = document.getElementById('requestTypeDeleteModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteRequestType() {
    var id = document.getElementById('delete-request-type-id')?.value;
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.deleteRequestType) || '/admin/delete_request_type';

    fetch(endpoint, {
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
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.getRequestTypeList) || '/admin/get_request_type_list';
    fetch(endpoint + '?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        var el = document.getElementById('request-type-list');
        if (el) el.innerHTML = html;
    });
}

// --- FAQ Handlers ---
document.addEventListener('DOMContentLoaded', function() {
    var btnAddFaq = document.getElementById('btn-add-faq');
    if (btnAddFaq) {
        btnAddFaq.onclick = function() {
            var modal = document.getElementById('faqModal');
            if (modal) modal.style.display = 'flex';
        };
    }

    var faqForm = document.getElementById('faq-form');
    if (faqForm) {
        faqForm.onsubmit = function(e) {
            e.preventDefault();
            var title = (document.getElementById('faq-title')?.value || '').trim();
            var desc = (document.getElementById('faq-desc')?.value || '').trim();
            if (!title || !desc) { showGlobalInvalid('Title dan Description wajib diisi!'); return; }

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.addFaq) || '/admin/add_faq';

            fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'question=' + encodeURIComponent(title) + '&answer=' + encodeURIComponent(desc)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    var elTitle = document.getElementById('faq-title');
                    var elDesc = document.getElementById('faq-desc');
                    if (elTitle) elTitle.value = '';
                    if (elDesc) elDesc.value = '';
                    closeFaqModal();
                    showGlobalSuccess('FAQ berhasil ditambahkan!');
                    loadFaqList();
                }
            });
        };
    }

    var faqEditForm = document.getElementById('faq-edit-form');
    if (faqEditForm) {
        faqEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-faq-id')?.value;
            var title = (document.getElementById('edit-faq-title')?.value || '').trim();
            var desc = (document.getElementById('edit-faq-desc')?.value || '').trim();
            if (!title || !desc) { showGlobalInvalid('Title dan Description wajib diisi!'); return; }

            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.editFaq) || '/admin/edit_faq';

            fetch(endpoint, {
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
    }
});

function closeFaqModal() {
    var modal = document.getElementById('faqModal');
    if (modal) modal.style.display = 'none';
}

function openFaqEditModal(id, title, desc) {
    var elId = document.getElementById('edit-faq-id');
    var elTitle = document.getElementById('edit-faq-title');
    var elDesc = document.getElementById('edit-faq-desc');
    if (elId) elId.value = id;
    if (elTitle) elTitle.value = title;
    if (elDesc) elDesc.value = desc;

    var modal = document.getElementById('faqEditModal');
    if (modal) modal.style.display = 'flex';
}

function closeFaqEditModal() {
    var modal = document.getElementById('faqEditModal');
    if (modal) modal.style.display = 'none';
}

function openFaqDeleteModal(id) {
    showGlobalWarning(
        'Apakah Anda yakin ingin menghapus artikel FAQ ini?',
        function() {
            var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.deleteFaq) || '/admin/delete_faq';
            fetch(endpoint, {
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
    var endpoint = (window.SYSTEM_SETTINGS_ENDPOINTS && window.SYSTEM_SETTINGS_ENDPOINTS.getFaqList) || '/admin/get_faq_list';
    fetch(endpoint + '?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        var el = document.getElementById('faq-list');
        if (el) el.innerHTML = html;
    });
}

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
