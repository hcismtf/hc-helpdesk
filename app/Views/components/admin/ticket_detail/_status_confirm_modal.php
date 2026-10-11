<?php
/**
 * Ticket Detail Modal: Status Change Confirmation
 *
 * Popup konfirmasi pengubahan status tiket interaktif disertai input catatan alasan perubahan.
 */
?>
<div id="statusConfirmModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:480px;">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Konfirmasi Perubahan Status</span>
            <button type="button" class="modal-close-btn" onclick="closeStatusConfirmModal()">&times;</button>
        </div>
        
        <p style="font-size:13.5px; color:#475569; margin:0 0 16px 0;">
            Apakah Anda yakin ingin mengubah status tiket ini? Perubahan ini akan dicatat dalam riwayat audit trail.
        </p>

        <div class="status-confirm-details">
            <span style="font-size:12.5px; color:#64748b; font-weight:600;">Status Baru:</span>
            <span class="status-badge-inline" id="confirmTargetStatusBadge">
                <span class="status-dot" id="confirmTargetStatusDot"></span>
                <strong id="confirmTargetStatusLabel">-</strong>
            </span>
        </div>

        <div class="modal-form-group">
            <label class="modal-form-label">Catatan / Alasan Perubahan (Opsional)</label>
            <textarea id="confirmStatusNotes" class="modal-form-textarea" style="min-height:75px;" placeholder="Tuliskan catatan alasan perubahan status..."></textarea>
        </div>

        <div class="modal-footer-clean">
            <button type="button" class="btn-modal-cancel" onclick="closeStatusConfirmModal()">Batal</button>
            <button type="button" class="btn-modal-submit" id="btnConfirmStatusSubmit" onclick="proceedTicketStatusChange()">Ya, Ubah Status</button>
        </div>
    </div>
</div>
