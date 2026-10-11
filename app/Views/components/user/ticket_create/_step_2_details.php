<?php
/**
 * Step 2: Detail Masalah & Validasi Data Pegawai
 *
 * @var object|array|null $currentUser Data user yang sedang login jika ada
 */
$loggedNip   = '';
$loggedName  = '';
$loggedEmail = '';
$loggedPhone = '';

if (!empty($currentUser)) {
    $loggedNip   = is_array($currentUser) ? ($currentUser['employee_no'] ?? '') : ($currentUser->getEmployeeNo() ?? $currentUser->employee_no ?? '');
    $loggedName  = is_array($currentUser) ? ($currentUser['name'] ?? '') : ($currentUser->getName() ?? $currentUser->name ?? '');
    $loggedEmail = is_array($currentUser) ? ($currentUser['email'] ?? '') : ($currentUser->getEmail() ?? $currentUser->email ?? '');
    $loggedPhone = is_array($currentUser) ? ($currentUser['phone_number'] ?? '') : ($currentUser->getPhoneNumber() ?? $currentUser->phone_number ?? '');
}
?>

<div class="ticket-step-panel is-hidden" id="stepPanel2">
  <div class="panel-card">
    <div class="panel-header">
      <span class="panel-step-badge">Langkah 2 dari 3</span>
      <h2 class="panel-title">Detail Masalah & Identitas Pegawai</h2>
      <p class="panel-desc">
        Lengkapi informasi identitas diri dan uraikan kronologi kendala atau permohonan kepegawaian Anda secara jelas.
      </p>
    </div>

    <!-- Alert / Banner Notifikasi Auto-fill Akun -->
    <div class="autofill-info-banner mb-5" id="autofillBanner" style="display: none;">
      <div class="autofill-banner-content">
        <div class="autofill-banner-icon">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="autofill-banner-text">
          <strong id="autofillUserTitle">Akun Terverifikasi</strong>
          <p id="autofillUserDesc">Data NIK, nama lengkap, dan email telah disinkronkan secara otomatis.</p>
        </div>
      </div>
      <button type="button" class="button is-small is-ghost" onclick="TicketWizard.resetRequesterFields()">
        Ubah Manual
      </button>
    </div>

    <!-- Group 1: Informasi Pelapor -->
    <div class="form-section-group mb-5">
      <h4 class="form-section-title">
        <i class="fa-solid fa-id-card mr-2 text-primary"></i> Data Diri Pelapor
      </h4>

      <div class="columns is-multiline">
        <!-- NIK / NIP -->
        <div class="column is-6">
          <div class="field">
            <label class="label" for="input_emp_id">
              Nomor Induk Pegawai (NIP / NIK) <span class="has-text-danger">*</span>
            </label>
            <div class="control has-icons-left">
              <input type="text"
                     name="emp_id"
                     id="input_emp_id"
                     class="input custom-input"
                     placeholder="Contoh: 12345678"
                     value="<?= esc($loggedNip) ?>"
                     inputmode="numeric"
                     required>
              <span class="icon is-small is-left">
                <i class="fa-solid fa-address-card"></i>
              </span>
            </div>
            <p class="help has-text-grey" id="help_emp_id">Wajib diisi sesuai NIK resmi Mandiri Tunas Finance.</p>
          </div>
        </div>

        <!-- Nama Lengkap -->
        <div class="column is-6">
          <div class="field">
            <label class="label" for="input_emp_name">
              Nama Lengkap Pegawai <span class="has-text-danger">*</span>
            </label>
            <div class="control has-icons-left">
              <input type="text"
                     name="emp_name"
                     id="input_emp_name"
                     class="input custom-input"
                     placeholder="Contoh: Dimas Pratama"
                     value="<?= esc($loggedName) ?>"
                     required>
              <span class="icon is-small is-left">
                <i class="fa-solid fa-user"></i>
              </span>
            </div>
            <p class="help has-text-grey">Nama lengkap terdaftar di sistem HCIS MTF.</p>
          </div>
        </div>

        <!-- MTF Email -->
        <div class="column is-6">
          <div class="field">
            <label class="label" for="input_email">
              Email Korporat MTF
            </label>
            <div class="control has-icons-left">
              <input type="email"
                     name="email"
                     id="input_email"
                     class="input custom-input"
                     placeholder="nama@mtf.co.id"
                     value="<?= esc($loggedEmail) ?>">
              <span class="icon is-small is-left">
                <i class="fa-solid fa-envelope"></i>
              </span>
            </div>
            <p class="help has-text-grey">Tiket dan notifikasi perkembangan dikirim ke email ini.</p>
          </div>
        </div>

        <!-- No WhatsApp -->
        <div class="column is-6">
          <div class="field">
            <label class="label" for="input_wa_no">
              Nomor Handphone / WhatsApp
            </label>
            <div class="control has-icons-left">
              <input type="tel"
                     name="wa_no"
                     id="input_wa_no"
                     class="input custom-input"
                     placeholder="0812xxxxxxxx"
                     value="<?= esc($loggedPhone) ?>"
                     inputmode="numeric">
              <span class="icon is-small is-left">
                <i class="fa-brands fa-whatsapp"></i>
              </span>
            </div>
            <p class="help has-text-grey">Nomor aktif untuk update tiket via bot/notifikasi WhatsApp.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Group 2: Rincian Kendala / Permohonan -->
    <div class="form-section-group mb-5">
      <h4 class="form-section-title">
        <i class="fa-solid fa-file-lines mr-2 text-primary"></i> Rincian Masalah &amp; Kronologi
      </h4>

      <!-- Subject / Judul Tiket -->
      <div class="field mb-4">
        <label class="label" for="input_subject">
          Subjek / Ringkasan Masalah <span class="has-text-danger">*</span>
        </label>
        <div class="control has-icons-left">
          <input type="text"
                 name="subject"
                 id="input_subject"
                 class="input custom-input"
                 placeholder="Contoh: Permohonan Koreksi Absensi Tanggal 05 Oktober 2026"
                 maxlength="200"
                 required>
          <span class="icon is-small is-left">
            <i class="fa-solid fa-pen-to-square"></i>
          </span>
        </div>
        <p class="help has-text-grey">Tuliskan ringkasan pokok permasalahan dengan singkat dan jelas.</p>
      </div>

      <!-- Detail / Kronologi -->
      <div class="field mb-3">
        <label class="label" for="input_message">
          Kronologi Kendala &amp; Catatan Pendukung
        </label>
        <div class="control">
          <textarea name="message"
                    id="input_message"
                    class="textarea custom-textarea"
                    rows="5"
                    maxlength="2000"
                    placeholder="Sampaikan kronologi secara rinci, misalnya:&#10;- Tanggal dan waktu kejadian&#10;- Lokasi kantor cabang / unit kerja&#10;- Pesan kesalahan (error) jika ada&#10;- Bukti persetujuan dari atasan langsung jika relevan..."></textarea>
        </div>
        <div class="level is-mobile mt-1">
          <div class="level-left">
            <span class="is-size-7 has-text-grey">
              <i class="fa-solid fa-lightbulb text-warning mr-1"></i> Semakin detail penjelasan Anda, semakin cepat HC memproses tiket ini.
            </span>
          </div>
          <div class="level-right">
            <span class="is-size-7 has-text-grey" id="messageCharCount">0 / 2000</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Validation Error Box -->
    <div class="notification is-danger is-light mb-4" id="step2ErrorBox" style="display: none;">
      <button class="delete" type="button" onclick="this.parentElement.style.display='none'"></button>
      <div id="step2ErrorMessage">Mohon lengkapi seluruh kolom bertanda bintang (*).</div>
    </div>

    <!-- Action Buttons -->
    <div class="panel-action-footer">
      <div class="action-footer-left">
        <button type="button" class="button is-light btn-step-prev" onclick="TicketWizard.prevStep(2)">
          <span class="icon is-small mr-2">
            <i class="fa-solid fa-arrow-left"></i>
          </span>
          <span>Kembali ke Topik</span>
        </button>
      </div>
      <div class="action-footer-right">
        <button type="button" class="button is-primary is-medium btn-step-next" onclick="TicketWizard.nextStep(2)">
          <span>Lanjut ke Lampiran &amp; Konfirmasi</span>
          <span class="icon is-small ml-2">
            <i class="fa-solid fa-arrow-right"></i>
          </span>
        </button>
      </div>
    </div>

  </div>
</div>
