<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HC Helpdesk</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/ticket_form.css') ?>">
  <style>
    .admin-login-btn {
      display: inline-flex;
      padding: 10px 20px;
      justify-content: center;
      align-items: center;
      gap: 8px;
      border-radius: 25px;
      background: #2940D3;
      color: white;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0px 2px 8px rgba(41, 64, 211, 0.25);
      transition: background 0.3s ease, box-shadow 0.3s ease;
      font-size: 13px;
      white-space: nowrap;
      flex-shrink: 0;
    }
    .admin-login-btn:hover {
      background: #1f30a6;
      text-decoration: none;
      color: white;
      box-shadow: 0px 4px 12px rgba(41, 64, 211, 0.35);
    }
    .d-none {
      display: none;
    }
    .loading-modal-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      z-index: 10000;
      display: none;
      align-items: center;
      justify-content: center;
    }
    .loading-modal {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #2940D3;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .loading-text {
      font-size: 16px;
      color: #333;
      margin-top: 10px;
    }
    .faq-modal-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.18);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .faq-modal {
      background: #fff;
      border-radius: 18px;
      padding: 32px;
      box-shadow: 0 2px 16px #aaa;
      max-width: 400px;
      width: 90%;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }
    #faq-section {
      border-radius: 16px;
      box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
      padding: 32px;
      background: #fff;
      width: 100%;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }
    #faq-section h2 {
      text-align: center;
      font-weight: 600;
      margin-bottom: 24px;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }
    .logo-section {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }
    .logo-section img {
      max-width: 580px;
      width: 100%;
      height: auto;
      display: block;
    }
    .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .file-input-wrapper input[type="file"] {
      position: absolute;
      left: -9999px;
    }
    .file-input-label {
      display: inline-block;
      background: #2940D3;
      color: #FFF;
      border-radius: 40px;
      padding: 6px 12px;
      font-weight: 600;
      border: none;
      font-size: 12px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-family: Montserrat, sans-serif;
      flex-shrink: 0;
    }
    .file-input-label:hover {
      background: #1f30a6;
    }
    .file-name-display {
      font-size: 12px;
      color: #666;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 1.4;
    }
  </style>
