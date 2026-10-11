/**
 * Admin Roles & Permissions Management Module
 * Handles dynamic permission select generation, CRUD AJAX, modal transitions, and table filtering.
 */

// Live Role Filter by Text
function filterRolesTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#user-role-list tbody tr');
    rows.forEach(function(r) {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// Modal Toggle Handlers
function openUserRoleModal() {
    var modal = document.getElementById('userRoleModal');
    if (modal) modal.style.display = 'flex';
}

function closeUserRoleModal() {
    var modal = document.getElementById('userRoleModal');
    if (modal) modal.style.display = 'none';
}

function closeUserRoleEditModal() {
    var modal = document.getElementById('userRoleEditModal');
    if (modal) modal.style.display = 'none';
}

function openRoleDeleteModal(id) {
    var idInput = document.getElementById('delete-role-id');
    if (idInput) idInput.value = id;
    var modal = document.getElementById('userRoleDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeUserRoleDeleteModal() {
    var modal = document.getElementById('userRoleDeleteModal');
    if (modal) modal.style.display = 'none';
}

// Confirm Delete Role via AJAX
function confirmDeleteUserRole() {
    var idInput = document.getElementById('delete-role-id');
    if (!idInput) return;
    var id = idInput.value;
    var url = window.ROLE_ENDPOINTS ? window.ROLE_ENDPOINTS.deleteUrl : '/admin/delete_user_role';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data && data.success) {
            closeUserRoleDeleteModal();
            if (typeof showGlobalSuccess === 'function') {
                showGlobalSuccess('User Role berhasil dihapus!');
            }
            loadRoleList();
        } else {
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid((data && data.message) || 'Gagal menghapus User Role!');
            }
        }
    })
    .catch(function() {
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Terjadi kesalahan jaringan.');
        }
    });
}

// Async reload role list table
function loadRoleList(page, perPage) {
    page = page || 1;
    perPage = perPage || 5;
    var url = window.ROLE_ENDPOINTS ? window.ROLE_ENDPOINTS.listUrl : '/admin/get_user_role_list';

    fetch(url + '?page=' + page + '&per_page=' + perPage, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        var listContainer = document.getElementById('user-role-list');
        if (listContainer) listContainer.innerHTML = html;
    });
}

