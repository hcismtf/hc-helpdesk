<?php
/**
 * System Settings Modals Component (Add/Edit/Delete for Roles, SLA, Request Type, FAQ)
 *
 * @var array $permissions
 * @var array $categories
 * @var array $allSlas
 */
$permissions = $permissions ?? [];
$categories  = $categories ?? [];
$allSlas     = $allSlas ?? [];

$permOptions = [];
if (!empty($permissions)) {
    foreach ($permissions as $perm) {
        $permOptions[$perm['id']] = $perm['name'];
    }
}
?>

<!-- 1. Modal Add User Role -->
<div id="userRoleModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal modal-role-scrollable" style="max-height:85vh; display:flex; flex-direction:column;">
        <div class="modal-header-clean" style="flex-shrink:0;">
            <span class="modal-title-clean">Add New User Role</span>
            <button type="button" class="modal-close-btn" onclick="closeUserRoleModal()">&times;</button>
        </div>
        <form id="user-role-form" style="display:flex; flex-direction:column; min-height:0; flex:1;">
            <div class="modal-body-scrollable" style="overflow-y:auto; padding-right:4px; margin-bottom:12px; flex:1;">
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

            <div class="modal-footer-clean" style="flex-shrink:0; margin-top:auto;">
                <button type="button" class="btn-modal-cancel" onclick="closeUserRoleModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Create Role</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Edit User Role -->
<div id="userRoleEditModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal modal-role-scrollable" style="max-height:85vh; display:flex; flex-direction:column;">
        <div class="modal-header-clean" style="flex-shrink:0;">
            <span class="modal-title-clean">Edit User Role</span>
            <button type="button" class="modal-close-btn" onclick="closeUserRoleEditModal()">&times;</button>
        </div>
        <form id="user-role-edit-form" style="display:flex; flex-direction:column; min-height:0; flex:1;">
            <input type="hidden" id="edit-role-id">
            <div class="modal-body-scrollable" style="overflow-y:auto; padding-right:4px; margin-bottom:12px; flex:1;">
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

            <div class="modal-footer-clean" style="flex-shrink:0; margin-top:auto;">
                <button type="button" class="btn-modal-cancel" onclick="closeUserRoleEditModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Delete User Role -->
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

<!-- 4. Modal Add SLA -->
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

<!-- 5. Modal Edit SLA -->
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

<!-- 6. Modal Delete SLA -->
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

<!-- 7. Modal Add Request Type -->
<div id="requestTypeModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Add New Request Type (Sub Category)</span>
            <button type="button" class="modal-close-btn" onclick="closeRequestTypeModal()">&times;</button>
        </div>
        <form id="request-type-form">
            <div class="modal-form-group">
                <label for="request-type-name" class="modal-form-label">Request Type Name <span style="color:#ef4444">*</span></label>
                <input id="request-type-name" type="text" placeholder="e.g. Koreksi Kehadiran & Presensi" class="modal-form-input" required>
            </div>
            <div class="modal-form-group">
                <label for="request-type-category" class="modal-form-label">Main Category (Topik HC) <span style="color:#ef4444">*</span></label>
                <select id="request-type-category" class="modal-form-select" required>
                    <option value="">-- Pilih Topik Kategori Utama --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= is_array($cat) ? $cat['id'] : $cat->id ?>">
                                <?= is_array($cat) ? esc($cat['name']) : esc($cat->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="request-type-sla" class="modal-form-label">SLA Policy</label>
                    <select id="request-type-sla" class="modal-form-select">
                        <option value="">-- Default SLA --</option>
                        <?php if (!empty($allSlas)): ?>
                            <?php foreach ($allSlas as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= esc($s['priority']) ?> (<?= esc($s['resolution_time']) ?> Jam)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label for="request-type-badge" class="modal-form-label">Badge Tag (Label)</label>
                    <input id="request-type-badge" type="text" placeholder="e.g. PRESENSI" class="modal-form-input">
                </div>
            </div>
            <div class="modal-form-group">
                <label for="request-type-desc" class="modal-form-label">Description</label>
                <textarea id="request-type-desc" placeholder="Scope and purpose of this service..." class="modal-form-textarea" style="min-height:75px;"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center;">
                <div class="modal-form-group">
                    <label for="request-type-status" class="modal-form-label">Status</label>
                    <select id="request-type-status" class="modal-form-select">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                </div>
                <div class="modal-form-group" style="padding-top:20px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" id="request-type-popular" value="1" style="width:16px; height:16px;">
                        <span>Tampilkan di Kategori Populer Beranda</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Create Type</button>
            </div>
        </form>
    </div>
</div>

<!-- 8. Modal Edit Request Type -->
<div id="requestTypeEditModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Edit Request Type (Sub Category)</span>
            <button type="button" class="modal-close-btn" onclick="closeRequestTypeEditModal()">&times;</button>
        </div>
        <form id="request-type-edit-form">
            <input type="hidden" id="edit-request-type-id">
            <div class="modal-form-group">
                <label for="edit-request-type-name" class="modal-form-label">Request Type Name <span style="color:#ef4444">*</span></label>
                <input id="edit-request-type-name" type="text" class="modal-form-input" required>
            </div>
            <div class="modal-form-group">
                <label for="edit-request-type-category" class="modal-form-label">Main Category (Topik HC) <span style="color:#ef4444">*</span></label>
                <select id="edit-request-type-category" class="modal-form-select" required>
                    <option value="">-- Pilih Topik Kategori Utama --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= is_array($cat) ? $cat['id'] : $cat->id ?>">
                                <?= is_array($cat) ? esc($cat['name']) : esc($cat->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="edit-request-type-sla" class="modal-form-label">SLA Policy</label>
                    <select id="edit-request-type-sla" class="modal-form-select">
                        <option value="">-- Default SLA --</option>
                        <?php if (!empty($allSlas)): ?>
                            <?php foreach ($allSlas as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= esc($s['priority']) ?> (<?= esc($s['resolution_time']) ?> Jam)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label for="edit-request-type-badge" class="modal-form-label">Badge Tag (Label)</label>
                    <input id="edit-request-type-badge" type="text" placeholder="e.g. PRESENSI" class="modal-form-input">
                </div>
            </div>
            <div class="modal-form-group">
                <label for="edit-request-type-desc" class="modal-form-label">Description</label>
                <textarea id="edit-request-type-desc" class="modal-form-textarea" style="min-height:75px;"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center;">
                <div class="modal-form-group">
                    <label for="edit-request-type-status" class="modal-form-label">Status</label>
                    <select id="edit-request-type-status" class="modal-form-select">
                        <option value="Active">Active</option>
                        <option value="In Active">In Active</option>
                    </select>
                </div>
                <div class="modal-form-group" style="padding-top:20px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" id="edit-request-type-popular" value="1" style="width:16px; height:16px;">
                        <span>Tampilkan di Kategori Populer Beranda</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeEditModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 9. Modal Delete Request Type -->
<div id="requestTypeDeleteModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:400px; text-align:center;">
        <div class="modal-title-clean" style="margin-bottom:8px;">Hapus Request Type?</div>
        <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus kategori Request Type ini?</p>
        <input type="hidden" id="delete-request-type-id">
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeDeleteModal()">Batal</button>
            <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteRequestType()">Hapus</button>
        </div>
    </div>
</div>

<!-- 10. Modal Add FAQ -->
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

<!-- 11. Modal Edit FAQ -->
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
