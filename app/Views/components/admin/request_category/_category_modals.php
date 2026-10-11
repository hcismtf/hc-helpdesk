<?php
/**
 * Request Category Modals (Add, Edit, Delete for both Sub Category and Main Category)
 *
 * @var array $categories  List of active main categories for dropdown options
 * @var array $slas        List of SLA options
 */
$categories = $categories ?? [];
$slas       = $slas ?? [];
?>

<!-- =========================================================================
     MODALS FOR REQUEST TYPE (SUB CATEGORY)
     ========================================================================= -->

<!-- Modal Add Request Type -->
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
                        <?php if (!empty($slas)): ?>
                            <?php foreach ($slas as $s): ?>
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

<!-- Modal Edit Request Type -->
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
                        <?php if (!empty($slas)): ?>
                            <?php foreach ($slas as $s): ?>
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

<!-- Modal Delete Request Type -->
<div id="requestTypeDeleteModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:400px; text-align:center;">
        <div class="modal-title-clean" style="margin-bottom:8px;">Hapus Request Type?</div>
        <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus Request Type ini?</p>
        <input type="hidden" id="delete-request-type-id">
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" class="btn-modal-cancel" onclick="closeRequestTypeDeleteModal()">Batal</button>
            <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteRequestType()">Hapus</button>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODALS FOR MAIN CATEGORY (TOPIK HC)
     ========================================================================= -->

<!-- Modal Add Main Category -->
<div id="categoryModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Add New Main Category (Topik HC)</span>
            <button type="button" class="modal-close-btn" onclick="closeCategoryModal()">&times;</button>
        </div>
        <form id="category-form">
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="category-name" class="modal-form-label">Category Name <span style="color:#ef4444">*</span></label>
                    <input id="category-name" type="text" placeholder="e.g. Presensi & Kehadiran" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="category-code" class="modal-form-label">Category Code</label>
                    <input id="category-code" type="text" placeholder="e.g. PRESENSI" class="modal-form-input" style="text-transform:uppercase;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="category-icon" class="modal-form-label">Icon (FontAwesome)</label>
                    <input id="category-icon" type="text" placeholder="fa-solid fa-calendar-check" value="fa-solid fa-folder" class="modal-form-input">
                </div>
                <div class="modal-form-group">
                    <label for="category-color" class="modal-form-label">Accent Color</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input id="category-color" type="color" value="#2563EB" style="width:40px; height:38px; border:1px solid #cbd5e1; border-radius:6px; padding:2px; cursor:pointer;">
                        <input id="category-color-hex" type="text" value="#2563EB" class="modal-form-input" style="flex:1;" oninput="document.getElementById('category-color').value=this.value">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label for="category-sort" class="modal-form-label">Sort Order</label>
                    <input id="category-sort" type="number" value="1" min="0" class="modal-form-input">
                </div>
            </div>

            <div class="modal-form-group">
                <label for="category-desc" class="modal-form-label">Description</label>
                <textarea id="category-desc" placeholder="Penjelasan ruang lingkup kategori topik HC ini..." class="modal-form-textarea" style="min-height:75px;"></textarea>
            </div>

            <div class="modal-form-group">
                <label for="category-status" class="modal-form-label">Status</label>
                <select id="category-status" class="modal-form-select">
                    <option value="1">Active</option>
                    <option value="0">In Active</option>
                </select>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Create Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Main Category -->
<div id="categoryEditModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Edit Main Category (Topik HC)</span>
            <button type="button" class="modal-close-btn" onclick="closeCategoryEditModal()">&times;</button>
        </div>
        <form id="category-edit-form">
            <input type="hidden" id="edit-category-id">
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="edit-category-name" class="modal-form-label">Category Name <span style="color:#ef4444">*</span></label>
                    <input id="edit-category-name" type="text" class="modal-form-input" required>
                </div>
                <div class="modal-form-group">
                    <label for="edit-category-code" class="modal-form-label">Category Code</label>
                    <input id="edit-category-code" type="text" class="modal-form-input" style="text-transform:uppercase;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                <div class="modal-form-group">
                    <label for="edit-category-icon" class="modal-form-label">Icon (FontAwesome)</label>
                    <input id="edit-category-icon" type="text" class="modal-form-input">
                </div>
                <div class="modal-form-group">
                    <label for="edit-category-color" class="modal-form-label">Accent Color</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input id="edit-category-color" type="color" value="#2563EB" style="width:40px; height:38px; border:1px solid #cbd5e1; border-radius:6px; padding:2px; cursor:pointer;">
                        <input id="edit-category-color-hex" type="text" value="#2563EB" class="modal-form-input" style="flex:1;" oninput="document.getElementById('edit-category-color').value=this.value">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label for="edit-category-sort" class="modal-form-label">Sort Order</label>
                    <input id="edit-category-sort" type="number" min="0" class="modal-form-input">
                </div>
            </div>

            <div class="modal-form-group">
                <label for="edit-category-desc" class="modal-form-label">Description</label>
                <textarea id="edit-category-desc" class="modal-form-textarea" style="min-height:75px;"></textarea>
            </div>

            <div class="modal-form-group">
                <label for="edit-category-status" class="modal-form-label">Status</label>
                <select id="edit-category-status" class="modal-form-select">
                    <option value="1">Active</option>
                    <option value="0">In Active</option>
                </select>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeCategoryEditModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Main Category -->
<div id="categoryDeleteModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:420px; text-align:center;">
        <div class="modal-title-clean" style="margin-bottom:8px;">Hapus Main Category?</div>
        <p id="delete-category-prompt" style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Apakah Anda yakin ingin menghapus Kategori Utama ini?</p>
        <input type="hidden" id="delete-category-id">
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" class="btn-modal-cancel" onclick="closeCategoryDeleteModal()">Batal</button>
            <button type="button" class="btn-modal-submit" style="background:#dc2626;" onclick="confirmDeleteCategory()">Hapus</button>
        </div>
    </div>
</div>
