<?php
/**
 * Step 3: Lampiran & Konfirmasi Akhir Tiket
 */
?>

<div class="ticket-step-panel is-hidden" id="stepPanel3">
  <div class="panel-card">
    <div class="panel-header">
      <span class="panel-step-badge">Langkah 3 dari 3</span>
      <h2 class="panel-title">Lampiran Dokumen &amp; Konfirmasi Akhir</h2>
      <p class="panel-desc">
        Unggah berkas pendukung (jika ada) dan periksa kembali rincian tiket Anda sebelum diajukan ke sistem.
      </p>
    </div>

    <!-- 1. Dropzone Unggah Berkas -->
    <div class="form-section-group mb-5">
      <h4 class="form-section-title">
        <i class="fa-solid fa-paperclip mr-2 text-primary"></i> Berkas Pendukung (Opsional)
      </h4>

      <!-- Dropzone Container -->
      <div class="ticket-dropzone" id="ticketDropzone" onclick="document.getElementById('input_attachment').click()">
        <input type="file"
               name="attachment"
               id="input_attachment"
               class="dropzone-hidden-input"
               accept=".jpg,.jpeg,.png,.webp,.pdf,.docx,.doc,.xlsx,.xls"
               onchange="TicketWizard.handleFileSelected(this)">

        <div class="dropzone-body" id="dropzonePrompt">
          <div class="dropzone-icon-circle">
            <i class="fa-solid fa-cloud-arrow-up"></i>
          </div>
          <div class="dropzone-title">Tarik &amp; letakkan berkas ke sini, atau <span class="text-primary-link">Pilih Dokumen</span></div>
          <p class="dropzone-subtitle">Mendukung: Gambar (JPG, PNG, WEBP), Dokumen (PDF, Word, Excel) &bull; Maksimal 10 MB</p>
        </div>

        <!-- File Selected Preview Card -->
        <div class="dropzone-file-preview" id="dropzoneFilePreview" style="display: none;">
          <div class="file-preview-left">
            <div class="file-icon-box" id="fileTypeIcon">
              <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="file-info-text">
              <div class="file-name" id="previewFileName">document.pdf</div>
              <div class="file-meta" id="previewFileSize">1.2 MB &bull; Siap diunggah</div>
            </div>
          </div>
          <div class="file-preview-right">
            <button type="button" class="button is-small is-danger is-light" onclick="TicketWizard.removeFile(event)" title="Hapus Berkas">
              <i class="fa-solid fa-trash mr-1"></i> Hapus
            </button>
          </div>
        </div>
      </div>
      <p class="help has-text-grey mt-2">
        <i class="fa-solid fa-circle-info mr-1"></i> Unggah tangkapan layar (screenshot), formulir permohonan, atau memo dinas terkait untuk mempercepat proses validasi.
      </p>
    </div>

    <!-- 2. Ringkasan Tiket (Review Summary) -->
    <div class="form-section-group mb-5">
      <h4 class="form-section-title">
        <i class="fa-solid fa-clipboard-check mr-2 text-primary"></i> Ringkasan Pengajuan Tiket
      </h4>

      <div class="ticket-review-card">
        <div class="review-row">
          <div class="review-label">Topik Layanan</div>
          <div class="review-value" id="summaryTopic">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Tingkat Prioritas</div>
          <div class="review-value" id="summaryPriority">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Pelapor (NIP &amp; Nama)</div>
          <div class="review-value" id="summaryRequester">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Kontak Koordinasi</div>
          <div class="review-value" id="summaryContact">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Subjek Pengaduan</div>
          <div class="review-value font-weight-bold text-dark" id="summarySubject">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Kronologi / Catatan</div>
          <div class="review-value" id="summaryMessage">—</div>
        </div>
        <div class="review-row">
          <div class="review-label">Lampiran Berkas</div>
          <div class="review-value" id="summaryAttachment">Tidak ada berkas terlampir</div>
        </div>
      </div>
    </div>

    <!-- 3. Pernyataan Kebenaran Data -->
    <div class="field mb-5">
      <label class="checkbox custom-checkbox">
        <input type="checkbox" id="checkAgreement" required checked>
        <span>
          Saya menyatakan bahwa data yang saya kirimkan adalah benar dan dapat dipertanggungjawabkan untuk keperluan pemrosesan oleh tim Human Capital MTF.
        </span>
      </label>
    </div>

    <!-- Validation / Error Alert Box -->
    <div class="notification is-danger is-light mb-4" id="step3ErrorBox" style="display: none;">
      <button class="delete" type="button" onclick="this.parentElement.style.display='none'"></button>
      <div id="step3ErrorMessage">Terjadi kendala saat mengirimkan tiket.</div>
    </div>

    <!-- Action Buttons -->
    <div class="panel-action-footer">
      <div class="action-footer-left">
        <button type="button" class="button is-light btn-step-prev" onclick="TicketWizard.prevStep(3)">
          <span class="icon is-small mr-2">
            <i class="fa-solid fa-arrow-left"></i>
          </span>
          <span>Kembali ke Detail</span>
        </button>
      </div>
      <div class="action-footer-right">
        <button type="button" id="btnSubmitTicket" class="button is-primary is-medium btn-submit-ticket" onclick="TicketWizard.submitTicketForm(event)">
          <span class="btn-text">
            <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Tiket Sekarang
          </span>
          <span class="btn-loading-state" style="display: none;">
            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengirim Tiket...
          </span>
        </button>
      </div>
    </div>

  </div>
</div>
