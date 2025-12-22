// // User Management JavaScript Functions
// // Loaded after DOM is ready

// console.log('user_mgt.js loaded');

// // Global variables from PHP
// if (typeof baseUrl === 'undefined') {
//     window.baseUrl = '/';
// }

// // Password visibility toggle functions
// function toggleAddUserPassword() {
//     const input = document.getElementById('add-user-password');
//     const eyeIcon = document.getElementById('add-eye-icon');
//     if (input && eyeIcon) {
//         if (input.type === 'password') {
//             input.type = 'text';
//             eyeIcon.classList.remove('fa-eye');
//             eyeIcon.classList.add('fa-eye-slash');
//         } else {
//             input.type = 'password';
//             eyeIcon.classList.remove('fa-eye-slash');
//             eyeIcon.classList.add('fa-eye');
//         }
//     }
// }

// function toggleEditUserPassword() {
//     const input = document.getElementById('edit-user-password');
//     const eyeIcon = document.getElementById('edit-eye-icon');
//     if (input && eyeIcon) {
//         if (input.type === 'password') {
//             input.type = 'text';
//             eyeIcon.classList.remove('fa-eye');
//             eyeIcon.classList.add('fa-eye-slash');
//         } else {
//             input.type = 'password';
//             eyeIcon.classList.remove('fa-eye-slash');
//             eyeIcon.classList.add('fa-eye');
//         }
//     }
// }

// // Tab switching functions
// function showTab(idx) {
//     console.log('showTab called with index:', idx);
//     let tabs = document.querySelectorAll('.settings-tab');
//     let contents = document.querySelectorAll('.tab-content');
//     tabs.forEach((tab, i) => {
//         tab.classList.toggle('active', i === idx);
//         contents[i].classList.toggle('active', i === idx);
//     });
//     moveSlider(idx);
// }

// function moveSlider(idx) {
//     console.log('moveSlider called with index:', idx);
//     let tabs = document.querySelectorAll('.settings-tab');
//     let slider = document.getElementById('settingsSlider');
//     if (tabs.length > idx && slider) {
//         let tab = tabs[idx];
//         slider.style.width = tab.offsetWidth + 'px';
//         slider.style.left = tab.offsetLeft + 'px';
//         console.log('Slider moved to:', tab.offsetLeft);
//     }
// }

// // Modal Functions - Add User
// function openAddUserModal() {
//     console.log('openAddUserModal called');
//     const modal = document.getElementById('addUserModal');
//     if (modal) {
//         modal.style.display = 'flex';
//         console.log('Add User Modal opened');
//     } else {
//         console.error('addUserModal not found');
//     }
// }

// function closeAddUserModal() {
//     const modal = document.getElementById('addUserModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// // Modal Functions - Edit User
// function openEditUserModal(id) {
//     console.log('openEditUserModal called with id:', id);
//     fetch(baseUrl + 'admin/get_user?id=' + encodeURIComponent(id))
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             const user = data.user;
//             const modal = document.getElementById('editUserModal');
//             if (modal) {
//                 document.getElementById('edit-user-id').value = user.id;
//                 document.getElementById('edit-user-username').value = user.username || user.name;
//                 document.getElementById('edit-user-name').value = user.name;
//                 document.getElementById('edit-user-email').value = user.email;
//                 document.getElementById('edit-user-role').value = user.role_id || '';
//                 document.getElementById('edit-user-status').value = user.status;
//                 document.getElementById('edit-user-password').value = '';
//                 modal.style.display = 'flex';
//                 console.log('Edit User Modal opened');
//             }
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }

