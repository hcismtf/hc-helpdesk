// System Settings JavaScript
// All functions for FAQ, User Roles, Request Types, and SLA Management

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

// Event listener untuk Edit User Role
document.addEventListener('DOMContentLoaded', function() {
    console.log('System Settings JS loaded');
    console.log('btn-add-user-role found:', !!document.getElementById('btn-add-user-role'));
    
    // Validasi untuk Add User Role form
    if (document.getElementById('role-name')) {
        document.getElementById('role-name').addEventListener('input', function() {
            validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
        });
    }
    if (document.getElementById('permission-container')) {
        document.getElementById('permission-container').addEventListener('change', function() {
            validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
        });
    }
    // Note: add-permission-btn handler is set up later in the script

    if (document.getElementById('edit-role-name')) {
        document.getElementById('edit-role-name').addEventListener('input', function() {
            validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
        });
        document.getElementById('edit-permission-container').addEventListener('change', function() {
            validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
        });
        // Note: add-edit-permission-btn handler is set up later in the script
    }

    // Validasi sebelum submit (Edit)
    if (document.getElementById('user-role-edit-form')) {
        document.getElementById('user-role-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-role-id').value;
            var name = document.getElementById('edit-role-name').value.trim();
            var selects = document.querySelectorAll('#edit-permission-container select');
            var permissions = [];
            selects.forEach(function(sel) {
                if (sel.value) permissions.push(sel.value);
            });

            if (!name) {
                showGlobalInvalid('Role Name wajib diisi!');
                return;
            }
            if (permissions.length === 0) {
                showGlobalInvalid('Minimal satu Permission wajib dipilih!');
                return;
            }
            
            fetch(baseUrl + 'admin/edit_user_role', {
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

    // User Role form submit
    if (document.getElementById('user-role-form')) {
        document.getElementById('user-role-form').onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('role-name').value.trim();
            var selects = document.querySelectorAll('#permission-container select');
            var permissions = [];
            selects.forEach(function(sel) {
                if (sel.value) permissions.push(sel.value);
            });

            if (!name) {
                showGlobalInvalid('Role Name wajib diisi!');
                return;
            }
            if (permissions.length === 0) {
                showGlobalInvalid('Minimal satu Permission wajib dipilih!');
                return;
            }

            fetch(baseUrl + 'admin/add_user_role', {
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
                } else {
                    showGlobalInvalid(data.message || 'Gagal menambah User Role!');
                }
            })
            .catch(error => {
                showGlobalInvalid('Error: ' + error.message);
            });
        };
    }

    // Add Permission Button
    if (document.getElementById('add-permission-btn')) {
        document.getElementById('add-permission-btn').onclick = function(e) {
            e.preventDefault();
            var container = document.getElementById('permission-container');
            var div = document.createElement('div');
            div.className = 'permission-row';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.gap = '8px';
            var select = document.createElement('select');
            select.name = "permissions[]";
            select.className = "faq-input permission-select";
            select.style.flex = '1';
            select.style.marginBottom = '8px';
            select.innerHTML = permissionOptionsHtml;
            
            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-permission-btn';
            removeBtn.textContent = 'Remove';
            removeBtn.style.background = '#932825';
            removeBtn.style.color = '#fff';
            removeBtn.style.border = 'none';
            removeBtn.style.borderRadius = '29px';
            removeBtn.style.padding = '8px 16px';
            removeBtn.style.fontSize = '15px';
            removeBtn.style.fontWeight = '600';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.marginBottom = '8px';
            removeBtn.style.whiteSpace = 'nowrap';
            removeBtn.style.height = '44px';
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                div.remove();
                updatePermissionDropdowns();
                validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
            });
            
            div.appendChild(select);
            div.appendChild(removeBtn);
            container.appendChild(div);
            updatePermissionDropdowns();
            validateRoleForm('user-role-form', 'role-name', 'permission-container', '#user-role-form .btn-submit-faq');
        };
    }

    // Remove permission row
    var permContainer = document.getElementById('permission-container');
    if (permContainer) {
        permContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-permission-btn')) {
                e.target.parentElement.remove();
                updatePermissionDropdowns();
            }
        });

        permContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('permission-select')) {
                updatePermissionDropdowns();
            }
        });

        updatePermissionDropdowns();
    }

    // User Role Add Button
    var btnAddRole = document.getElementById('btn-add-user-role');
    if (btnAddRole) {
        btnAddRole.addEventListener('click', function() {
            openUserRoleModal();
        });
    }

    // FAQ Functions
    if (document.getElementById('btn-add-faq')) {
        document.getElementById('btn-add-faq').onclick = function() {
            document.getElementById('faqModal').style.display = 'flex';
        };
    }

    if (document.getElementById('faq-form')) {
        document.getElementById('faq-form').onsubmit = function(e) {
            e.preventDefault();
            var title = document.getElementById('faq-title').value.trim();
            var desc = document.getElementById('faq-desc').value.trim();
            
            if (!title) {
                showGlobalInvalid('Title wajib diisi!');
                return;
            }
            if (!desc) {
                showGlobalInvalid('Description wajib diisi!');
                return;
            }
            
            fetch(baseUrl + 'admin/add_faq', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'question=' + encodeURIComponent(title)
                    + '&answer=' + encodeURIComponent(desc)
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
    }

    if (document.getElementById('faq-edit-form')) {
        document.getElementById('faq-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var title = document.getElementById('edit-faq-title').value.trim();
            var desc = document.getElementById('edit-faq-desc').value.trim();
            
            if (!title) {
                showGlobalInvalid('Title wajib diisi!');
                return;
            }
            if (!desc) {
                showGlobalInvalid('Description wajib diisi!');
                return;
            }
            
            fetch(baseUrl + 'admin/edit_faq', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(document.getElementById('edit-faq-id').value)
                    + '&question=' + encodeURIComponent(title)
                    + '&answer=' + encodeURIComponent(desc)
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

    // Request Type Functions
    if (document.getElementById('btn-add-request-type')) {
        document.getElementById('btn-add-request-type').onclick = function() {
            document.getElementById('requestTypeModal').style.display = 'flex';
        };
    }

    if (document.getElementById('request-type-form')) {
        document.getElementById('request-type-form').onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('request-type-name').value.trim();
            
            if (!name) {
                showGlobalInvalid('Request Type Name wajib diisi!');
                return;
            }
            
            fetch(baseUrl + 'admin/add_request_type', {
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
                    document.getElementById('request-type-status').value = 'Active';
                    closeRequestTypeModal();
                    showGlobalSuccess('Request Type berhasil ditambahkan!');
                    loadRequestTypeList();
                }
            });
        };
    }

    if (document.getElementById('request-type-edit-form')) {
        document.getElementById('request-type-edit-form').onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('edit-request-type-name').value.trim();
            
            if (!name) {
                showGlobalInvalid('Request Type Name wajib diisi!');
                return;
            }
            
            fetch(baseUrl + 'admin/edit_request_type', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(document.getElementById('edit-request-type-id').value)
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
    }

    // SLA Functions
    if (document.getElementById('btn-add-sla')) {
        document.getElementById('btn-add-sla').onclick = function() {
            document.getElementById('slaModal').style.display = 'flex';
        };
    }

    if (document.getElementById('sla-form')) {
        document.getElementById('sla-form').onsubmit = function(e) {
            e.preventDefault();

            var priority = document.getElementById('sla-priority').value.trim();
            var response = document.getElementById('sla-response').value.trim();
            var resolution = document.getElementById('sla-resolution').value.trim();

            if (!priority) {
                showGlobalInvalid('Priority Level wajib dipilih!');
                return;
            }
            if (!response) {
                showGlobalInvalid('Response Time wajib diisi!');
                return;
            }
            if (!resolution) {
                showGlobalInvalid('Resolution Time wajib diisi!');
                return;
            }

            if (!/^[0-9]+$/.test(response) || !/^[0-9]+$/.test(resolution)) {
                showGlobalInvalid('Response Time dan Resolution Time harus berupa angka bulat!');
                return;
            }

            response = parseInt(response);
            resolution = parseInt(resolution);

            if (response <= 0 || resolution <= 0) {
                showGlobalInvalid('Nilai Response Time dan Resolution Time harus lebih dari 0!');
                return;
            }

            fetch(baseUrl + 'admin/add_sla', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'priority=' + encodeURIComponent(priority)
                    + '&response_time=' + encodeURIComponent(response)
                    + '&resolution_time=' + encodeURIComponent(resolution)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('sla-priority').value = '';
                    document.getElementById('sla-response').value = '';
                    document.getElementById('sla-resolution').value = '';
                    closeSlaModal();
                    showGlobalSuccess('SLA berhasil ditambahkan!');
                    loadSlaList();
                }
            });
        };
    }

    if (document.getElementById('sla-edit-form')) {
        document.getElementById('sla-edit-form').onsubmit = function(e) {
            e.preventDefault();

            var id = document.getElementById('edit-sla-id').value.trim();
            var priority = document.getElementById('edit-sla-priority').value.trim();
            var response = document.getElementById('edit-sla-response').value.trim();
            var resolution = document.getElementById('edit-sla-resolution').value.trim();

            if (!priority) {
                showGlobalInvalid('Priority Level wajib dipilih!');
                return;
            }
            if (!response) {
                showGlobalInvalid('Response Time wajib diisi!');
                return;
            }
            if (!resolution) {
                showGlobalInvalid('Resolution Time wajib diisi!');
                return;
            }

            if (!/^[0-9]+$/.test(response) || !/^[0-9]+$/.test(resolution)) {
                showGlobalInvalid('Response Time dan Resolution Time harus berupa angka bulat!');
                return;
            }

            response = parseInt(response);
            resolution = parseInt(resolution);

            if (response <= 0 || resolution <= 0) {
                showGlobalInvalid('Nilai Response Time dan Resolution Time harus lebih dari 0!');
                return;
            }

            fetch(baseUrl + 'admin/edit_sla', {
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
                    showGlobalSuccess('SLA berhasil diupdate!');
                    loadSlaList();
                }
            });
        };
    }

    // Initial setup
    moveSlider(0);
    loadFaqList(1, 10);

    if (document.querySelector('.faq-pagination')) {
        document.querySelector('.faq-pagination').onchange = function() {
            loadFaqList(1, this.value);
        };
    }
});

