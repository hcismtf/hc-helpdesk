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
      <form id="userLoginForm" onsubmit="handleUserLoginSubmit(event)" data-login-url="<?= base_url('login') ?>" data-csrf-token-name="<?= csrf_token() ?>">
        <!-- CSRF Token (Security Best Practice CI4) -->
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="userLoginCsrf">

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