// function closeEditUserModal() {
//     const modal = document.getElementById('editUserModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// // Delete User
// function deleteUser(id) {
//     console.log('deleteUser called with id:', id);
//     if (typeof showGlobalWarning === 'function') {
//         showGlobalWarning('Yakin ingin menghapus user ini?', function() {
//             fetch(baseUrl + 'admin/delete_user', {
//                 method: 'POST',
//                 headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//                 body: 'id=' + encodeURIComponent(id)
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     if (typeof showGlobalSuccess === 'function') {
//                         showGlobalSuccess('User berhasil dihapus!');
//                     }
//                     setTimeout(function() {
//                         location.reload();
//                     }, 2500);
//                 } else {
//                     const errorMsg = data.message || data.error || 'Gagal menghapus user!';
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid(errorMsg);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Terjadi kesalahan saat menghapus user!');
//                 }
//             });
//         });
//     }
// }

// // Modal Functions - Add Permission
// function openAddPermissionModal() {
//     console.log('openAddPermissionModal called');
//     const modal = document.getElementById('addPermissionModal');
//     if (modal) {
//         modal.style.display = 'flex';
//         console.log('Add Permission Modal opened');
//     } else {
//         console.error('addPermissionModal not found');
//     }
// }

// function closeAddPermissionModal() {
//     const modal = document.getElementById('addPermissionModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// // Modal Functions - Edit Permission
// function editPermission(id) {
//     console.log('editPermission called with id:', id);
//     if (!id || id === '' || isNaN(id)) {
//         console.error('Invalid permission ID:', id);
//         if (typeof showGlobalInvalid === 'function') {
//             showGlobalInvalid('ID permission tidak valid!');
//         }
//         return;
//     }
    
//     fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
//     .then(response => response.json())
//     .then(data => {
//         if (data.success && data.permission) {
//             document.getElementById('edit-permission-id').value = data.permission.id;
//             document.getElementById('edit-permission-name').value = data.permission.name;
//             const modal = document.getElementById('editPermissionModal');
//             if (modal) {
//                 modal.style.display = 'flex';
//                 console.log('Edit Permission Modal opened');
//             }
//         }
//     })
//     .catch(error => console.error('Fetch error:', error));
// }

// function closeEditPermissionModal() {
//     const modal = document.getElementById('editPermissionModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// // Modal Functions - Delete Permission
// function deletePermission(id) {
//     console.log('deletePermission called with id:', id);
//     if (!id || id === '' || isNaN(id)) {
//         if (typeof showGlobalInvalid === 'function') {
//             showGlobalInvalid('ID permission tidak valid!');
//         }
//         return;
//     }
    
//     fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
//     .then(response => response.json())
//     .then(data => {
//         if (data.success && data.permission) {
//             window.deletePermissionId = id;
//             document.getElementById('delete-permission-name').innerText = data.permission.name;
//             const modal = document.getElementById('deletePermissionModal');
//             if (modal) {
//                 modal.style.display = 'flex';
//                 console.log('Delete Permission Modal opened');
//             }
//         }
//     })
//     .catch(error => console.error('Fetch error:', error));
// }

// function closeDeletePermissionModal() {
//     const modal = document.getElementById('deletePermissionModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// function confirmDeletePermission() {
//     if (!window.deletePermissionId || isNaN(window.deletePermissionId)) {
//         if (typeof showGlobalInvalid === 'function') {
//             showGlobalInvalid('ID permission tidak valid!');
//         }
//         return;
//     }
    
//     fetch(baseUrl + 'admin/delete_permission', {
//         method: 'POST',
//         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//         body: 'id=' + encodeURIComponent(window.deletePermissionId)
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             closeDeletePermissionModal();
//             removePermissionRow(window.deletePermissionId);
//             if (typeof showGlobalSuccess === 'function') {
//                 showGlobalSuccess('Permission berhasil dihapus!');
//             }
//         } else {
//             const errorMsg = data.message || data.error || 'Gagal menghapus permission!';
//             if (typeof showGlobalInvalid === 'function') {
//                 showGlobalInvalid(errorMsg);
//             }
//         }
//     })
//     .catch(error => {
//         console.error('Fetch error:', error);
//         if (typeof showGlobalInvalid === 'function') {
//             showGlobalInvalid('Terjadi kesalahan saat menghapus permission!');
//         }
//     });
// }

// // Escape key handler
// document.addEventListener('keydown', function(event) {
//     if (event.key === "Escape") {
//         closeAddUserModal();
//         closeEditUserModal();
//         closeAddPermissionModal();
//         closeEditPermissionModal();
//         closeDeletePermissionModal();
//     }
// });

// // Outside click handlers
// document.addEventListener('click', function(event) {
//     const addUserModal = document.getElementById('addUserModal');
//     const editUserModal = document.getElementById('editUserModal');
//     const addPermModal = document.getElementById('addPermissionModal');
//     const editPermModal = document.getElementById('editPermissionModal');
//     const deletePermModal = document.getElementById('deletePermissionModal');
    
//     if (event.target === addUserModal) closeAddUserModal();
//     if (event.target === editUserModal) closeEditUserModal();
//     if (event.target === addPermModal) closeAddPermissionModal();
//     if (event.target === editPermModal) closeEditPermissionModal();
//     if (event.target === deletePermModal) closeDeletePermissionModal();
// });

// // Form Submissions
// document.addEventListener('DOMContentLoaded', function() {
//     // Add User Form
//     const addUserForm = document.getElementById('addUserForm');
//     if (addUserForm) {
//         addUserForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const pwd = form.password.value || '';
//             const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
            
//             if (!pwdPattern.test(pwd)) {
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
//                 }
//                 return;
//             }
            
//             const formData = new FormData(form);
//             fetch(baseUrl + 'admin/add_user', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     closeAddUserModal();
//                     addUserRow(data.user);
//                     form.reset();
//                     const message = 'User berhasil ditambahkan!\n\nGenerated UUID:\n' + data.user.generated_uuid;
//                     if (typeof showGlobalSuccess === 'function') {
//                         showGlobalSuccess(message);
//                     }
//                     setTimeout(function() {
//                         location.reload();
//                     }, 2500);
//                 } else {
//                     const errorMsg = data.message || data.error || 'Gagal menambahkan user!';
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid(errorMsg);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Terjadi kesalahan saat menambahkan user!');
//                 }
//             });
//         };
//     }

//     // Edit User Form
//     const editUserForm = document.getElementById('editUserForm');
//     if (editUserForm) {
//         editUserForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const formData = new FormData(form);
//             const pwd = form.password.value || '';
            
//             if (pwd) {
//                 const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
//                 if (!pwdPattern.test(pwd)) {
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
//                     }
//                     return;
//                 }
//             }
            
//             fetch(baseUrl + 'admin/edit_user', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     closeEditUserModal();
//                     if (typeof showGlobalSuccess === 'function') {
//                         showGlobalSuccess('User berhasil diupdate!');
//                     }
//                     location.reload();
//                 } else {
//                     const errorMsg = data.message || data.error || 'Gagal mengupdate user!';
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid(errorMsg);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Terjadi kesalahan saat mengupdate user!');
//                 }
//             });
//         };
//     }

//     // Add Permission Form
//     const addPermissionForm = document.getElementById('addPermissionForm');
//     if (addPermissionForm) {
//         addPermissionForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const name = form.name.value.trim();
            
//             if (!name) {
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Permission name tidak boleh kosong!');
//                 }
//                 return;
//             }

//             const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
//             const formData = new FormData();
//             formData.append('name', name);
//             formData.append('code', code);

//             fetch(baseUrl + 'admin/add_permission', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     closeAddPermissionModal();
//                     addPermissionRow(data.permission);
//                     form.reset();
//                     if (typeof showGlobalSuccess === 'function') {
//                         showGlobalSuccess('Permission berhasil ditambahkan!');
//                     }
//                     setTimeout(function() {
//                         location.reload();
//                     }, 2500);
//                 } else {
//                     const errorMsg = data.message || data.error || 'Gagal menambahkan permission!';
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid(errorMsg);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Terjadi kesalahan saat menambahkan permission!');
//                 }
//             });
//         };
//     }

//     // Edit Permission Form
//     const editPermissionForm = document.getElementById('editPermissionForm');
//     if (editPermissionForm) {
//         editPermissionForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const name = form.name.value.trim();
//             const id = document.getElementById('edit-permission-id').value;
            
//             if (!name) {
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Permission name tidak boleh kosong!');
//                 }
//                 return;
//             }

//             const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
//             const formData = new FormData();
//             formData.append('id', id);
//             formData.append('name', name);
//             formData.append('code', code);

//             fetch(baseUrl + 'admin/edit_permission', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success && data.permission) {
//                     closeEditPermissionModal();
//                     updatePermissionRow(data.permission);
//                     if (typeof showGlobalSuccess === 'function') {
//                         showGlobalSuccess('Permission berhasil diupdate!');
//                     }
//                     setTimeout(function() {
//                         location.reload();
//                     }, 2500);
//                 } else {
//                     const errorMsg = data.message || data.error || 'Gagal mengupdate permission!';
//                     if (typeof showGlobalInvalid === 'function') {
//                         showGlobalInvalid(errorMsg);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 if (typeof showGlobalInvalid === 'function') {
//                     showGlobalInvalid('Terjadi kesalahan saat mengupdate permission!');
//                 }
//             });
//         };
//     }

//     // Initialize slider on first tab
//     showTab(0);
// });

// // Helper Functions
// function escapeHtml(text) {
//     const map = {
//         '&': '&amp;',
//         '<': '&lt;',
//         '>': '&gt;',
//         '"': '&quot;',
//         "'": '&#039;'
//     };
//     return text.replace(/[&<>"']/g, m => map[m]);
// }

// function addPermissionRow(permission) {
//     const tbody = document.querySelector('.user-table-permissions tbody');
//     if (!tbody) return;
//     const tr = document.createElement('tr');
//     tr.setAttribute('data-permission-id', permission.id);
//     tr.innerHTML = `
//         <td class="permission-code">${escapeHtml(permission.code || '')}</td>
//         <td class="permission-name">${escapeHtml(permission.name || '')}</td>
//         <td class="permission-created-by">${escapeHtml(permission.created_by || '-')}</td>
//         <td class="permission-created-date">${escapeHtml(permission.created_date || '-')}</td>
//         <td>
//             <button class="user-edit-btn" onclick="editPermission(${permission.id})">Edit</button>
//             <button class="user-delete-btn" onclick="deletePermission(${permission.id})">Delete</button>
//         </td>
//     `;
//     tbody.prepend(tr);
// }

// function updatePermissionRow(permission) {
//     const row = document.querySelector(`tr[data-permission-id="${permission.id}"]`);
//     if (row) {
//         row.querySelector('.permission-code').innerText = permission.code;
//         row.querySelector('.permission-name').innerText = permission.name;
//         row.querySelector('.permission-created-by').innerText = permission.created_by ?? '-';
//         row.querySelector('.permission-created-date').innerText = permission.created_date ?? '-';
//     }
// }

// function removePermissionRow(id) {
//     const row = document.querySelector(`tr[data-permission-id="${id}"]`);
//     if (row) row.remove();
// }

// function addUserRow(user) {
//     const tbody = document.querySelector('.user-table tbody');
//     if (!tbody) return;
//     const tr = document.createElement('tr');
//     tr.setAttribute('data-user-id', user.id);
//     const roleText = (typeof window.roleMap !== 'undefined' && window.roleMap[user.role_id]) 
//         ? window.roleMap[user.role_id] 
//         : '-';
//     tr.innerHTML = `
//         <td class="user-username">${user.name}</td>
//         <td class="user-email">${user.email}</td>
//         <td class="user-role">${roleText}</td>
//         <td class="user-status">${user.status}</td>
//         <td class="user-last-login">${user.last_login_time ?? '-'}</td>
//         <td>
//             <button class="user-edit-btn" onclick="openEditUserModal(${user.id})">Edit</button>
//             <button class="user-delete-btn" onclick="deleteUser(${user.id})">Delete</button>
//         </td>
//     `;
//     tbody.prepend(tr);
// }