// Modal Functions
function openUserRoleModal() {
    // Reset form
    document.getElementById('role-name').value = '';
    var container = document.getElementById('permission-container');
    container.innerHTML = '';
    
    var div = document.createElement('div');
    div.className = 'permission-row';
    div.style.display = 'flex';
    div.style.alignItems = 'center';
    div.style.gap = '8px';
    div.style.width = '100%';
    
    var select = document.createElement('select');
    select.name = "permissions[]";
    select.className = "faq-input permission-select";
    select.style.flex = '1';
    select.innerHTML = permissionOptionsHtml;
    
    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'remove-permission-btn';
    removeBtn.textContent = '-';
    removeBtn.style.display = 'none';
    removeBtn.style.background = '#bbb';
    removeBtn.style.color = '#fff';
    removeBtn.style.border = 'none';
    removeBtn.style.borderRadius = '12px';
    removeBtn.style.padding = '6px 12px';
    removeBtn.style.fontSize = '15px';
    removeBtn.style.fontWeight = '600';
    removeBtn.style.cursor = 'pointer';
    
    div.appendChild(select);
    div.appendChild(removeBtn);
    container.appendChild(div);
    
    document.getElementById('userRoleModal').style.display = 'flex';
}

function openRoleEditModalFromButton(button) {
    var id = button.getAttribute('data-id');
    var name = button.getAttribute('data-name');
    var permissionsJson = button.getAttribute('data-permissions');
    var permissionIds = [];
    
    try {
        permissionIds = JSON.parse(permissionsJson || '[]');
    } catch(e) {
        console.error('Error parsing permissions JSON:', permissionsJson, e);
        permissionIds = [];
    }
    
    openRoleEditModal(id, name, permissionIds);
}

