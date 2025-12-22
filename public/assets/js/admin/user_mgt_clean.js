// User Management JavaScript - Permissions and User Management
// Global variables
var baseUrl = window.baseUrl || '';
var roleMap = window.roleMap || {};
let deletePermissionId = null;

// ============================================
// PASSWORD TOGGLE FUNCTIONS
// ============================================
function toggleAddUserPassword() {
    const input = document.getElementById('add-user-password');
    const eyeIcon = document.getElementById('add-eye-icon');
    if (input && eyeIcon) {
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
}

function toggleEditUserPassword() {
    const input = document.getElementById('edit-user-password');
    const eyeIcon = document.getElementById('edit-eye-icon');
    if (input && eyeIcon) {
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
}

// ============================================
// TAB SLIDER FUNCTIONS
// ============================================
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
    if (tabs.length > idx && slider) {
        let tab = tabs[idx];
        slider.style.width = tab.offsetWidth + 'px';
        slider.style.left = tab.offsetLeft + 'px';
    }
}

// ============================================
// MODAL FUNCTIONS - ADD USER
// ============================================
function openAddUserModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) modal.style.display = 'none';
}

// ============================================
// MODAL FUNCTIONS - EDIT USER
// ============================================
function openEditUserModal(id) {
    fetch(baseUrl + 'admin/get_user?id=' + encodeURIComponent(id))
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
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid('Gagal mengambil data user!');
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Terjadi kesalahan saat mengambil data user!');
        }
    });
}

function closeEditUserModal() {
    const modal = document.getElementById('editUserModal');
    if (modal) modal.style.display = 'none';
}

// ============================================
// DELETE USER
// ============================================
function deleteUser(id) {
    if (typeof showGlobalWarning === 'function') {
        showGlobalWarning('Yakin ingin menghapus user ini?', function() {
            fetch(baseUrl + 'admin/delete_user', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('User berhasil dihapus!');
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 2500);
                } else {
                    const errorMsg = data.message || data.error || 'Gagal menghapus user!';
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan saat menghapus user!');
                }
            });
        });
    }
}

// ============================================
// ADD PERMISSION MODAL
// ============================================
function openAddPermissionModal() {
    const modal = document.getElementById('addPermissionModal');
    if (modal) modal.style.display = 'flex';
}

function closeAddPermissionModal() {
    const modal = document.getElementById('addPermissionModal');
    if (modal) modal.style.display = 'none';
}

// ============================================
// EDIT PERMISSION MODAL
// ============================================
function editPermission(id) {
    if (!id || id === '' || isNaN(id)) {
        console.error('Invalid permission ID:', id);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('ID permission tidak valid!');
        }
        return;
    }
    
    fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
    .then(response => response.json())
    .then(data => {
        if (data.success && data.permission) {
            document.getElementById('edit-permission-id').value = data.permission.id;
            document.getElementById('edit-permission-name').value = data.permission.name;
            const modal = document.getElementById('editPermissionModal');
            if (modal) modal.style.display = 'flex';
        } else {
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid('Gagal mengambil data permission!');
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
        }
    });
}

function closeEditPermissionModal() {
    const modal = document.getElementById('editPermissionModal');
    if (modal) modal.style.display = 'none';
}

// ============================================
// DELETE PERMISSION MODAL
// ============================================
function deletePermission(id) {
    if (!id || id === '' || isNaN(id)) {
        console.error('Invalid permission ID:', id);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('ID permission tidak valid!');
        }
        return;
    }
    
    fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
    .then(response => response.json())
    .then(data => {
        if (data.success && data.permission) {
            deletePermissionId = id;
            document.getElementById('delete-permission-name').innerText = data.permission.name;
            const modal = document.getElementById('deletePermissionModal');
            if (modal) modal.style.display = 'flex';
        } else {
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid('Gagal mengambil data permission!');
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
        }
    });
}

function closeDeletePermissionModal() {
    const modal = document.getElementById('deletePermissionModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeletePermission() {
    if (!deletePermissionId || isNaN(deletePermissionId)) {
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('ID permission tidak valid!');
        }
        return;
    }
    
    fetch(baseUrl + 'admin/delete_permission', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(deletePermissionId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeletePermissionModal();
            removePermissionRow(deletePermissionId);
            if (typeof showGlobalSuccess === 'function') {
                showGlobalSuccess('Permission berhasil dihapus!');
            }
            setTimeout(function() {
                reloadPermissionList();
            }, 1500);
        } else {
            const errorMsg = data.message || data.error || 'Gagal menghapus permission!';
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid(errorMsg);
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Gagal menghapus permission: ' + error.message);
        }
    });
}

// ============================================
// HELPER FUNCTIONS
// ============================================
// ============================================
// UTILITY FUNCTIONS
// ============================================
function reloadUserList() {
    fetch(baseUrl + 'admin/user_mgt')
    .then(response => response.text())
    .then(html => {
        // Extract the table from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTable = doc.querySelector('.user-table tbody');
        const currentTable = document.querySelector('.user-table tbody');
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
        }
    })
    .catch(error => {
        console.error('Error reloading user list:', error);
        location.reload();
    });
}

function reloadPermissionList() {
    fetch(baseUrl + 'admin/user_mgt')
    .then(response => response.text())
    .then(html => {
        // Extract the permissions table from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTable = doc.querySelector('.user-table-permissions tbody');
        const currentTable = document.querySelector('.user-table-permissions tbody');
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
        }
    })
    .catch(error => {
        console.error('Error reloading permission list:', error);
        location.reload();
    });
}

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

