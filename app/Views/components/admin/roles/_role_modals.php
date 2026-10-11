<?php
/**
 * Roles Management Modals Component (Add, Edit, Delete Roles)
 *
 * @var array $permissions
 */
$permissions = $permissions ?? [];
$permOptions = [];
if (!empty($permissions)) {
    foreach ($permissions as $perm) {
        $permOptions[$perm['id']] = $perm['name'];
    }
}
?>

<!-- Modal Add User Role -->
<div id="userRoleModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal modal-role-scrollable">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Add New User Role</span>
            <button type="button" class="modal-close-btn" onclick="closeUserRoleModal()">&times;</button>
        </div>
        <form id="user-role-form" style="display:flex; flex-direction:column; min-height:0; flex:1;">
            <div class="modal-body-scrollable">
                <div class="modal-form-group">
                    <label for="role-name" class="modal-form-label">Role Name <span style="color:#ef4444">*</span></label>
                    <input id="role-name" type="text" placeholder="e.g. Senior Support Lead" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">Authorized Permissions <span style="color:#ef4444">*</span></label>
                    <div id="permission-container" style="display: flex; flex-direction: column; gap: 8px; max-height: 380px; overflow-y: auto; padding-right: 4px;">
                        <div class="permission-row" style="display:flex; align-items:center; gap:8px;">
                            <div style="flex:1;">
                                <?= view('components/common/_basic_select', [
                                    'name'        => 'permissions[]',
                                    'id'          => 'perm_initial_add',
                                    'placeholder' => 'Select Permission Module',
                                    'options'     => $permOptions
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-permission-btn" style="margin-top:8px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:12.5px; font-weight:600; cursor:pointer; align-self: flex-start;">
                        + Add Another Permission
                    </button>
                </div>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeUserRoleModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Create Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User Role -->
<div id="userRoleEditModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal modal-role-scrollable">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Edit User Role</span>
            <button type="button" class="modal-close-btn" onclick="closeUserRoleEditModal()">&times;</button>
        </div>
        <form id="user-role-edit-form" style="display:flex; flex-direction:column; min-height:0; flex:1;">
            <input type="hidden" id="edit-role-id">
            <div class="modal-body-scrollable">
                <div class="modal-form-group">
                    <label for="edit-role-name" class="modal-form-label">Role Name <span style="color:#ef4444">*</span></label>
                    <input id="edit-role-name" type="text" class="modal-form-input" required>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">Authorized Permissions <span style="color:#ef4444">*</span></label>
                    <div id="edit-permission-container" style="display: flex; flex-direction: column; gap: 8px; max-height: 380px; overflow-y: auto; padding-right: 4px;"></div>
                    <button type="button" id="add-edit-permission-btn" style="margin-top:8px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:12.5px; font-weight:600; cursor:pointer; align-self: flex-start;">
                        + Add Another Permission
                    </button>
                </div>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeUserRoleEditModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete User Role -->
<div id="userRoleDeleteModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:400px; text-align:center;">
        <div class="modal-title-clean" style="margin-bottom:8px;">Hapus User Role?</div>
        <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus User Role ini? Tindakan ini tidak dapat dibatalkan.</p>
        <input type="hidden" id="delete-role-id">
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" class="btn-modal-cancel" onclick="closeUserRoleDeleteModal()">Batal</button>
            <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteUserRole()">Hapus</button>
        </div>
    </div>
</div>
