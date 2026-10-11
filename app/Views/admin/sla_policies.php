<?php include(APPPATH . 'Views/components/common/_success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SLA Policies - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/system_settings.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php 
        $active = 'settings'; 
        $subActive = 'sla_policies';
        include('navbar.php'); 
    ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'System Settings',
            'breadcrumbActive' => 'SLA Policies',
            'pageTitle'        => 'SLA Policies',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- Dedicated SLA Policies Management Card -->
            <div id="tab-content-sla" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">SLA Response & Resolution Policies</h2>
                            <span class="headline-badge badge-engine">Global Engine</span>
                        </div>
                        <p class="headline-desc">Configure target response times, resolution deadlines, and automatic escalation pathways by priority tier.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="sla-search-input" class="settings-search-input" placeholder="Filter SLA rules..." onkeyup="filterSlaTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-sla">
                            <span style="font-size:16px;">+</span>
                            <span>New SLA Rule</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="sla-list">
                    <?= view('admin/sla_settings', [
                        'slas'           => $slasData['slas'],
                        'totalPages'     => $slasData['totalPages'],
                        'page'           => $page ?? 1,
                        'perPage'        => $perPage ?? 5,
                        'paginationHTML' => $paginationHTML ?? ''
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODALS (Add, Edit, Delete SLA)
         ========================================================================= -->

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
                    <?= view('components/common/_basic_select', [
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
                    <?= view('components/common/_basic_select', [
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

    <script>
        // Live SLA Filter by Text
        function filterSlaTable(query) {
            const q = query.toLowerCase();
            const rows = document.querySelectorAll('#sla-list tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // --- SLA Modal & Action Handlers ---
        document.getElementById('btn-add-sla').onclick = function() {
            document.getElementById('slaModal').style.display = 'flex';
        };
        function closeSlaModal() { 
            document.getElementById('slaModal').style.display = 'none'; 
        }

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
            fetch('<?= base_url('admin/delete_sla') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeSlaDeleteModal();
                    showGlobalSuccess('SLA Policy berhasil dihapus!');
                    loadSlaList();
                }
            });
        }

        function loadSlaList(page = 1, perPage = 5) {
            fetch('<?= base_url('admin/get_sla_list') ?>?page=' + page + '&per_page=' + perPage)
            .then(res => res.text())
            .then(html => { 
                document.getElementById('sla-list').innerHTML = html; 
            });
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
                } else {
                    showGlobalInvalid(data.message || 'Gagal menambahkan SLA!');
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
                } else {
                    showGlobalInvalid(data.message || 'Gagal mengupdate SLA!');
                }
            });
        };
    </script>
</body>
</html>
