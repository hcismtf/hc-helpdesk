<!-- RIGHT COLUMN: Lacak Status Tiket Aside Card -->
<aside class="track-column" id="lacak-tiket">
  <div class="track-ticket-card">
    <div class="track-header">
      <div class="track-icon-box">
        <img src="<?= base_url('assets/icons/global-search.svg') ?>" alt="Lacak Tiket">
      </div>
      <div>
        <h2 class="track-title">Lacak Status Tiket</h2>
        <div class="track-subtitle">Cek progres penanganan tiket secara instan</div>
      </div>
    </div>

    <p class="track-instruction">
      Sudah pernah mengajukan keluhan atau tiket bantuan sebelumnya? Masukkan nomor referensi untuk melacak status SLA.
    </p>

    <form id="trackTicketForm" class="track-form" onsubmit="handleTrackTicket(event)">
      <div class="track-inputs-stack">
        <div class="form-group-landing">
          <label for="track_ticket_no">Nomor Tiket <span class="required-star">*</span></label>
          <div class="input-with-icon">
            <i class="fas fa-ticket-alt input-icon"></i>
            <input type="text" id="track_ticket_no" name="ticket_no" placeholder="CTH: #HC-2025-0042" required autocomplete="off">
          </div>
        </div>

        <div class="form-group-landing">
          <label for="track_email">Email Pelapor <span class="label-opt">(Opsional)</span></label>
          <div class="input-with-icon">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" id="track_email" name="email" placeholder="nama@perusahaan.co.id" autocomplete="off">
          </div>
        </div>
      </div>

      <div class="track-actions-group">
        <button type="submit" class="btn-track-submit" id="btnTrackSubmit">
          <i class="fas fa-search btn-submit-icon"></i>
          <span>Lacak Progres Penanganan</span>
        </button>

        <div class="track-no-ticket-wrap">
          <span class="no-ticket-text">Ingin melihat detail tiket?</span>
          <a href="javascript:void(0)" onclick="openUserLoginModal();" class="btn-link-create-ticket">
            <span>Masuk ke Akun</span>
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
</aside>
