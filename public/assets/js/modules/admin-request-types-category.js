/**
 * Admin Request Types & Categories Management Module
 * Handles tab transitions (Sub Category vs Main Category), live filter, modals, and AJAX CRUD.
 */

// Tab switching logic
function switchCategoryTab(tabKey) {
    const btnSub = document.getElementById('btn-tab-sub-category');
    const btnMain = document.getElementById('btn-tab-main-category');
    const cardSub = document.getElementById('tab-content-sub_category');
    const cardMain = document.getElementById('tab-content-main_category');

    if (tabKey === 'main_category') {
        if (btnSub) btnSub.classList.remove('active');
        if (btnMain) btnMain.classList.add('active');
        if (cardSub) cardSub.style.display = 'none';
        if (cardMain) cardMain.style.display = 'block';
        const url = new URL(window.location);
        url.searchParams.set('tab', 'main_category');
        window.history.replaceState({}, '', url);
    } else {
        if (btnMain) btnMain.classList.remove('active');
        if (btnSub) btnSub.classList.add('active');
        if (cardMain) cardMain.style.display = 'none';
        if (cardSub) cardSub.style.display = 'block';
        const url = new URL(window.location);
        url.searchParams.delete('tab');
        window.history.replaceState({}, '', url);
    }
}

// Live text filter for Request Type (Sub Category)
function filterRequestTypeTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#request-type-list tbody tr');
    rows.forEach(function(r) {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// Live text filter for Main Category
function filterCategoryTable(query) {
    const q = (query || '').toLowerCase();
    const rows = document.querySelectorAll('#main-category-list tbody tr');
    rows.forEach(function(r) {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// =========================================================================
// SUB CATEGORY (REQUEST TYPE) HANDLERS
// =========================================================================
function openRequestTypeModal() {
    var modal = document.getElementById('requestTypeModal');
    if (modal) modal.style.display = 'flex';
}

function closeRequestTypeModal() {
    var modal = document.getElementById('requestTypeModal');
    if (modal) modal.style.display = 'none';
}

function closeRequestTypeEditModal() {
    var modal = document.getElementById('requestTypeEditModal');
    if (modal) modal.style.display = 'none';
}

function openRequestTypeDeleteModal(id) {
    var idInput = document.getElementById('delete-request-type-id');
    if (idInput) idInput.value = id;
    var modal = document.getElementById('requestTypeDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeRequestTypeDeleteModal() {
    var modal = document.getElementById('requestTypeDeleteModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteRequestType() {
    var idInput = document.getElementById('delete-request-type-id');
    if (!idInput) return;
    var id = idInput.value;
    var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.deleteTypeUrl : '/admin/delete_request_type';

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
            closeRequestTypeDeleteModal();
            if (typeof showGlobalSuccess === 'function') {
                showGlobalSuccess('Request Type berhasil dihapus!');
            }
            loadRequestTypeList();
        } else {
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid((data && data.message) || 'Gagal menghapus Request Type.');
            }
        }
    })
    .catch(function() {
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Terjadi kesalahan jaringan.');
        }
    });
}

function loadRequestTypeList(page, perPage) {
    page = page || 1;
    perPage = perPage || 5;
    var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.listTypeUrl : '/admin/get_request_type_list';

    fetch(url + '?page=' + page + '&per_page=' + perPage, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        var listContainer = document.getElementById('request-type-list');
        if (listContainer) listContainer.innerHTML = html;
    });
}

function openRequestTypeEditModal(id, name, catId, slaId, desc, badge, isPopular, status) {
    var idInput = document.getElementById('edit-request-type-id');
    var nameInput = document.getElementById('edit-request-type-name');
    var catSelect = document.getElementById('edit-request-type-category');
    var slaSelect = document.getElementById('edit-request-type-sla');
    var descInput = document.getElementById('edit-request-type-desc');
    var badgeInput = document.getElementById('edit-request-type-badge');
    var popularCheckbox = document.getElementById('edit-request-type-popular');
    var statusSelect = document.getElementById('edit-request-type-status');

    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;
    if (catSelect) catSelect.value = catId || '';
    if (slaSelect) slaSelect.value = slaId || '';
    if (descInput) descInput.value = desc || '';
    if (badgeInput) badgeInput.value = badge || '';
    if (popularCheckbox) popularCheckbox.checked = (parseInt(isPopular, 10) === 1);
    if (statusSelect) statusSelect.value = status || 'Active';

    var modal = document.getElementById('requestTypeEditModal');
    if (modal) modal.style.display = 'flex';
}

// =========================================================================
// MAIN CATEGORY HANDLERS
// =========================================================================
function openCategoryModal() {
    var modal = document.getElementById('categoryModal');
    if (modal) modal.style.display = 'flex';
}

function closeCategoryModal() {
    var modal = document.getElementById('categoryModal');
    if (modal) modal.style.display = 'none';
}

function closeCategoryEditModal() {
    var modal = document.getElementById('categoryEditModal');
    if (modal) modal.style.display = 'none';
}

function openCategoryDeleteModal(id, name, subCount) {
    var idInput = document.getElementById('delete-category-id');
    var promptEl = document.getElementById('delete-category-prompt');
    if (idInput) idInput.value = id;

    if (promptEl) {
        if (subCount > 0) {
            promptEl.innerHTML = 'Kategori <strong>' + name + '</strong> masih memiliki <strong>' + subCount + ' Sub Kategori</strong> terkait.<br><span style="color:#dc2626; font-size:12px;">Hapus atau pindahkan sub kategori terlebih dahulu!</span>';
        } else {
            promptEl.innerHTML = 'Apakah Anda yakin ingin menghapus Kategori Utama <strong>' + name + '</strong>?';
        }
    }

    var modal = document.getElementById('categoryDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeCategoryDeleteModal() {
    var modal = document.getElementById('categoryDeleteModal');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteCategory() {
    var idInput = document.getElementById('delete-category-id');
    if (!idInput) return;
    var id = idInput.value;
    var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.deleteCatUrl : '/admin/delete_category';

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
            closeCategoryDeleteModal();
            if (typeof showGlobalSuccess === 'function') {
                showGlobalSuccess('Main Category berhasil dihapus!');
            }
            loadCategoryList();
        } else {
            if (typeof showGlobalInvalid === 'function') {
                showGlobalInvalid((data && data.message) || 'Gagal menghapus kategori.');
            }
        }
    })
    .catch(function() {
        if (typeof showGlobalInvalid === 'function') {
            showGlobalInvalid('Terjadi kesalahan jaringan.');
        }
    });
}