function openRoleEditModal(id, name, permissionIds) {
    if (!Array.isArray(permissionIds)) {
        permissionIds = [];
    }
    
    document.getElementById('edit-role-id').value = id;
    document.getElementById('edit-role-name').value = name;
    var container = document.getElementById('edit-permission-container');
    container.innerHTML = '';
    
    permissionIds.forEach(function(pId) {
        var select = document.createElement('select');
        select.name = "permissions[]";
        select.className = "faq-input";
        select.style.marginBottom = "8px";
        select.style.width = "100%";
        select.innerHTML = permissionOptionsHtml;
        select.value = pId;
        container.appendChild(select);
    });
    
    if (permissionIds.length === 0) {
        var select = document.createElement('select');
        select.name = "permissions[]";
        select.className = "faq-input";
        select.style.marginBottom = "8px";
        select.style.width = "100%";
        select.innerHTML = permissionOptionsHtml;
        container.appendChild(select);
    }
    
    document.getElementById('userRoleEditModal').style.display = 'flex';
}

function closeUserRoleEditModal() {
    document.getElementById('userRoleEditModal').style.display = 'none';
}

// Add Edit Permission Button Handler
if (document.getElementById('add-edit-permission-btn')) {
    document.getElementById('add-edit-permission-btn').onclick = function(e) {
        e.preventDefault();
        var container = document.getElementById('edit-permission-container');
        var div = document.createElement('div');
        div.className = 'permission-row';
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.gap = '8px';
        var select = document.createElement('select');
        select.name = "permissions[]";
        select.className = "faq-input permission-select";
        select.style.flex = '1';
        select.style.marginBottom = '8px';
        select.innerHTML = permissionOptionsHtml;
        
        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-permission-btn';
        removeBtn.textContent = 'Remove';
        removeBtn.style.background = '#932825';
        removeBtn.style.color = '#fff';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '29px';
        removeBtn.style.padding = '8px 16px';
        removeBtn.style.fontSize = '15px';
        removeBtn.style.fontWeight = '600';
        removeBtn.style.cursor = 'pointer';
        removeBtn.style.marginBottom = '8px';
        removeBtn.style.whiteSpace = 'nowrap';
        removeBtn.style.height = '44px';
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            div.remove();
            validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
        });
        
        div.appendChild(select);
        div.appendChild(removeBtn);
        container.appendChild(div);
        validateRoleForm('user-role-edit-form', 'edit-role-name', 'edit-permission-container', '#user-role-edit-form .btn-submit-faq');
    };
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
    fetch(baseUrl + 'admin/delete_user_role', {
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

function closeFaqModal() {
    document.getElementById('faqModal').style.display = 'none';
}

function closeFaqEditModal() {
    document.getElementById('faqEditModal').style.display = 'none';
}

function openFaqDeleteModal(id) {
    showGlobalWarning(
        'Apakah Anda yakin ingin menghapus FAQ ini? Data yang dihapus tidak dapat dikembalikan.',
        function() {
            confirmDeleteFaq(id);
        },
        function() {}
    );
}

function confirmDeleteFaq(id) {
    fetch(baseUrl + 'admin/delete_faq', {
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

function closeRequestTypeModal() {
    document.getElementById('requestTypeModal').style.display = 'none';
}

function closeRequestTypeEditModal() {
    document.getElementById('requestTypeEditModal').style.display = 'none';
}

function openRequestTypeEditModal(id, name, desc, status) {
    document.getElementById('edit-request-type-id').value = id;
    document.getElementById('edit-request-type-name').value = name;
    document.getElementById('edit-request-type-desc').value = desc;
    document.getElementById('edit-request-type-status').value = status;
    document.getElementById('requestTypeEditModal').style.display = 'flex';
}

function openRequestTypeDeleteModal(id) {
    showGlobalWarning(
        'Apakah Anda yakin ingin menghapus Request Type ini? Data yang dihapus tidak dapat dikembalikan.',
        function() {
            confirmDeleteRequestType(id);
        },
        function() {}
    );
}

function confirmDeleteRequestType(id) {
    fetch(baseUrl + 'admin/delete_request_type', {
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

function closeSlaModal() {
    document.getElementById('slaModal').style.display = 'none';
}

function closeSlaEditModal() {
    document.getElementById('slaEditModal').style.display = 'none';
}

function openSlaDeleteModal(id) {
    document.getElementById('delete-sla-id').value = id;
    document.getElementById('slaDeleteModal').style.display = 'flex';
}

function closeSlaDeleteModal() {
    document.getElementById('slaDeleteModal').style.display = 'none';
}

function confirmDeleteSla() {
    var id = document.getElementById('delete-sla-id').value;
    fetch(baseUrl + 'admin/delete_sla', {
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

function openSlaEditModal(id, priority, response, resolution) {
    document.getElementById('edit-sla-id').value = id;
    document.getElementById('edit-sla-priority').value = priority;
    document.getElementById('edit-sla-response').value = response;
    document.getElementById('edit-sla-resolution').value = resolution;
    document.getElementById('slaEditModal').style.display = 'flex';
}

// List Loader Functions
function loadFaqList(page = 1, perPage = 10) {
    fetch(baseUrl + 'admin/get_faq_list?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        document.getElementById('faq-list').innerHTML = html;
    });
}

function loadRoleList(page = 1, perPage = 10) {
    fetch(baseUrl + 'admin/get_user_role_list?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        document.getElementById('user-role-list').innerHTML = html;
    });
}

function loadRequestTypeList(page = 1, perPage = 10) {
    fetch(baseUrl + 'admin/get_request_type_list?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        document.getElementById('request-type-list').innerHTML = html;
    });
}

function loadSlaList(page = 1, perPage = 10) {
    fetch(baseUrl + 'admin/get_sla_list?page=' + page + '&per_page=' + perPage)
    .then(res => res.text())
    .then(html => {
        document.getElementById('sla-list').innerHTML = html;
    });
}

function closeUserRoleModal() {
    document.getElementById('userRoleModal').style.display = 'none';
}

// Tab Slider
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

// Permission Dropdowns
function updatePermissionDropdowns() {
    var selects = document.querySelectorAll('#permission-container .permission-select');
    selects.forEach(function(sel, idx) {
        var btn = sel.parentElement.querySelector('.remove-permission-btn');
        btn.style.display = (idx === 0) ? 'none' : 'inline-block';
    });
    var selected = [];
    selects.forEach(function(sel) {
        if (sel.value) selected.push(sel.value);
    });
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

// Keyboard shortcuts
document.addEventListener('keydown', function(e){
    if(e.key === "Escape") {
        closeFaqModal();
        closeFaqEditModal();
        closeSlaModal();
        closeSlaEditModal();
        closeSlaDeleteModal();
        closeUserRoleModal();
        closeUserRoleEditModal();
        closeUserRoleDeleteModal();
        closeRequestTypeModal();
        closeRequestTypeEditModal();
    }
});