// Helper to create custom select DOM dynamically for permissions
function createPermissionSelectElement(selectedId, showRemove) {
    selectedId = selectedId || '';
    showRemove = !!showRemove;

    var row = document.createElement('div');
    row.className = 'permission-row';
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '8px';
    row.style.width = '100%';

    var uid = 'perm_' + Math.random().toString(36).substring(2, 9);
    var permsMaster = window.PERMISSIONS_MASTER || [];

    var selectedLabel = 'Select Permission Module';
    permsMaster.forEach(function(p) {
        if (String(p.id) === String(selectedId)) {
            selectedLabel = p.name;
        }
    });

    var optionsHtml = '<div class="custom-select-option option-placeholder ' + (!selectedId ? 'is-selected' : '') + '" onclick="selectCustomOption(\'' + uid + '\', \'\', \'Select Permission Module\')"><span>Select Permission Module</span></div>';
    permsMaster.forEach(function(p) {
        var isSel = (String(p.id) === String(selectedId));
        var safeName = (p.name || '').replace(/'/g, "\\'");
        optionsHtml += '<div class="custom-select-option ' + (isSel ? 'is-selected' : '') + '" data-value="' + p.id + '" onclick="selectCustomOption(\'' + uid + '\', \'' + p.id + '\', \'' + safeName + '\')">'
            + '<span>' + p.name + '</span>'
            + '<svg class="select-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>'
            + '</div>';
    });

    row.innerHTML = 
        '<div class="custom-select-wrapper" id="select_wrapper_' + uid + '" style="flex:1;">' +
            '<input type="hidden" name="permissions[]" id="' + uid + '" value="' + selectedId + '">' +
            '<button type="button" class="custom-select-trigger ' + (selectedId ? 'has-value' : '') + '" id="select_trigger_' + uid + '" onclick="toggleCustomSelect(event, \'' + uid + '\')">' +
                '<span class="select-trigger-text" id="select_label_' + uid + '">' + selectedLabel + '</span>' +
                '<svg class="select-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<polyline points="6 9 12 15 18 9"></polyline>' +
                '</svg>' +
            '</button>' +
            '<div class="custom-select-menu" id="select_menu_' + uid + '">' +
                optionsHtml +
            '</div>' +
        '</div>' +
        (showRemove ? '<button type="button" class="remove-permission-btn" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:10px; width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; cursor:pointer;" onclick="this.parentElement.remove()" title="Remove">&times;</button>' : '');

    return row;
}

// Setup edit modal when user clicks Edit button in table
function openRoleEditModalFromButton(button) {
    if (!button) return;
    var id = button.getAttribute('data-id');
    var name = button.getAttribute('data-name');
    var permissionsJson = button.getAttribute('data-permissions');
    var permissionIds = [];
    try { permissionIds = JSON.parse(permissionsJson || '[]'); } catch(e) { permissionIds = []; }

    var idInput = document.getElementById('edit-role-id');
    var nameInput = document.getElementById('edit-role-name');
    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;

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

// DOM Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Open add role modal
    var btnAdd = document.getElementById('btn-add-user-role');
    if (btnAdd) {
        btnAdd.onclick = openUserRoleModal;
    }

    // Add more permission buttons
    var btnAddPerm = document.getElementById('add-permission-btn');
    if (btnAddPerm) {
        btnAddPerm.onclick = function() {
            var container = document.getElementById('permission-container');
            if (container) {
                var row = createPermissionSelectElement('', true);
                container.appendChild(row);
            }
        };
    }

    var btnAddEditPerm = document.getElementById('add-edit-permission-btn');
    if (btnAddEditPerm) {
        btnAddEditPerm.onclick = function() {
            var container = document.getElementById('edit-permission-container');
            if (container) {
                var row = createPermissionSelectElement('', true);
                container.appendChild(row);
            }
        };
    }

    // Submit Add Role Form
    var addForm = document.getElementById('user-role-form');
    if (addForm) {
        addForm.onsubmit = function(e) {
            e.preventDefault();
            var nameInput = document.getElementById('role-name');
            var name = nameInput ? nameInput.value.trim() : '';
            var selects = document.querySelectorAll('#permission-container input[name="permissions[]"]');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Role Name wajib diisi!');
                return;
            }
            if (permissions.length === 0) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Minimal satu Permission wajib dipilih!');
                return;
            }

            var url = window.ROLE_ENDPOINTS ? window.ROLE_ENDPOINTS.addUrl : '/admin/add_user_role';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'name=' + encodeURIComponent(name)
                    + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    if (nameInput) nameInput.value = '';
                    closeUserRoleModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('User Role berhasil ditambahkan!');
                    }
                    loadRoleList();
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal menambah User Role!');
                    }
                }
            })
            .catch(function() {
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan jaringan.');
                }
            });
        };
    }

    // Submit Edit Role Form
    var editForm = document.getElementById('user-role-edit-form');
    if (editForm) {
        editForm.onsubmit = function(e) {
            e.preventDefault();
            var idInput = document.getElementById('edit-role-id');
            var nameInput = document.getElementById('edit-role-name');
            var id = idInput ? idInput.value : '';
            var name = nameInput ? nameInput.value.trim() : '';
            var selects = document.querySelectorAll('#edit-permission-container input[name="permissions[]"]');
            var permissions = [];
            selects.forEach(function(sel) { if (sel.value) permissions.push(sel.value); });

            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Role Name wajib diisi!');
                return;
            }
            if (permissions.length === 0) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Minimal satu Permission wajib dipilih!');
                return;
            }

            var url = window.ROLE_ENDPOINTS ? window.ROLE_ENDPOINTS.editUrl : '/admin/edit_user_role';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(id)
                    + '&name=' + encodeURIComponent(name)
                    + '&permissions[]=' + permissions.map(encodeURIComponent).join('&permissions[]=')
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    closeUserRoleEditModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Perubahan User Role berhasil disimpan!');
                    }
                    loadRoleList();
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal mengubah User Role!');
                    }
                }
            })
            .catch(function() {
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan jaringan.');
                }
            });
        };
    }
});
