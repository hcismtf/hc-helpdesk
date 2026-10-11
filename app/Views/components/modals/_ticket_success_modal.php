<?php
/**
 * Ticket Success Modal
 * Tampil setelah tiket berhasil diajukan via AJAX
 */
?>
<div class="modal custom-modal" id="ticketSuccessModal">
  <div class="modal-background" onclick="TicketWizard.closeSuccessModal()"></div>
  <div class="modal-card">
    <header class="modal-card-head has-text-centered is-block py-4">
      <div class="modal-success-icon-wrapper mb-2">
        <i class="fa-solid fa-circle-check text-success"></i>
      </div>
      <p class="modal-card-title is-size-4 font-weight-bold mb-0">Tiket Berhasil Diajukan!</p>
    </header>
    <section class="modal-card-body has-text-centered py-4">
      <p class="is-size-6 has-text-grey mb-3">
        Tiket pengaduan / permohonan Anda telah berhasil dicatat dalam antrean layanan Human Capital MTF.
      </p>

      <!-- Ticket Number Display Box -->
      <div class="ticket-number-success-box mb-4">
        <span class="ticket-number-label">Nomor Tiket Anda:</span>
        <div class="ticket-number-code">
          <strong id="successTicketNo">HC-2026-00001</strong>
          <button type="button" class="button is-small is-ghost ml-2" onclick="TicketWizard.copyTicketNo()" title="Salin Nomor Tiket">
            <i class="fa-regular fa-copy"></i>
          </button>
        </div>
      </div>

      <div class="notification is-info is-light is-size-7 has-text-left mb-0">
        <i class="fa-solid fa-shield-halved mr-1"></i>
        Simpan nomor tiket ini untuk memantau status tindak lanjut secara berkala melalui menu <strong>Lacak Tiket</strong> pada halaman utama.
      </div>
    </section>
    <footer class="modal-card-foot is-justify-content-center py-3">
      <a href="<?= base_url('beranda') ?>" class="button is-light mr-2">
        Ke Beranda
      </a>
      <a href="#" id="btnTrackSuccessTicket" class="button is-primary">
        <i class="fa-solid fa-magnifying-glass mr-2"></i> Lacak Status Tiket
      </a>
    </footer>
  </div>
</div>
