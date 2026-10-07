<!-- User Login Popup Modal (HC Helpdesk / HC Eazy) -->
<style>
  .user-login-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.25s ease-in-out;
  }

  .user-login-modal-backdrop.show {
    opacity: 1;
  }

  .user-login-card {
    background: #ffffff;
    border-radius: 20px;
    max-width: 638px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transform: translateY(12px) scale(0.98);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: 'Montserrat', sans-serif;
  }

  .user-login-modal-backdrop.show .user-login-card {
    transform: translateY(0) scale(1);
  }

  /* Card Header */
  .user-login-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 24px 28px 18px 28px;
    border-bottom: 1px solid #F1F5F9;
    position: relative;
  }

  .user-login-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .user-login-brand-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #0F2B5B;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 8px 16px -4px rgba(15, 43, 91, 0.35);
  }

  .user-login-brand-icon svg,
  .user-login-brand-icon img {
    width: 26px;
    height: 26px;
    object-fit: contain;
  }

  .user-login-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .user-login-title {
    font-size: 20px;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
    line-height: 1.25;
  }

  .user-login-pill {
    display: inline-flex;
    align-items: center;
    background: #FEE7C8;
    color: #B45309;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    letter-spacing: 0.3px;
  }

  .user-login-subtitle {
    margin: 4px 0 0 0;
    font-size: 13px;
    color: #64748B;
    font-weight: 500;
    line-height: 1.4;
  }

  .user-login-close-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #E2E8F0;
    background: #ffffff;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  .user-login-close-btn:hover {
    background: #F8FAFC;
    color: #0F172A;
    border-color: #CBD5E1;
    transform: rotate(90deg);
  }

  /* Body */
  .user-login-body {
    padding: 24px 28px 28px 28px;
  }

  /* HC Eazy Info Banner */
  .hceazy-login-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px 16px;
    margin-bottom: 24px;
    background: #FAFAFC;
    border-radius: 12px;
    border: 1px solid #EDF2F7;
  }

  .hceazy-logo-img {
    height: 38px;
    width: auto;
    object-fit: contain;
  }

  .hceazy-banner-text {
    font-size: 14px;
    font-weight: 700;
    color: #1E293B;
  }

  /* Form Elements */
  .login-field-group {
    margin-bottom: 20px;
  }

  .login-field-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 8px;
  }

  .login-field-label {
    font-size: 13px;
    font-weight: 700;
    color: #1E293B;
  }

  .login-field-required {
    font-size: 11px;
    font-weight: 500;
    color: #94A3B8;
  }

  .login-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }

  .login-input-icon {
    position: absolute;
    left: 14px;
    color: #64748B;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
  }

  .login-input-icon svg,
  .login-input-icon img {
    width: 18px;
    height: 18px;
    object-fit: contain;
  }

  .user-login-input {
    width: 100%;
    height: 48px;
    padding: 0 44px 0 44px;
    border: 1.5px solid #E2E8F0;
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    color: #0F172A;
    background: #FFFFFF;
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .user-login-input::placeholder {
    color: #94A3B8;
    font-weight: 400;
  }

  .user-login-input:focus {
    outline: none;
    border-color: #0F2B5B;
    box-shadow: 0 0 0 3px rgba(15, 43, 91, 0.12);
  }

  .login-password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #64748B;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-password-toggle:hover {
    color: #0F2B5B;
  }

  /* Submit Button */
  .btn-submit-user-login {
    width: 100%;
    height: 50px;
    background: #0F2B5B;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
    margin-bottom: 20px;
    transition: all 0.25s ease;
    box-shadow: 0 6px 16px -2px rgba(15, 43, 91, 0.35);
  }

  .btn-submit-user-login:hover:not(:disabled) {
    background: #0b1f42;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px -2px rgba(15, 43, 91, 0.45);
  }

  .btn-submit-user-login:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .btn-submit-user-login svg {
    width: 18px;
    height: 18px;
    transition: transform 0.2s ease;
  }

  .btn-submit-user-login:hover svg {
    transform: translateX(3px);
  }

  /* Bottom Help Notice Box */
  .login-notice-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }

  .login-notice-icon {
    color: #0F2B5B;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .login-notice-text {
    font-size: 12.5px;
    color: #475569;
    line-height: 1.5;
    margin: 0;
  }

  .login-notice-link {
    color: #0284C7;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: color 0.15s ease;
  }

  .login-notice-link:hover {
    color: #0369A1;
    text-decoration: underline;
  }

  /* Inline Error Banner */
  .user-login-alert {
    display: none;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 16px;
    background: #FEF2F2;
    color: #991B1B;
    border: 1px solid #FCA5A5;
  }

  .user-login-spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #ffffff;
    animation: userSpin 0.8s linear infinite;
  }

  @keyframes userSpin {
    to {
      transform: rotate(360deg);
    }
  }

  /* =========================================================================
     RESPONSIVE BREAKPOINTS (Mobile & Small Screens)
     Best Practice: Clean, Compact, Zero-Scroll, Touch-Friendly Bottom-Sheet
     ========================================================================= */
  @media (max-width: 640px) {
    .user-login-modal-backdrop {
      align-items: flex-end;
      /* Modern bottom-sheet style di mobile */
      padding: 0;
    }

    .user-login-card {
      max-width: 100%;
      width: 100%;
      max-height: 92dvh;
      border-radius: 20px 20px 0 0;
      display: flex;
      flex-direction: column;
      transform: translateY(100%);
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .user-login-modal-backdrop.show .user-login-card {
      transform: translateY(0);
    }

    /* Header lebih ramping & hemat vertikal */
    .user-login-header {
      padding: 12px 16px;
      flex-shrink: 0;
      align-items: center;
    }

    .user-login-header-left {
      gap: 12px;
      flex: 1;
      min-width: 0;
    }

    .user-login-brand-icon {
      width: 36px;
      height: 36px;
      border-radius: 9px;
      flex-shrink: 0;
    }

    .user-login-brand-icon img,
    .user-login-brand-icon svg {
      width: 20px;
      height: 20px;
    }

    /* Pastikan Judul & Badge Portal Karyawan selalu 1 Baris Sejajar */
    .user-login-title-row {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: nowrap;
    }

    .user-login-title {
      font-size: 15px;
      white-space: nowrap;
      line-height: 1.2;
    }

    .user-login-pill {
      font-size: 10px;
      padding: 2px 7px;
      white-space: nowrap;
      flex-shrink: 0;
      line-height: 1.2;
    }

    .user-login-subtitle {
      font-size: 11px;
      margin-top: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 240px;
      line-height: 1.3;
    }

    .user-login-close-btn {
      width: 30px;
      height: 30px;
      margin-left: 8px;
    }

    /* Body dengan scroll aman jika keyboard virtual aktif */
    .user-login-body {
      padding: 14px 18px 22px 18px;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      flex: 1;
    }

    /* Banner HC Eazy dirampingkan */
    .hceazy-login-banner {
      padding: 8px 12px;
      margin-bottom: 14px;
      gap: 10px;
    }

    .hceazy-logo-img {
      height: 26px;
    }

    .hceazy-banner-text {
      font-size: 12.5px;
    }

    /* Form Fields lebih padat & proporsional */
    .login-field-group {
      margin-bottom: 12px;
    }

    .login-field-label {
      font-size: 12px;
    }

    .user-login-input {
      height: 44px;
      font-size: 13.5px;
      padding: 0 40px 0 38px;
    }

    .login-input-icon {
      left: 12px;
    }

    .login-input-icon svg,
    .login-input-icon img {
      width: 16px;
      height: 16px;
      object-fit: contain;
    }

    /* Tombol Submit */
    .btn-submit-user-login {
      height: 46px;
      font-size: 13.5px;
      margin-top: 6px;
      margin-bottom: 12px;
      border-radius: 10px;
    }

    /* Notice callout */
    .login-notice-box {
      padding: 10px 12px;
      gap: 10px;
      border-radius: 10px;
    }

    .login-notice-text {
      font-size: 11px;
      line-height: 1.45;
    }
  }

  /* =========================================================================
     SAAT KEYBOARD VIRTUAL HP MUNCUL (.keyboard-open atau max-height < 540px)
     Best Practice: 
     - Badge "Portal Karyawan" pindah ke bawah judul "Masuk HC Helpdesk"
     - Hilangkan banner dekoratif agar input & tombol submit muat presisi
     - Padding header & body dirampingkan sehingga modal tetap rapi
     ========================================================================= */
  .user-login-modal-backdrop.keyboard-open .user-login-card,
  @media (max-height: 540px) {
    .user-login-modal-backdrop {
      align-items: flex-end;
      padding: 0;
    }

    .user-login-card {
      max-height: 98dvh;
      border-radius: 18px 18px 0 0;
    }

    .user-login-header {
      padding: 10px 16px 8px 16px;
      align-items: flex-start;
    }

    .user-login-brand-icon {
      width: 34px;
      height: 34px;
      border-radius: 8px;
    }

    .user-login-brand-icon img,
    .user-login-brand-icon svg {
      width: 18px;
      height: 18px;
    }

    /* Portal Karyawan PINDAH KE BAWAH tulisan Masuk HC Helpdesk */
    .user-login-title-row {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 3px;
    }

    .user-login-title {
      font-size: 14px;
      line-height: 1.2;
    }

    .user-login-pill {
      font-size: 9.5px;
      padding: 1.5px 6px;
      margin-top: 1px;
    }

    /* Sembunyikan elemen non-kritis agar form leluasa */
    .user-login-subtitle,
    .hceazy-login-banner,
    .login-notice-box {
      display: none;
    }

    .user-login-body {
      padding: 10px 16px 14px 16px;
    }

    .login-field-group {
      margin-bottom: 8px;
    }

    .login-field-header {
      margin-bottom: 4px;
    }

    .login-field-label {
      font-size: 11.5px;
    }

    .user-login-input {
      height: 40px;
      font-size: 13px;
    }

    .btn-submit-user-login {
      height: 42px;
      font-size: 13px;
      margin-top: 6px;
      margin-bottom: 6px;
    }
  }
</style>

<div id="userLoginModal" class="user-login-modal-backdrop" onclick="handleUserLoginBackdropClick(event)">
  <div class="user-login-card">

    <!-- Header -->
    <div class="user-login-header">
      <div class="user-login-header-left">
        <div class="user-login-brand-icon">
          <img src="<?= base_url('assets/icons/helpdesk.svg') ?>" alt="HC Helpdesk Icon">
        </div>
        <div>
          <div class="user-login-title-row">
            <h2 class="user-login-title">Masuk HC Helpdesk</h2>
            <span class="user-login-pill">Portal Karyawan</span>
          </div>
          <p class="user-login-subtitle">Pelaporan & Permohonan Karyawan</p>
        </div>
      </div>
      <button type="button" class="user-login-close-btn" onclick="closeUserLoginModal()" aria-label="Tutup">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Body -->
    <div class="user-login-body">

      <!-- HC Eazy Identity Sub-banner -->
      <div class="hceazy-login-banner">
        <img src="<?= base_url('assets/images/hceazy.png') ?>" alt="HC Eazy Logo" class="hceazy-logo-img">
        <span class="hceazy-banner-text">Masuk dengan menggunakan akun HC Eazy</span>
      </div>

      <!-- Alert Error -->
      <div id="userLoginAlert" class="user-login-alert"></div>

      <!-- Login Form -->
      <form id="userLoginForm" onsubmit="handleUserLoginSubmit(event)">
        <!-- Username (NIP) -->
        <div class="login-field-group">
          <div class="login-field-header">
            <label for="userLoginNip" class="login-field-label">Username (NIP)</label>
            <span class="login-field-required">Wajib diisi</span>
          </div>
          <div class="login-input-wrapper">
            <span class="login-input-icon">
              <img src="<?= base_url('assets/icons/keycard.svg') ?>" alt="NIP Icon">
            </span>
            <input type="text" id="userLoginNip" name="username" class="user-login-input" placeholder="contoh: 00011234"
              autocomplete="username" required>
          </div>
        </div>

        <!-- Kata Sandi -->
        <div class="login-field-group">
          <div class="login-field-header">
            <label for="userLoginPassword" class="login-field-label">Kata Sandi</label>
            <span class="login-field-required">Wajib diisi</span>
          </div>
          <div class="login-input-wrapper">
            <span class="login-input-icon">
              <img src="<?= base_url('assets/icons/keypass.svg') ?>" alt="Password Icon">
            </span>
            <input type="password" id="userLoginPassword" name="password" class="user-login-input"
              placeholder="Masukkan kata sandi HC Eazy Anda" autocomplete="current-password" required>
            <button type="button" class="login-password-toggle" onclick="toggleUserPasswordVisibility()"
              aria-label="Toggle Password Visibility">
              <i id="userPasswordEyeIcon" class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="btnUserLoginSubmit" class="btn-submit-user-login">
          <span id="userLoginBtnText">Masuk ke Portal HC Helpdesk Karyawan</span>
          <svg id="userLoginBtnArrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
          <span id="userLoginBtnSpinner" class="user-login-spinner" style="display:none;"></span>
        </button>

        <!-- Reset Password Notice -->
        <div class="login-notice-box">
          <div class="login-notice-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <p class="login-notice-text">
            Kendala akun HC Eazy terkunci, lupa Password, atau tidak menerima OTP? Anda dapat langsung mengakses
            formulir permohonan tanpa login.
            <a href="<?= base_url('pusat-bantuan') ?>" class="login-notice-link">Formulir Reset Mandiri &rarr;</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openUserLoginModal() {
    var modal = document.getElementById('userLoginModal');
    var form = document.getElementById('userLoginForm');
    var alertBox = document.getElementById('userLoginAlert');

    if (alertBox) {
      alertBox.style.display = 'none';
      alertBox.textContent = '';
    }

    if (modal) {
      modal.style.display = 'flex';
      setTimeout(function () {
        modal.classList.add('show');
        var nipInput = document.getElementById('userLoginNip');
        if (nipInput) nipInput.focus();
      }, 10);
      document.body.style.overflow = 'hidden';
    }
  }

  function closeUserLoginModal() {
    var modal = document.getElementById('userLoginModal');
    if (modal) {
      modal.classList.remove('show');
      setTimeout(function () {
        modal.style.display = 'none';
        document.body.style.overflow = '';
      }, 250);
    }
  }

  function handleUserLoginBackdropClick(e) {
    if (e.target && e.target.id === 'userLoginModal') {
      closeUserLoginModal();
    }
  }

  function toggleUserPasswordVisibility() {
    var pwdInput = document.getElementById('userLoginPassword');
    var eyeIcon = document.getElementById('userPasswordEyeIcon');
    if (!pwdInput) return;

    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      if (eyeIcon) {
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    } else {
      pwdInput.type = 'password';
      if (eyeIcon) {
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  }

  function handleUserLoginSubmit(e) {
    e.preventDefault();
    var form = document.getElementById('userLoginForm');
    var btn = document.getElementById('btnUserLoginSubmit');
    var btnText = document.getElementById('userLoginBtnText');
    var btnArrow = document.getElementById('userLoginBtnArrow');
    var btnSpinner = document.getElementById('userLoginBtnSpinner');
    var alertBox = document.getElementById('userLoginAlert');

    var nip = (document.getElementById('userLoginNip').value || '').trim();
    var password = document.getElementById('userLoginPassword').value || '';

    if (!nip || !password) {
      if (alertBox) {
        alertBox.textContent = 'Harap masukkan Username (NIP) dan kata sandi Anda.';
        alertBox.style.display = 'block';
      }
      return;
    }

    // Set loading
    btn.disabled = true;
    if (btnText) btnText.textContent = 'Memverifikasi...';
    if (btnArrow) btnArrow.style.display = 'none';
    if (btnSpinner) btnSpinner.style.display = 'inline-block';
    if (alertBox) alertBox.style.display = 'none';

    var formData = new URLSearchParams();
    formData.append('username', nip);
    formData.append('password', password);

    fetch('<?= base_url('login') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData.toString()
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { status: res.status, ok: res.ok, data: data };
        });
      })
      .then(function (result) {
        if (result.ok && result.data.status === 'success') {
          if (btnText) btnText.textContent = 'Berhasil Masuk!';
          if (result.data.access_token) {
            try {
              localStorage.setItem('user_access_token', result.data.access_token);
              if (result.data.refresh_token) {
                localStorage.setItem('user_refresh_token', result.data.refresh_token);
              }
              if (result.data.app_jwt) {
                localStorage.setItem('user_jwt', result.data.app_jwt);
              }
            } catch (e) { }
          }
          setTimeout(function () {
            if (result.data.redirect) {
              window.location.href = result.data.redirect;
            } else {
              window.location.reload();
            }
          }, 500);
        } else {
          btn.disabled = false;
          if (btnText) btnText.textContent = 'Masuk ke Portal HC Helpdesk Karyawan';
          if (btnArrow) btnArrow.style.display = 'inline-block';
          if (btnSpinner) btnSpinner.style.display = 'none';
          if (alertBox) {
            alertBox.textContent = result.data.message || 'Username atau Kata Sandi salah.';
            alertBox.style.display = 'block';
          }
        }
      })
      .catch(function (err) {
        btn.disabled = false;
        if (btnText) btnText.textContent = 'Masuk ke Portal HC Helpdesk Karyawan';
        if (btnArrow) btnArrow.style.display = 'inline-block';
        if (btnSpinner) btnSpinner.style.display = 'none';
        if (alertBox) {
          alertBox.textContent = 'Terjadi kesalahan sistem / jaringan. Silakan coba kembali.';
          alertBox.style.display = 'block';
        }
      });
  }

  // Detect Virtual Keyboard on Mobile via VisualViewport & Focus events
  (function () {
    var modalBackdrop = document.getElementById('userLoginModal');
    var inputs = [document.getElementById('userLoginNip'), document.getElementById('userLoginPassword')];

    function setKeyboardState(isOpen) {
      if (!modalBackdrop) return;
      if (isOpen) {
        modalBackdrop.classList.add('keyboard-open');
      } else {
        modalBackdrop.classList.remove('keyboard-open');
      }
    }

    if (window.visualViewport) {
      var initialHeight = window.visualViewport.height;
      window.visualViewport.addEventListener('resize', function () {
        if (window.innerWidth <= 640) {
          // If viewport height drops significantly (> 120px), virtual keyboard is active
          var isKeyboardActive = (initialHeight - window.visualViewport.height) > 120;
          setKeyboardState(isKeyboardActive);
        }
      });
    }

    inputs.forEach(function (input) {
      if (!input) return;
      input.addEventListener('focus', function () {
        if (window.innerWidth <= 640) {
          setKeyboardState(true);
          setTimeout(function () {
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }, 150);
        }
      });

      input.addEventListener('blur', function () {
        setTimeout(function () {
          var activeEl = document.activeElement;
          if (!inputs.includes(activeEl)) {
            setKeyboardState(false);
          }
        }, 150);
      });
    });
  })();

  // Escape key closes modal
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var modal = document.getElementById('userLoginModal');
      if (modal && modal.style.display === 'flex') {
        closeUserLoginModal();
      }
    }
  });
</script>