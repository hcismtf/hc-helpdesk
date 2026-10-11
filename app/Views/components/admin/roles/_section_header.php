<?php
/**
 * Roles Management Section Header Component
 *
 * Menampilkan judul section, badge RBAC, deskripsi, input search role, dan tombol Add New Role.
 */
?>
<div class="settings-section-header">
    <div class="section-headline-group">
        <div class="headline-title-row">
            <h2 class="headline-title">User Roles & Permissions</h2>
            <span class="headline-badge badge-rbac">RBAC</span>
        </div>
        <p class="headline-desc">Kelola tingkatan akses peran (roles), modul otorisasi, dan anggota karyawan yang terhubung.</p>
    </div>
    <div class="section-actions-row">
        <div class="search-input-wrapper">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="role-search-input" class="settings-search-input" placeholder="Filter roles..." onkeyup="filterRolesTable(this.value)">
        </div>
        <button type="button" class="btn-primary-add" id="btn-add-user-role">
            <span style="font-size:16px;">+</span>
            <span>Add New Role</span>
        </button>
    </div>
</div>