</head>
<body>
  <?php include(APPPATH . 'Views/components/invalid_confirm.php'); ?>

  <div class="container">
    <!-- Header -->
    <div class="header-section">
      <div class="header-text">
        <h3>HC Helpdesk</h3>
        <p>Get support and find answers to common question</p>
      </div>
      <div class="header-button">
        <a href="<?= base_url('login') ?>" class="admin-login-btn d-none">
          Admin Login
        </a>
      </div>
    </div>

    <!-- Toggle Navigation -->
    <div class="nav-toggle">
      <button class="active" id="btn-ticket">Submit Support Ticket</button>
      <button id="btn-faq">FAQ & Help</button>
    </div>

    <!-- Ticket Form -->
    <div id="ticket-section">
      <div class="card">
        <h5>Submit Support Ticket</h5>
        <form id="ticketForm" action="<?= base_url('ticket/store') ?>" method="post" enctype="multipart/form-data">
          
          <!-- Two Column Row -->
          <div class="form-group row-2col">
            <div>
              <label for="emp_name">Nama <span>*</span></label>
              <input type="text" name="emp_name" id="emp_name" class="form-control" placeholder="Masukkan Nama Lengkap Anda" required>
            </div>
            <div>
              <label for="emp_id">Nomor Induk Pegawai <span>*</span></label>
              <input type="text" name="emp_id" id="emp_id" class="form-control" placeholder="Masukkan NIP Anda" inputmode="numeric" required>
            </div>
          </div>

          <!-- Single Column Fields -->
          <div class="form-group">
            <label for="email">Email MTF <span>*</span></label>
            <input type="email" name="email" id="email" class="form-control" placeholder="masukkan email MTF, cth: john@mtf.co.id" required>
          </div>
          
          <div class="form-group">
            <label for="wa_no">No. Handphone / Whatsapp <span>*</span></label>
            <input type="text" name="wa_no" id="wa_no" class="form-control" placeholder="Masukkan No Handphone yang terdaftar pada whatsapp" inputmode="numeric" required>
          </div>
          
          <div class="form-group">
            <label for="req_type">Tipe Pengajuan <span>*</span></label>
            <select name="req_type" id="req_type" class="form-select" required>
              <option value="">Pilih tipe pengajuan</option>
              <?php if (!empty($requestTypes)): ?>
                <?php foreach ($requestTypes as $type): ?>
                  <option value="<?= esc($type['name']) ?>"><?= esc($type['name']) ?></option>
                <?php endforeach ?>
              <?php endif ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="subject">Subject <span>*</span></label>
            <input type="text" name="subject" id="subject" class="form-control" placeholder="Masukkan judul pengajuan ticket" required>
          </div>
          
          <div class="form-group">
            <label for="message">Deskripsi <span>*</span></label>
            <textarea name="message" id="message" class="form-control" placeholder="Deskripsikan pengajuan disertai dengan informasi yang lengkap"></textarea>
          </div>
          
          <div class="form-group">
            <label>Lampirkan File <span>*</span> <span class="note">(.jpg, .pdf, .docx) - MAX 5MB</span></label>
            <div class="file-input-wrapper">
              <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.pdf,.docx" required>
              <label for="attachment" class="file-input-label">Upload</label>
              <span class="file-name-display" id="fileNameDisplay"></span>
            </div>
          </div>
          
          <button type="button" class="btn-submit" id="confirmBtn">Submit Ticket</button>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi Ticket -->
    <div id="ticketConfirmModal" class="faq-modal-bg" style="display:none;">
      <div class="faq-modal">
        <div style="font-size:20px; font-weight:600; margin-bottom:12px;">Konfirmasi Data Ticket</div>
        <div style="font-size:15px; color:#444; margin-bottom:18px; text-align:left; width: 100%;">
          Apakah data yang anda isi telah benar?
          <ul style="margin-top:10px; margin-bottom:0; padding-left:18px;">
            <li>Nama: <span id="modalEmpName"></span></li>
            <li>NIP: <span id="modalEmpId"></span></li>
            <li>Email MTF: <span id="modalEmail"></span></li>
            <li>No Handphone: <span id="modalWaNo"></span></li>
            <li>Tipe Pengajuan: <span id="modalReqType"></span></li>
            <li>Subject: <span id="modalSubject"></span></li>
            <li>Message: <span id="modalMessage"></span></li>
          </ul>
        </div>
        <div style="display: flex; gap: 12px; justify-content: center; width: 100%; margin-top: 20px;">
          <button type="button" style="padding: 10px 24px; border-radius: 8px; border: 1px solid #ddd; background: #f5f5f5; cursor: pointer; font-weight: 600;" id="cancelTicketConfirm">Cek Lagi</button>
          <button type="button" style="padding: 10px 24px; border-radius: 8px; border: none; background: #22c55e; color: white; cursor: pointer; font-weight: 600;" id="submitTicketConfirm">Ya, data sudah benar</button>
        </div>
      </div>
    </div>

    <!-- Modal Success -->
    <div id="successConfirmModal" class="faq-modal-bg" style="display:none;">
      <div class="faq-modal" style="text-align: center;">
        <div style="font-size:22px; font-weight:700; margin-bottom:10px; color:#22c55e;">
          <svg width="32" height="32" fill="none" style="vertical-align:middle;margin-right:8px;">
            <circle cx="16" cy="16" r="16" fill="#22c55e"/>
            <path d="M10 16l4 4 8-8" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Ticket Berhasil Dikirim!
        </div>
        <div style="font-size:15px; color:#444; margin-bottom:18px;">
          Terima kasih, tiket Anda telah berhasil dikirim.<br>Halaman akan di-refresh...
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq-section" class="d-none">
      <h2>Frequently Asked Question</h2>
      <?php include(APPPATH . 'Views/list_faq.php'); ?>
    </div>

    <!-- Logo Section -->
    <div class="logo-section">
      <img src="<?= base_url('assets/images/logo-perwira.png') ?>" alt="logo-perwira">
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="loading-modal-bg">
      <div class="loading-modal">
        <div class="spinner"></div>
        <div class="loading-text">Memproses ticket Anda...</div>
        <div style="font-size: 13px; color: #999; margin-top: 10px;">Silakan tunggu sebentar</div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/ticket_form.js') ?>"></script>
  <script src="<?= base_url('assets/js/device-security-check.js') ?>"></script>
  <script>
    function showLoadingModal() {
      document.getElementById('loadingModal').style.display = 'flex';
    }

    function hideLoadingModal() {
      document.getElementById('loadingModal').style.display = 'none';
    }

    // Handle file input change
    document.getElementById('attachment').addEventListener('change', function(e) {
      const fileNameDisplay = document.getElementById('fileNameDisplay');
      if (this.files && this.files[0]) {
        fileNameDisplay.textContent = 'File dipilih: ' + this.files[0].name;
      } else {
        fileNameDisplay.textContent = '';
      }
    });
  </script>
</body>
</html>
