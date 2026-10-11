/**
 * Admin User Management Module
 * Handles tab transitions, candidate user search & autocomplete, edit modal, password reveal, and form validations.
 */

// Tab switching logic (Assigned Role Users vs All Users Directory)
function switchUserTab(tabKey) {
    const btnRole = document.getElementById('btn-tab-users-role');
    const btnAll  = document.getElementById('btn-tab-all-users');
    const cardRole = document.getElementById('tab-content-users-role');
    const cardAll  = document.getElementById('tab-content-all-users');

    if (tabKey === 'all_users') {
        if (btnRole) btnRole.classList.remove('active');
        if (btnAll) btnAll.classList.add('active');
        if (cardRole) cardRole.style.display = 'none';
        if (cardAll) cardAll.style.display = 'block';
        const url = new URL(window.location);
        url.searchParams.set('tab', 'all_users');
        window.history.replaceState({}, '', url);
    } else {
        if (btnAll) btnAll.classList.remove('active');
        if (btnRole) btnRole.classList.add('active');
        if (cardAll) cardAll.style.display = 'none';
        if (cardRole) cardRole.style.display = 'block';
        const url = new URL(window.location);
        url.searchParams.delete('tab');
        window.history.replaceState({}, '', url);
    }
}

// Live filter for users with role
function filterUserTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#user-mgt-list-wrap tbody tr');
    rows.forEach(function(r) {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// Live filter for all users directory
function filterAllUserTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#all-user-directory-wrap tbody tr');
    rows.forEach(function(r) {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// Modal open & close
function openAddUserModal() {
    clearSelectedCandidate();
    var modal = document.getElementById('addUserModal');
    if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
    var modal = document.getElementById('addUserModal');
    if (modal) modal.style.display = 'none';
    closeCandidateDropdown();
}

function closeCandidateDropdown() {
    var menu = document.getElementById('candidate-results-menu');
    if (menu) menu.style.display = 'none';
}

// Search Employee Candidate (NIP - Nama) via AJAX
let searchDebounceTimer = null;
function searchCandidates(keyword) {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function() {
        var url = (window.USER_MGT_CONFIG && window.USER_MGT_CONFIG.searchCandidateUrl) 
            ? window.USER_MGT_CONFIG.searchCandidateUrl 
            : '/admin/search_candidate_users';

        fetch(url + '?q=' + encodeURIComponent(keyword), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.status === 'success') {
                renderCandidateList(data.data || []);
            }
        })
        .catch(function() {});
    }, 180);
}

function renderCandidateList(candidates) {
    var menu = document.getElementById('candidate-results-menu');
    var container = document.getElementById('candidate-items-container');
    if (!menu || !container) return;

    container.innerHTML = '';
    if (candidates.length === 0) {
        container.innerHTML = '<div style="padding:10px 14px; font-size:12.5px; color:#94a3b8; text-align:center;">Tidak ada karyawan ditemukan</div>';
        menu.style.display = 'block';
        return;
    }

    candidates.forEach(function(c) {
        var item = document.createElement('div');
        item.className = 'candidate-item';
        item.innerHTML = 
            '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                '<div>' +
                    '<span style="font-weight:700; color:#0f172a; font-size:13px;">' + (c.name || '-') + '</span>' +
                    '<span style="font-size:11.5px; font-weight:700; color:#4f46e5; background:#eef2ff; padding:1px 6px; border-radius:4px; margin-left:6px;">NIP: ' + (c.employee_no || '-') + '</span>' +
                    '<div style="font-size:11.5px; color:#64748b; margin-top:2px;">' + (c.position_desc || '-') + ' &bull; ' + (c.email || '-') + '</div>' +
                '</div>' +
                '<span style="font-size:11px; color:#2563eb; font-weight:600;">Pilih &rarr;</span>' +
            '</div>';

        item.onclick = function() {
            selectCandidate(c);
        };
        container.appendChild(item);
    });

    menu.style.display = 'block';
}

function selectCandidate(c) {
    var userIdInput = document.getElementById('add-selected-user-id');
    var nameInput = document.getElementById('add-selected-name');
    var nipInput = document.getElementById('add-selected-nip');
    var emailInput = document.getElementById('add-selected-email');
    var searchInput = document.getElementById('candidate-search-input');
    var clearBtn = document.getElementById('candidate-clear-btn');
    var previewCard = document.getElementById('selected-employee-preview');

    if (userIdInput) userIdInput.value = c.id;
    if (nameInput) nameInput.value = c.name;
    if (nipInput) nipInput.value = c.employee_no;
    if (emailInput) emailInput.value = c.email;

    if (searchInput) {
        searchInput.value = (c.employee_no ? c.employee_no + ' - ' : '') + c.name;
        searchInput.readOnly = true;
        searchInput.style.background = '#f1f5f9';
    }
    if (clearBtn) clearBtn.style.display = 'block';

    var prevName = document.getElementById('preview-name');
    var prevNip = document.getElementById('preview-nip');
    var prevEmail = document.getElementById('preview-email');
    var prevPos = document.getElementById('preview-position');

    if (prevName) prevName.textContent = c.name;
    if (prevNip) prevNip.textContent = 'NIP: ' + (c.employee_no || '-');
    if (prevEmail) prevEmail.textContent = c.email || '-';
    if (prevPos) prevPos.textContent = (c.position_desc || '-') + (c.position_level ? ' (' + c.position_level + ')' : '');

    if (previewCard) previewCard.style.display = 'block';
    closeCandidateDropdown();
}

function clearSelectedCandidate() {
    var userIdInput = document.getElementById('add-selected-user-id');
    var nameInput = document.getElementById('add-selected-name');
    var nipInput = document.getElementById('add-selected-nip');
    var emailInput = document.getElementById('add-selected-email');
    var searchInput = document.getElementById('candidate-search-input');
    var clearBtn = document.getElementById('candidate-clear-btn');
    var previewCard = document.getElementById('selected-employee-preview');

    if (userIdInput) userIdInput.value = '';
    if (nameInput) nameInput.value = '';
    if (nipInput) nipInput.value = '';
    if (emailInput) emailInput.value = '';

    if (searchInput) {
        searchInput.value = '';
        searchInput.readOnly = false;
        searchInput.style.background = '#ffffff';
    }
    if (clearBtn) clearBtn.style.display = 'none';
    if (previewCard) previewCard.style.display = 'none';
    closeCandidateDropdown();
}

function openEditUserModal(id, name, email, role, status) {
    var idInput = document.getElementById('edit-user-id');
    var nameInput = document.getElementById('edit-user-name');
    var emailInput = document.getElementById('edit-user-email');
    var passInput = document.getElementById('edit-user-password');
    var roleInput = document.getElementById('edit-user-role');
    var statusInput = document.getElementById('edit-user-status');

    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;
    if (emailInput) emailInput.value = email;
    if (passInput) passInput.value = '';
    if (roleInput) roleInput.value = role;
    if (statusInput) statusInput.value = status;

    var modal = document.getElementById('editUserModal');
    if (modal) modal.style.display = 'flex';
}

function closeEditUserModal() {
    var modal = document.getElementById('editUserModal');
    if (modal) modal.style.display = 'none';
}

function toggleEditUserPassword() {
    var passInput = document.getElementById('edit-user-password');
    var eyeIcon = document.getElementById('edit-eye-icon');
    if (passInput) {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            if (eyeIcon) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        } else {
            passInput.type = 'password';
            if (eyeIcon) {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    }
}

// DOM Initialization & Form Handlers
document.addEventListener('DOMContentLoaded', function() {
    // Check URL tab param on load
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'all_users') {
        switchUserTab('all_users');
    }

    // Close candidate dropdown when clicking outside
    document.addEventListener('click', function(e) {
        var wrap = document.querySelector('.searchable-user-dropdown-wrap');
        if (wrap && !wrap.contains(e.target)) {
            closeCandidateDropdown();
        }
    });

    // Add User Form Validation
    var addForm = document.getElementById('addUserForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            var selectedUserId = document.getElementById('add-selected-user-id');
            var roleSelect = document.getElementById('add-user-role-select');

            if (!selectedUserId || !selectedUserId.value) {
                e.preventDefault();
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Pilih akun karyawan melalui pencarian NIP / Nama terlebih dahulu!');
                }
                return false;
            }

            if (!roleSelect || !roleSelect.value) {
                e.preventDefault();
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Pilih Role untuk karyawan ini!');
                }
                return false;
            }
        });
    }

    // Edit User Form Validation
    var editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            var nameInput = document.getElementById('edit-user-name');
            var emailInput = document.getElementById('edit-user-email');
            var roleSelect = document.getElementById('edit-user-role');
            var passInput = document.getElementById('edit-user-password');

            if (!nameInput.value.trim() || !emailInput.value.trim() || !roleSelect.value) {
                e.preventDefault();
                if (typeof showGlobalInvalid === 'function') {
                    showGlobalInvalid('Semua kolom bertanda * wajib diisi!');
                }
                return false;
            }

            if (passInput && passInput.value) {
                var val = passInput.value;
                var upper = /[A-Z]/.test(val);
                var special = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>\/?`~]/.test(val);
                if (val.length < 16 || !upper || !special) {
                    e.preventDefault();
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid('Password minimal 16 karakter, harus mengandung setidaknya 1 huruf kapital dan 1 karakter spesial!');
                    }
                    return false;
                }
            }
        });
    }
});
