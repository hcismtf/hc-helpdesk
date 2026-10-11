<!-- MODAL: HASIL LACAK TIKET -->
<div class="landing-modal-overlay" id="trackResultModal">
  <div class="landing-modal">
    <div class="modal-header">
      <h3><i class="fas fa-ticket-alt" style="color: #2563EB; margin-right: 8px;"></i> Status Tiket Anda</h3>
      <button type="button" class="btn-close-modal" onclick="closeModal('trackResultModal')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="track-result-card" id="trackResultBody">
        <!-- Populated dynamically via JS -->
      </div>
      <div style="margin-top: 18px; text-align: right;">
        <button type="button" class="btn-quick-action" style="width: auto; padding: 8px 20px; display: inline-block;" onclick="closeModal('trackResultModal')">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>