function loadCategoryList(page, perPage) {
    page = page || 1;
    perPage = perPage || 5;
    var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.listCatUrl : '/admin/get_category_list';

    fetch(url + '?page=' + page + '&per_page=' + perPage, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        var listContainer = document.getElementById('main-category-list');
        if (listContainer) listContainer.innerHTML = html;
    });
}

function openCategoryEditModal(id, name, code, icon, color, sort, desc, isActive) {
    var idInput = document.getElementById('edit-category-id');
    var nameInput = document.getElementById('edit-category-name');
    var codeInput = document.getElementById('edit-category-code');
    var iconInput = document.getElementById('edit-category-icon');
    var colorInput = document.getElementById('edit-category-color');
    var colorHex = document.getElementById('edit-category-color-hex');
    var sortInput = document.getElementById('edit-category-sort');
    var descInput = document.getElementById('edit-category-desc');
    var statusSelect = document.getElementById('edit-category-status');

    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;
    if (codeInput) codeInput.value = code || '';
    if (iconInput) iconInput.value = icon || 'fa-solid fa-folder';
    if (colorInput) colorInput.value = color || '#2563EB';
    if (colorHex) colorHex.value = color || '#2563EB';
    if (sortInput) sortInput.value = sort || 0;
    if (descInput) descInput.value = desc || '';
    if (statusSelect) statusSelect.value = String(isActive);

    var modal = document.getElementById('categoryEditModal');
    if (modal) modal.style.display = 'flex';
}

