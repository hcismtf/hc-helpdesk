<?php
/**
 * Modern Create Ticket Modal Component for Admin
 */
try {
    $db = \Config\Database::connect();
    $requestTypes = $db->table('request_type')
        ->where('status', 'Active')
        ->orderBy('name', 'ASC')
        ->get()
        ->getResultArray();
} catch (\Throwable $e) {
    $requestTypes = [];
}
?>
<!-- Create Ticket Pop-up Modal -->
<div id="createTicketModal" class="create-ticket-modal-overlay" style="display:none;">
    <div class="create-ticket-modal-dialog">
        <!-- Modal Header -->
        <div class="create-ticket-modal-header">
            <div class="modal-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
            </div>
            <div class="modal-header-text">
                <h3>Create Support Ticket</h3>
                <p>Submit a new incident or service request into the helpdesk system</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeCreateTicketModal()" title="Close">&times;</button>
        </div>

        <!-- Alert Notification Box -->
        <div id="createTicketAlert" class="modal-alert-box" style="display:none;"></div>

        <!-- Form Body -->
        <form id="createTicketForm" enctype="multipart/form-data" onsubmit="handleCreateTicketSubmit(event)">
            <input type="hidden" name="is_ajax" value="1">

            <div class="modal-form-grid">
                <!-- Requester Name -->
                <div class="modal-form-group">
                    <label for="create_emp_name">Requester Name <span class="required-star">*</span></label>
                    <input type="text" name="emp_name" id="create_emp_name" class="modal-form-input" placeholder="e.g. John Doe" required>
                </div>

                <!-- NIP -->
                <div class="modal-form-group">
                    <label for="create_emp_id">NIP (Nomor Induk Pegawai) <span class="required-star">*</span></label>
                    <input type="text" name="emp_id" id="create_emp_id" class="modal-form-input" placeholder="e.g. 12345678" inputmode="numeric" required>
                </div>

                <!-- Email -->
                <div class="modal-form-group">
                    <label for="create_email">MTF Email <span class="required-star">*</span></label>
                    <input type="email" name="email" id="create_email" class="modal-form-input" placeholder="e.g. name@mtf.co.id" required>
                </div>

                <!-- WhatsApp / Phone -->
                <div class="modal-form-group">
                    <label for="create_wa_no">No. Handphone / WhatsApp <span class="required-star">*</span></label>
                    <input type="tel" name="wa_no" id="create_wa_no" class="modal-form-input" placeholder="e.g. 08123456789" inputmode="numeric" required>
                </div>

                <!-- Request Type -->
                <div class="modal-form-group">
                    <label for="create_req_type">Request Type <span class="required-star">*</span></label>
                    <select name="req_type" id="create_req_type" class="modal-form-select" required>
                        <option value="">-- Select Request Type --</option>
                        <?php if (!empty($requestTypes)): ?>
                            <?php foreach ($requestTypes as $rt): ?>
                                <option value="<?= esc($rt['name']) ?>"><?= esc($rt['name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="General Support">General Support</option>
                            <option value="Hardware / Software">Hardware / Software</option>
                            <option value="Access & Permission">Access & Permission</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Ticket Priority -->
                <div class="modal-form-group">
                    <label for="create_priority">Priority</label>
                    <select name="ticket_priority" id="create_priority" class="modal-form-select">
                        <option value="">Auto (Default by SLA)</option>
                        <option value="low">Low Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="high">High Priority</option>
                        <option value="urgent">Urgent Priority</option>
                    </select>
                </div>

                <!-- Subject (Full Width) -->
                <div class="modal-form-group full-width">
                    <label for="create_subject">Ticket Subject <span class="required-star">*</span></label>
                    <input type="text" name="subject" id="create_subject" class="modal-form-input" placeholder="Brief summary of the issue or request" required>
                </div>

                <!-- Description / Message (Full Width) -->
                <div class="modal-form-group full-width">
                    <label for="create_message">Detailed Description</label>
                    <textarea name="message" id="create_message" rows="3" class="modal-form-textarea" placeholder="Provide complete details, steps to reproduce, or any relevant information..."></textarea>
                </div>

                <!-- File Attachment (Full Width) -->
                <div class="modal-form-group full-width">
                    <label for="create_attachment">Attachment <span class="file-hint">(.jpg, .png, .pdf, .docx - Max 5MB)</span></label>
                    <div class="modal-file-upload-box" onclick="document.getElementById('create_attachment').click()">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span id="createFileLabel" class="file-label-text">Choose file or drag here</span>
                        <input type="file" name="attachment" id="create_attachment" class="modal-hidden-file" accept=".jpg,.jpeg,.png,.pdf,.docx" onchange="handleCreateFileChange(this)">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="create-ticket-modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeCreateTicketModal()">Cancel</button>
                <button type="submit" id="btnSubmitCreateTicket" class="btn-modal-submit">
                    <span class="btn-text">Submit Ticket</span>
                    <span class="btn-spinner" style="display:none;"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateTicketModal() {
        var modal = document.getElementById('createTicketModal');
        var form = document.getElementById('createTicketForm');
        var alertBox = document.getElementById('createTicketAlert');
        var fileLabel = document.getElementById('createFileLabel');
        
        if (form) form.reset();
        if (alertBox) {
            alertBox.style.display = 'none';
            alertBox.innerHTML = '';
        }
        if (fileLabel) fileLabel.textContent = 'Choose file or drag here';

        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(function() {
                var firstInput = document.getElementById('create_emp_name');
                if (firstInput) firstInput.focus();
            }, 100);
        }
    }

    function closeCreateTicketModal() {
        var modal = document.getElementById('createTicketModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function handleCreateFileChange(input) {
        var fileLabel = document.getElementById('createFileLabel');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('File size exceeds 5MB limit.');
                input.value = '';
                if (fileLabel) fileLabel.textContent = 'Choose file or drag here';
                return;
            }
            if (fileLabel) fileLabel.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        } else {
            if (fileLabel) fileLabel.textContent = 'Choose file or drag here';
        }
    }

    function handleCreateTicketSubmit(e) {
        e.preventDefault();
        var form = document.getElementById('createTicketForm');
        var submitBtn = document.getElementById('btnSubmitCreateTicket');
        var btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
        var btnSpinner = submitBtn ? submitBtn.querySelector('.btn-spinner') : null;
        var alertBox = document.getElementById('createTicketAlert');

        if (!form) return;

        // UI Loading
        if (submitBtn) submitBtn.disabled = true;
        if (btnText) btnText.style.display = 'none';
        if (btnSpinner) btnSpinner.style.display = 'inline-block';
        if (alertBox) alertBox.style.display = 'none';

        var formData = new FormData(form);

        fetch('<?= base_url('ticket/store') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) {
            return res.json().then(function(data) {
                return { status: res.status, ok: res.ok, data: data };
            });
        })
        .then(function(response) {
            if (response.ok && response.data.status === 'success') {
                if (alertBox) {
                    alertBox.className = 'modal-alert-box alert-success';
                    alertBox.innerHTML = '<strong>Success!</strong> ' + (response.data.message || 'Ticket created successfully.');
                    alertBox.style.display = 'block';
                }
                setTimeout(function() {
                    closeCreateTicketModal();
                    window.location.reload();
                }, 1200);
            } else {
                var msg = response.data && response.data.message ? response.data.message : 'Failed to create ticket. Please check input.';
                if (alertBox) {
                    alertBox.className = 'modal-alert-box alert-danger';
                    alertBox.innerHTML = '<strong>Error:</strong> ' + msg;
                    alertBox.style.display = 'block';
                }
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.style.display = 'inline';
                if (btnSpinner) btnSpinner.style.display = 'none';
            }
        })
        .catch(function(err) {
            if (alertBox) {
                alertBox.className = 'modal-alert-box alert-danger';
                alertBox.innerHTML = '<strong>Error:</strong> Failed to connect to server. Please try again.';
                alertBox.style.display = 'block';
            }
            if (submitBtn) submitBtn.disabled = false;
            if (btnText) btnText.style.display = 'inline';
            if (btnSpinner) btnSpinner.style.display = 'none';
        });
    }

    // Close on Escape or click outside
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateTicketModal();
        }
    });

    document.addEventListener('click', function(e) {
        var modal = document.getElementById('createTicketModal');
        if (modal && e.target === modal) {
            closeCreateTicketModal();
        }
    });
</script>
