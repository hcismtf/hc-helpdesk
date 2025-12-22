// // User Management JavaScript Functions

// // Ensure baseUrl is defined
// if (typeof baseUrl === 'undefined') {
//     var baseUrl = '/';
// }

// // Initialize after DOM is ready
// document.addEventListener('DOMContentLoaded', function() {
//     console.log('User Management JS loaded');
    
//     // Copy roleMap from global if available
//     if (typeof window.roleMap !== 'undefined') {
//         console.log('Using global roleMap:', window.roleMap);
//     }

//     // Password visibility toggle functions
// function toggleAddUserPassword() {
//     const input = document.getElementById('add-user-password');
//     const eyeIcon = document.getElementById('add-eye-icon');
//     if (input.type === 'password') {
//         input.type = 'text';
//         eyeIcon.classList.remove('fa-eye');
//         eyeIcon.classList.add('fa-eye-slash');
//     } else {
//         input.type = 'password';
//         eyeIcon.classList.remove('fa-eye-slash');
//         eyeIcon.classList.add('fa-eye');
//     }
// }

// function toggleEditUserPassword() {
//     const input = document.getElementById('edit-user-password');
//     const eyeIcon = document.getElementById('edit-eye-icon');
//     if (input.type === 'password') {
//         input.type = 'text';
//         eyeIcon.classList.remove('fa-eye');
//         eyeIcon.classList.add('fa-eye-slash');
//     } else {
//         input.type = 'password';
//         eyeIcon.classList.remove('fa-eye-slash');
//         eyeIcon.classList.add('fa-eye');
//     }
// }

// // Role map for displaying role names
// const roleMap = {
//     // This will be populated by PHP via inline script or global variable
// };

// // Permission Management Functions
// function editPermission(id) {
//     if (!id || id === '' || isNaN(id)) {
//         console.error('Invalid permission ID:', id);
//         showGlobalInvalid('ID permission tidak valid!');
//         return;
//     }
    
//     fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok: ' + response.status);
//         }
//         return response.json();
//     })
//     .then(data => {
//         if (data.success && data.permission) {
//             document.getElementById('edit-permission-id').value = data.permission.id;
//             document.getElementById('edit-permission-name').value = data.permission.name;
//             document.getElementById('editPermissionModal').style.display = 'flex';
//         } else {
//             console.error('API returned success=false:', data);
//             showGlobalInvalid('Gagal mengambil data permission!');
//         }
//     })
//     .catch(error => {
//         console.error('Fetch error:', error);
//         showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
//     });
// }

// function closeEditPermissionModal() {
//     document.getElementById('editPermissionModal').style.display = 'none';
// }

// // Submit edit permission
// document.addEventListener('DOMContentLoaded', function() {
//     const editPermissionForm = document.getElementById('editPermissionForm');
//     if (editPermissionForm) {
//         editPermissionForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const name = form.name.value.trim();
//             const id = document.getElementById('edit-permission-id').value;
            
//             // Validasi
//             if (!name) {
//                 showGlobalInvalid('Permission name tidak boleh kosong!');
//                 return;
//             }
//             if (!id || id === '' || isNaN(id)) {
//                 showGlobalInvalid('ID permission tidak valid!');
//                 return;
//             }

//             // Generate code baru dari name
//             const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

//             const formData = new FormData();
//             formData.append('id', id);
//             formData.append('name', name);
//             formData.append('code', code);

//             fetch(baseUrl + 'admin/edit_permission', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error('Network response was not ok: ' + response.status);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 if (data.success && data.permission) {
//                     closeEditPermissionModal();
//                     updatePermissionRow(data.permission);
//                     showSuccessConfirm('Permission berhasil diupdate!');
//                 } else {
//                     console.error('API returned success=false:', data);
//                     showGlobalInvalid('Gagal update permission!');
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 showGlobalInvalid('Gagal update permission: ' + error.message);
//             });
//         };
//     }
// });

// // Delete permission functions
// let deletePermissionId = null;

// function deletePermission(id) {
//     if (!id || id === '' || isNaN(id)) {
//         console.error('Invalid permission ID:', id);
//         showGlobalInvalid('ID permission tidak valid!');
//         return;
//     }
    
//     fetch(baseUrl + 'admin/get_permission?id=' + encodeURIComponent(id))
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok: ' + response.status);
//         }
//         return response.json();
//     })
//     .then(data => {
//         if (data.success && data.permission) {
//             deletePermissionId = id;
//             document.getElementById('delete-permission-name').innerText = data.permission.name;
//             document.getElementById('deletePermissionModal').style.display = 'flex';
//         } else {
//             console.error('API returned success=false:', data);
//             showGlobalInvalid('Gagal mengambil data permission!');
//         }
//     })
//     .catch(error => {
//         console.error('Fetch error:', error);
//         showGlobalInvalid('Gagal mengambil data permission: ' + error.message);
//     });
// }

// function closeDeletePermissionModal() {
//     document.getElementById('deletePermissionModal').style.display = 'none';
// }

