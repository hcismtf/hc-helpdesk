<?php include(APPPATH . 'Views/components/common/_success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQ Management - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php 
        $active = 'settings'; 
        $subActive = 'faq_management';
        include('navbar.php'); 
    ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'System Settings',
            'breadcrumbActive' => 'FAQ Management',
            'pageTitle'        => 'FAQ Management',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- Dedicated FAQ Management Card -->
            <div id="tab-content-faq" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Pusat Bantuan & Artikel FAQ</h2>
                            <span class="headline-badge badge-knowledge">Knowledge Base</span>
                        </div>
                        <p class="headline-desc">Kelola artikel tanya-jawab mandiri untuk membantu penyelesaian kendala umum pegawai secara cepat.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="faq-search-input" class="settings-search-input" placeholder="Filter artikel FAQ..." onkeyup="filterFaqCards(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-faq">
                            <span style="font-size:16px;">+</span>
                            <span>Add New FAQ</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="faq-list">
                    <?= view('admin/faq_list', [
                        'faqs'           => $faqs ?? [],
                        'page'           => $page ?? 1,
                        'perPage'        => $perPage ?? 5,
                        'totalPages'     => $totalPages ?? 1,
                        'paginationHTML' => $paginationHTML ?? ''
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODALS (Add & Edit FAQ)
         ========================================================================= -->

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

    <script>
        // Live Filter FAQ Cards
        function filterFaqCards(query) {
            const q = query.toLowerCase();
            const cards = document.querySelectorAll('#faq-list .faq-modern-card');
            cards.forEach(c => {
                const text = c.textContent.toLowerCase();
                c.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // --- FAQ Handlers ---
        document.getElementById('btn-add-faq').onclick = function() {
            document.getElementById('faqModal').style.display = 'flex';
        };
        function closeFaqModal() { 
            document.getElementById('faqModal').style.display = 'none'; 
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

        function loadFaqList(page = 1, perPage = 5) {
            fetch('<?= base_url('admin/get_faq_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => { 
                document.getElementById('faq-list').innerHTML = html; 
            });
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
    </script>
</body>
</html>