function removeUserRow(id) {
    const row = document.querySelector(`tr[data-user-id="${id}"]`);
    if (row) row.remove();
}

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

function updateUserRow(id, username, name, email, roleId, status) {
    const row = document.querySelector(`tr[data-user-id="${id}"]`);
    if (row) {
        row.querySelector('.user-username').innerText = name;
        row.querySelector('.user-email').innerText = email;
        const roleSelect = document.getElementById('edit-user-role');
        if (roleSelect) {
            row.querySelector('.user-role').innerText = roleMap[roleId] ?? '-';
        }
        row.querySelector('.user-status').innerText = status;
    }
}

function addUserRow(user) {
    const tbody = document.querySelector('.user-table tbody');
    if (!tbody) return;
    const tr = document.createElement('tr');
    tr.setAttribute('data-user-id', user.id);
    const roleText = (roleMap && roleMap[user.role_id]) ? roleMap[user.role_id] : '-';
    
    const editBtn = document.createElement('button');
    editBtn.className = 'user-edit-btn';
    editBtn.textContent = 'Edit';
    editBtn.onclick = function() { openEditUserModal(user.id); };
    
    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'user-delete-btn';
    deleteBtn.textContent = 'Delete';
    deleteBtn.onclick = function() { deleteUser(user.id); };
    
    const td = document.createElement('td');
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    
    tr.innerHTML = `
        <td class="user-username">${escapeHtml(user.name)}</td>
        <td class="user-email">${escapeHtml(user.email)}</td>
        <td class="user-role">${escapeHtml(roleText)}</td>
        <td class="user-status">${escapeHtml(user.status)}</td>
        <td class="user-last-login">${escapeHtml(user.last_login_time ?? '-')}</td>
    `;
    tr.appendChild(td);
    tbody.prepend(tr);
}

// ============================================
// EVENT LISTENERS - DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Add User Form Submission
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const pwd = form.password.value || '';
            const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
            
            if (!pwdPattern.test(pwd)) {
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                }
                return;
            }
            
            const formData = new FormData(form);
            fetch(baseUrl + 'admin/add_user', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddUserModal();
                    addUserRow(data.user);
                    form.reset();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('User berhasil ditambahkan!');
                    }
                    setTimeout(function() {
                        reloadUserList();
                    }, 1500);
                } else {
                    const errorMsg = data.message || data.error || 'Gagal menambahkan user!';
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan saat menambahkan user!');
                }
            });
        };
    }

    // Edit User Form Submission
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const pwd = form.password.value || '';
            
            if (pwd) {
                const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
                if (!pwdPattern.test(pwd)) {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
                    }
                    return;
                }
            }
            
            fetch(baseUrl + 'admin/edit_user', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditUserModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('User berhasil diupdate!');
                    }
                    setTimeout(function() {
                        reloadUserList();
                    }, 1500);
                } else {
                    const errorMsg = data.message || data.error || 'Gagal mengupdate user!';
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan saat mengupdate user!');
                }
            });
        };
    }

    // Add Permission Form Submission
    const addPermissionForm = document.getElementById('addPermissionForm');
    if (addPermissionForm) {
        addPermissionForm.onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const name = form.name.value.trim();
            
            if (!name) {
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Permission name tidak boleh kosong!');
                }
                return;
            }

            const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
            const formData = new FormData();
            formData.append('name', name);
            formData.append('code', code);

            fetch(baseUrl + 'admin/add_permission', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.permission) {
                    closeAddPermissionModal();
                    addPermissionRow(data.permission);
                    form.reset();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Permission berhasil ditambahkan!');
                    }
                    setTimeout(function() {
                        reloadPermissionList();
                    }, 1500);
                } else {
                    const errorMsg = data.message || data.error || 'Gagal menambahkan permission!';
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan saat menambahkan permission!');
                }
            });
        };
    }

    // Edit Permission Form Submission
    const editPermissionForm = document.getElementById('editPermissionForm');
    if (editPermissionForm) {
        editPermissionForm.onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const name = form.name.value.trim();
            const id = document.getElementById('edit-permission-id').value;
            
            if (!name) {
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Permission name tidak boleh kosong!');
                }
                return;
            }

            const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', name);
            formData.append('code', code);

            fetch(baseUrl + 'admin/edit_permission', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.permission) {
                    closeEditPermissionModal();
                    updatePermissionRow(data.permission);
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Permission berhasil diupdate!');
                    }
                    setTimeout(function() {
                        reloadPermissionList();
                    }, 1500);
                } else {
                    const errorMsg = data.message || data.error || 'Gagal mengupdate permission!';
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Terjadi kesalahan saat mengupdate permission!');
                }
            });
        };
    }

    // Modal Close Handlers - Escape Key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeAddUserModal();
            closeEditUserModal();
            closeAddPermissionModal();
            closeEditPermissionModal();
            closeDeletePermissionModal();
        }
    });

    // Modal Close Handlers - Outside Click
    document.addEventListener('click', function(event) {
        const addUserModal = document.getElementById('addUserModal');
        const editUserModal = document.getElementById('editUserModal');
        const addPermModal = document.getElementById('addPermissionModal');
        const editPermModal = document.getElementById('editPermissionModal');
        const deletePermModal = document.getElementById('deletePermissionModal');
        
        if (event.target === addUserModal) closeAddUserModal();
        if (event.target === editUserModal) closeEditUserModal();
        if (event.target === addPermModal) closeAddPermissionModal();
        if (event.target === editPermModal) closeEditPermissionModal();
        if (event.target === deletePermModal) closeDeletePermissionModal();
    });

    // Initialize slider on first tab
    showTab(0);
});
