<?php
/**
 * User Management Modals Component (Add User with Candidate Search & Edit User)
 *
 * @var array $roles
 */
$roles = $roles ?? [];
?>

<!-- Modal Add User -->
<div id="addUserModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width: 520px;">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Add User & Assign Role</span>
            <button type="button" class="modal-close-btn" onclick="closeAddUserModal()">&times;</button>
        </div>
        <form id="addUserForm" method="post" action="<?= base_url('admin/add_user') ?>" novalidate>
            <!-- Hidden fields filled automatically upon candidate selection -->
            <input type="hidden" name="user_id" id="add-selected-user-id">
            <input type="hidden" name="name" id="add-selected-name">
            <input type="hidden" name="employee_no" id="add-selected-nip">
            <input type="hidden" name="email" id="add-selected-email">

            <!-- 1. Search NIP - Nama Dropdown -->
            <div class="modal-form-group">
                <label class="modal-form-label">Search Employee (NIP - Nama) <span style="color:#ef4444">*</span></label>
                <div class="searchable-user-dropdown-wrap" style="position:relative;">
                    <div class="search-input-box" style="position:relative; display:flex; align-items:center;">
                        <input type="text" id="candidate-search-input" class="modal-form-input" 
                               placeholder="Ketik NIP atau Nama karyawan..." 
                               autocomplete="off" 
                               onfocus="searchCandidates(this.value)" 
                               oninput="searchCandidates(this.value)">
                        <span id="candidate-clear-btn" style="position:absolute; right:12px; cursor:pointer; color:#94a3b8; display:none;" onclick="clearSelectedCandidate()">&times;</span>
                    </div>
                    
                    <!-- Floating Dropdown Result List -->
                    <div id="candidate-results-menu" class="candidate-results-dropdown" style="display:none;">
                        <div id="candidate-items-container" class="candidate-items-list"></div>
                    </div>
                </div>
                <small style="color:#64748b; font-size:11.5px; margin-top:4px; display:block;">Pilih akun karyawan untuk diberikan penugasan role.</small>
            </div>

            <!-- 2. Selected Employee Details Card (Preview) -->
            <div id="selected-employee-preview" class="selected-employee-card" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; margin-bottom:18px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="font-weight:700; color:#0f172a; font-size:13.5px;" id="preview-name">-</div>
                        <div style="font-size:12px; color:#475569; margin-top:2px;">
                            <span id="preview-nip" style="font-weight:600; color:#4f46e5;">NIP: -</span> &bull; 
                            <span id="preview-email">-</span>
                        </div>
                        <div style="font-size:11.5px; color:#64748b; margin-top:2px;" id="preview-position">-</div>
                    </div>
                    <span class="badge-selected-status" style="font-size:10.5px; font-weight:700; color:#059669; background:#d1fae5; padding:2px 8px; border-radius:12px;">Verified</span>
                </div>
            </div>

            <!-- 3. Role Select -->
            <div class="modal-form-group">
                <label class="modal-form-label">Role <span style="color:#ef4444">*</span></label>
                <select name="role" id="add-user-role-select" class="modal-form-select" required>
                    <option value="">-- Select Role --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Status Select -->
            <div class="modal-form-group">
                <label class="modal-form-label">Status <span style="color:#ef4444">*</span></label>
                <select name="status" class="modal-form-select" required>
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeAddUserModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit" id="btn-submit-add-user">Assign Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Edit User</span>
            <button type="button" class="modal-close-btn" onclick="closeEditUserModal()">&times;</button>
        </div>
        <form id="editUserForm" method="post" action="<?= base_url('admin/edit_user') ?>" novalidate>
            <input type="hidden" name="id" id="edit-user-id">
            <div class="modal-form-group">
                <label class="modal-form-label">Full Name <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" id="edit-user-name" class="modal-form-input" required>
            </div>
            <div class="modal-form-group">
                <label class="modal-form-label">Email Address <span style="color:#ef4444">*</span></label>
                <input type="email" name="email" id="edit-user-email" class="modal-form-input" required>
            </div>
            <div class="modal-form-group">
                <label class="modal-form-label">Change Password <span style="font-weight:normal;color:#888;font-size:0.88em;">(Kosongkan jika tidak ingin ganti)</span></label>
                <div style="position:relative; display:flex; align-items:center;">
                    <input type="password" name="password" id="edit-user-password" class="modal-form-input"
                        placeholder="Input password baru" style="padding-right:40px;"
                        minlength="16" pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':\\|,.<>\/~`]).{16,}$"
                        title="Minimal 16 karakter, harus mengandung setidaknya 1 huruf kapital dan 1 karakter spesial">
                    <span style="position:absolute; right:12px; cursor:pointer; color:#94a3b8;" onclick="toggleEditUserPassword()">
                        <i id="edit-eye-icon" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            <div class="modal-form-group">
                <label class="modal-form-label">Role <span style="color:#ef4444">*</span></label>
                <select name="role" id="edit-user-role" class="modal-form-select" required>
                    <option value="">-- Select User Role --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-form-group">
                <label class="modal-form-label">Status <span style="color:#ef4444">*</span></label>
                <select name="status" id="edit-user-status" class="modal-form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeEditUserModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>