// DOM Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Check URL query tab parameter on page load
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'main_category') {
        switchCategoryTab('main_category');
    }

    // Modal open buttons
    var btnAddType = document.getElementById('btn-add-request-type');
    if (btnAddType) btnAddType.onclick = openRequestTypeModal;

    var btnAddCat = document.getElementById('btn-add-category');
    if (btnAddCat) btnAddCat.onclick = openCategoryModal;

    // Add Request Type form submit
    var reqTypeForm = document.getElementById('request-type-form');
    if (reqTypeForm) {
        reqTypeForm.onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('request-type-name').value.trim();
            var categoryId = document.getElementById('request-type-category').value;
            var slaId = document.getElementById('request-type-sla').value;
            var desc = document.getElementById('request-type-desc').value.trim();
            var badge = document.getElementById('request-type-badge').value.trim();
            var status = document.getElementById('request-type-status').value;
            var isPopular = document.getElementById('request-type-popular').checked ? 1 : 0;

            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Nama Request Type wajib diisi!');
                return;
            }
            if (!categoryId) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Kategori Utama (Topik HC) wajib dipilih!');
                return;
            }

            var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.addTypeUrl : '/admin/add_request_type';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'name=' + encodeURIComponent(name)
                    + '&category_id=' + encodeURIComponent(categoryId)
                    + '&sla_id=' + encodeURIComponent(slaId)
                    + '&description=' + encodeURIComponent(desc)
                    + '&badge_tag=' + encodeURIComponent(badge)
                    + '&status=' + encodeURIComponent(status)
                    + '&is_popular=' + encodeURIComponent(isPopular)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    document.getElementById('request-type-name').value = '';
                    document.getElementById('request-type-desc').value = '';
                    document.getElementById('request-type-badge').value = '';
                    closeRequestTypeModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Request Type berhasil ditambahkan!');
                    }
                    loadRequestTypeList();
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal menambahkan data.');
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

    // Edit Request Type form submit
    var reqTypeEditForm = document.getElementById('request-type-edit-form');
    if (reqTypeEditForm) {
        reqTypeEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-request-type-id').value;
            var name = document.getElementById('edit-request-type-name').value.trim();
            var categoryId = document.getElementById('edit-request-type-category').value;
            var slaId = document.getElementById('edit-request-type-sla').value;
            var desc = document.getElementById('edit-request-type-desc').value.trim();
            var badge = document.getElementById('edit-request-type-badge').value.trim();
            var status = document.getElementById('edit-request-type-status').value;
            var isPopular = document.getElementById('edit-request-type-popular').checked ? 1 : 0;

            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Nama Request Type wajib diisi!');
                return;
            }
            if (!categoryId) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Kategori Utama (Topik HC) wajib dipilih!');
                return;
            }

            var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.editTypeUrl : '/admin/edit_request_type';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(id)
                    + '&name=' + encodeURIComponent(name)
                    + '&category_id=' + encodeURIComponent(categoryId)
                    + '&sla_id=' + encodeURIComponent(slaId)
                    + '&description=' + encodeURIComponent(desc)
                    + '&badge_tag=' + encodeURIComponent(badge)
                    + '&status=' + encodeURIComponent(status)
                    + '&is_popular=' + encodeURIComponent(isPopular)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    closeRequestTypeEditModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Perubahan Request Type berhasil disimpan!');
                    }
                    loadRequestTypeList();
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal menyimpan perubahan.');
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

    // Add Category form submit
    var catForm = document.getElementById('category-form');
    if (catForm) {
        catForm.onsubmit = function(e) {
            e.preventDefault();
            var name = document.getElementById('category-name').value.trim();
            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Nama Kategori wajib diisi!');
                return;
            }

            var code = document.getElementById('category-code').value.trim();
            var icon = document.getElementById('category-icon').value.trim();
            var color = document.getElementById('category-color').value;
            var sort = document.getElementById('category-sort').value;
            var desc = document.getElementById('category-desc').value.trim();
            var status = document.getElementById('category-status').value;

            var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.addCatUrl : '/admin/add_category';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'name=' + encodeURIComponent(name)
                    + '&code=' + encodeURIComponent(code)
                    + '&icon=' + encodeURIComponent(icon)
                    + '&color=' + encodeURIComponent(color)
                    + '&sort_order=' + encodeURIComponent(sort)
                    + '&description=' + encodeURIComponent(desc)
                    + '&is_active=' + encodeURIComponent(status)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    document.getElementById('category-name').value = '';
                    document.getElementById('category-code').value = '';
                    document.getElementById('category-desc').value = '';
                    closeCategoryModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Main Category berhasil ditambahkan!');
                    }
                    loadCategoryList();
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal menambahkan kategori.');
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

    // Edit Category form submit
    var catEditForm = document.getElementById('category-edit-form');
    if (catEditForm) {
        catEditForm.onsubmit = function(e) {
            e.preventDefault();
            var id = document.getElementById('edit-category-id').value;
            var name = document.getElementById('edit-category-name').value.trim();
            if (!name) {
                if (typeof showGlobalInvalid === 'function') showGlobalInvalid('Nama Kategori wajib diisi!');
                return;
            }

            var code = document.getElementById('edit-category-code').value.trim();
            var icon = document.getElementById('edit-category-icon').value.trim();
            var color = document.getElementById('edit-category-color').value;
            var sort = document.getElementById('edit-category-sort').value;
            var desc = document.getElementById('edit-category-desc').value.trim();
            var status = document.getElementById('edit-category-status').value;

            var url = window.REQ_CATEGORY_ENDPOINTS ? window.REQ_CATEGORY_ENDPOINTS.editCatUrl : '/admin/edit_category';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(id)
                    + '&name=' + encodeURIComponent(name)
                    + '&code=' + encodeURIComponent(code)
                    + '&icon=' + encodeURIComponent(icon)
                    + '&color=' + encodeURIComponent(color)
                    + '&sort_order=' + encodeURIComponent(sort)
                    + '&description=' + encodeURIComponent(desc)
                    + '&is_active=' + encodeURIComponent(status)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    closeCategoryEditModal();
                    if (typeof showGlobalSuccess === 'function') {
                        showGlobalSuccess('Perubahan Main Category berhasil disimpan!');
                    }
                    loadCategoryList();
                } else {
                    if (typeof showGlobalInvalid === 'function') {
                        showGlobalInvalid((data && data.message) || 'Gagal menyimpan perubahan.');
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