// function confirmDeletePermission() {
//     if (!deletePermissionId || isNaN(deletePermissionId)) {
//         showGlobalInvalid('ID permission tidak valid!');
//         return;
//     }
    
//     fetch(baseUrl + 'admin/delete_permission', {
//         method: 'POST',
//         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//         body: 'id=' + encodeURIComponent(deletePermissionId)
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok: ' + response.status);
//         }
//         return response.json();
//     })
//     .then(data => {
//         if (data.success) {
//             closeDeletePermissionModal();
//             removePermissionRow(deletePermissionId);
//             showSuccessConfirm('Permission berhasil dihapus!');
//         } else {
//             console.error('API returned success=false:', data);
//             showGlobalInvalid('Gagal menghapus permission!');
//         }
//     })
//     .catch(error => {
//         console.error('Fetch error:', error);
//         showGlobalInvalid('Gagal menghapus permission: ' + error.message);
//     });
// }

// // Helper update & remove row
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

// // Add Permission Modal Functions
// function openAddPermissionModal() {
//     console.log('Opening Add Permission Modal');
//     const modal = document.getElementById('addPermissionModal');
//     if (modal) {
//         console.log('Modal found, setting display to flex');
//         modal.style.display = 'flex';
//     } else {
//         console.error('addPermissionModal element not found');
//     }
// }

// function closeAddPermissionModal() {
//     console.log('Closing Add Permission Modal');
//     const modal = document.getElementById('addPermissionModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// // Close modal with escape key and outside click
// window.addEventListener('keydown', function(event) {
//     const addPermModal = document.getElementById('addPermissionModal');
//     if (event.key === "Escape" && addPermModal.style.display === 'flex') {
//         closeAddPermissionModal();
//     }
// });

// // Submit Add Permission
// document.addEventListener('DOMContentLoaded', function() {
//     const addPermissionForm = document.getElementById('addPermissionForm');
//     if (addPermissionForm) {
//         addPermissionForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const name = form.name.value.trim();
            
//             if (!name) {
//                 showGlobalInvalid('Permission name tidak boleh kosong!');
//                 return;
//             }

//             // Convert ke lowercase, replace space with underscore, remove non-alphanumeric
//             const code = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

//             const formData = new FormData();
//             formData.append('name', name);
//             formData.append('code', code);

//             fetch(baseUrl + 'admin/add_permission', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error('Network response was not ok: ' + response.status);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 if (data.success && data.permission) {
//                     closeAddPermissionModal();
//                     addPermissionRow(data.permission);
//                     form.reset();
//                     showSuccessConfirm('Permission berhasil ditambahkan!');
//                 } else {
//                     console.error('API returned success=false:', data);
//                     showGlobalInvalid('Gagal menambah permission!');
//                 }
//             })
//             .catch(error => {
//                 console.error('Fetch error:', error);
//                 showGlobalInvalid('Gagal menambah permission: ' + error.message);
//             });
//         };
//     }
// });

// // Tambahkan row permission ke tabel
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

// // Escape HTML untuk prevent XSS
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

// // Tab switching functions
// function showTab(idx) {
//     let tabs = document.querySelectorAll('.settings-tab');
//     let contents = document.querySelectorAll('.tab-content');
//     tabs.forEach((tab, i) => {
//         tab.classList.toggle('active', i === idx);
//         contents[i].classList.toggle('active', i === idx);
//     });
//     moveSlider(idx);
// }

// function moveSlider(idx) {
//     let tabs = document.querySelectorAll('.settings-tab');
//     let slider = document.getElementById('settingsSlider');
//     let tab = tabs[idx];
//     // Ambil posisi tab relatif terhadap parent (settings-tabs)
//     slider.style.width = tab.offsetWidth + 'px';
//     slider.style.left = tab.offsetLeft + 'px';
// }

// window.addEventListener('load', function() { 
//     showTab(0); 
// });

// // Add User Modal Functions
// function openAddUserModal() {
//     console.log('Opening Add User Modal');
//     const modal = document.getElementById('addUserModal');
//     if (modal) {
//         console.log('Modal found, setting display to flex');
//         modal.style.display = 'flex';
//     } else {
//         console.error('addUserModal element not found');
//     }
// }

// function closeAddUserModal() {
//     console.log('Closing Add User Modal');
//     const modal = document.getElementById('addUserModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// window.addEventListener('keydown', function(event) {
//     var modal = document.getElementById('addUserModal');
//     if (event.key === "Escape" && modal.style.display === 'flex') {
//         closeAddUserModal();
//     }
// });

