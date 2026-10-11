<?php
/**
 * Floating Asynchronous Upload & Ticket Progress Toast
 * Ditampilkan di kanan bawah layar agar pengguna bebas menjelajahi halaman lain tanpa terhalang.
 */
?>

<div class="async-upload-toast" id="asyncUploadToast" style="display: none;" role="status" aria-live="polite">
  <div class="toast-card">
    
    <!-- Toast Header -->
    <div class="toast-header">
      <div class="toast-header-left">
        <span class="toast-icon-circle" id="toastStatusIcon">
          <i class="fa-solid fa-circle-notch fa-spin text-primary"></i>
        </span>
        <div class="toast-title-group">
          <h5 class="toast-title" id="toastTitle">Membuat Tiket Layanan</h5>
          <span class="toast-subtitle" id="toastSubtitle">Memproses data ke server...</span>
        </div>
      </div>
      <div class="toast-header-right">
        <span class="toast-pill-badge" id="toastPercentageBadge">0%</span>
        <button type="button" class="toast-close-btn" onclick="(window.GlobalTicketToast ? GlobalTicketToast.close() : (window.TicketWizard && TicketWizard.closeAsyncToast()))" title="Tutup Notifikasi">&times;</button>
      </div>
    </div>

    <!-- Toast Body / Info -->
    <div class="toast-body">
      <!-- Info Subjek & File -->
      <div class="toast-meta-line" id="toastSubjectLine">
        <i class="fa-solid fa-envelope-open-text text-muted mr-1"></i>
        <span class="toast-meta-value" id="toastSubjectText">—</span>
      </div>
      <div class="toast-meta-line" id="toastFileLine" style="display: none;">
        <i class="fa-solid fa-paperclip text-info mr-1"></i>
        <span class="toast-meta-value" id="toastFileText">—</span>
      </div>

      <!-- Animated Progress Bar -->
      <div class="toast-progress-track mb-2">
        <div class="toast-progress-bar" id="toastProgressBar" style="width: 0%;"></div>
      </div>

      <!-- Detail Byte & Status -->
      <div class="toast-status-row">
        <span class="toast-bytes-text" id="toastBytesText">Menghubungkan...</span>
        <span class="toast-note-text" id="toastNoteText">Anda dapat tetap menjelajahi halaman lain</span>
      </div>

      <!-- Complete State Actions -->
      <div class="toast-complete-box" id="toastCompleteBox" style="display: none;">
        <div class="toast-ticket-code-row">
          <span class="toast-ticket-label">Nomor Tiket:</span>
          <strong class="toast-ticket-number" id="toastTicketNo">HC-2026-00001</strong>
          <button type="button" class="button is-small is-ghost ml-1" onclick="(window.GlobalTicketToast ? GlobalTicketToast.copyTicketNo() : (window.TicketWizard && TicketWizard.copyAsyncTicketNo()))" title="Salin Nomor Tiket">
            <i class="fa-regular fa-copy"></i>
          </button>
        </div>
        <div class="toast-actions-row mt-2">
          <a href="#" id="toastTrackLink" class="button is-small is-primary is-fullwidth">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Lacak Status Tiket
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