// // Delete User
// function deleteUser(id) {
//     showWarningConfirm('Yakin ingin menghapus user ini?', function() {
//         fetch(baseUrl + 'admin/delete_user', {
//             method: 'POST',
//             headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//             body: 'id=' + encodeURIComponent(id)
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 showSuccessConfirm('User berhasil dihapus!', function() {
//                     location.reload();
//                 });
//             } else {
//                 alert('Gagal menghapus user.');
//             }
//         });
//     });
// }

// // Helper untuk warning_confirm
// function showWarningConfirm(message, onOk) {
//     document.getElementById('global-warning-message').innerText = message;
//     document.getElementById('global-warning-confirm-bg').style.display = 'block';
//     document.getElementById('global-warning-confirm').style.display = 'block';

//     let okBtn = document.getElementById('global-warning-ok-btn');
//     let cancelBtn = document.getElementById('global-warning-cancel-btn');
//     okBtn.onclick = function() {
//         document.getElementById('global-warning-confirm-bg').style.display = 'none';
//         document.getElementById('global-warning-confirm').style.display = 'none';
//         if (onOk) onOk();
//     };
//     cancelBtn.onclick = function() {
//         document.getElementById('global-warning-confirm-bg').style.display = 'none';
//         document.getElementById('global-warning-confirm').style.display = 'none';
//     };
// }

// // Helper untuk global_success_confirm
// function showSuccessConfirm(message, onOk) {
//     document.getElementById('global-success-message').innerText = message;
//     document.getElementById('global-success-confirm-bg').style.display = 'block';
//     document.getElementById('global-success-confirm').style.display = 'block';

//     document.getElementById('global-success-confirm-bg').onclick =
//     document.getElementById('global-success-confirm').onclick = function() {
//         document.getElementById('global-success-confirm-bg').style.display = 'none';
//         document.getElementById('global-success-confirm').style.display = 'none';
//         if (onOk) onOk();
//     };
// }

// // Edit User Modal Functions
// function openEditUserModal(id) {
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
//             }
//         } else {
//             alert('Gagal mengambil data user!');
//         }
//     });
// }

// function closeEditUserModal() {
//     const modal = document.getElementById('editUserModal');
//     if (modal) {
//         modal.style.display = 'none';
//     }
// }

// window.addEventListener('keydown', function(event) {
//     var modal = document.getElementById('editUserModal');
//     if (event.key === "Escape" && modal.style.display === 'flex') {
//         closeEditUserModal();
//     }
// });

// // Edit User Form Submit
// document.addEventListener('DOMContentLoaded', function() {
//     const editUserForm = document.getElementById('editUserForm');
//     if (editUserForm) {
//         editUserForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const formData = new FormData(form);
//             // Client-side password validation: if password provided, enforce rules
//             const pwd = form.password.value || '';
//             if (pwd) {
//                 const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
//                 if (!pwdPattern.test(pwd)) {
//                     if (typeof showGlobalError === 'function') {
//                         showGlobalError('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
//                     } else {
//                         alert('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
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
//                     updateUserRow(
//                         formData.get('id'),
//                         formData.get('username'),
//                         formData.get('name'),
//                         formData.get('email'),
//                         formData.get('role'),
//                         formData.get('status')
//                     );
//                     closeEditUserModal();
//                     showSuccessConfirm('User berhasil diupdate!');
//                 } else {
//                     alert('Gagal update user!');
//                 }
//             });
//         };
//     }
// });

// function updateUserRow(id, username, name, email, roleId, status) {
//     const row = document.querySelector(`tr[data-user-id="${id}"]`);
//     if (row) {
//         row.querySelector('.user-username').innerText = username;
//         row.querySelector('.user-email').innerText = email;
//         row.querySelector('.user-role').innerText =
//             document.getElementById('edit-user-role').selectedOptions[0].text;
//         row.querySelector('.user-status').innerText = status;
//     }
// }

// // Add User Form Submit
// document.addEventListener('DOMContentLoaded', function() {
//     const addUserForm = document.getElementById('addUserForm');
//     if (addUserForm) {
//         addUserForm.onsubmit = function(e) {
//             e.preventDefault();
//             const form = e.target;
//             const pwd = form.password.value || '';
//             const pwdPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$/;
//             if (!pwdPattern.test(pwd)) {
//                 if (typeof showGlobalError === 'function') {
//                     showGlobalError('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
//                 } else {
//                     alert('Password harus minimal 16 karakter, mengandung setidaknya 1 huruf kapital dan 1 karakter spesial.');
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
//                     const message = 'User berhasil ditambahkan!\n\nGenerated UUID:\n' + data.user.generated_uuid + '\n\nUser dapat login dengan password yang telah diatur.';
//                     showSuccessConfirm(message);
//                 } else {
//                     alert('Gagal menambah user!');
//                 }
//             });
//         };
//     }
// });

// function addUserRow(user) {
//     const tbody = document.querySelector('.user-table tbody');
//     if (!tbody) return;
//     const tr = document.createElement('tr');
//     tr.setAttribute('data-user-id', user.id);
//     tr.innerHTML = `
//         <td class="user-username">${user.name}</td>
//         <td class="user-email">${user.email}</td>
//         <td class="user-role">${roleMap[user.role_id] ?? '-'}</td>
//         <td class="user-status">${user.status}</td>
//         <td class="user-last-login">${user.last_login_time ?? '-'}</td>
//         <td>
//             <button class="user-edit-btn" onclick="openEditUserModal(${user.id})">Edit</button>
//             <button class="user-delete-btn" onclick="deleteUser(${user.id})">Delete</button>
//         </td>
//     `;
//     tbody.prepend(tr);
// }
