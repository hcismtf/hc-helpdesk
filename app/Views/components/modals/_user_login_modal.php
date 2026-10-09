<!-- Modal Component: HC Eazy User Login -->
<div id="userLoginModal" class="modal user-login-modal" role="dialog" aria-modal="true"
  aria-labelledby="userLoginTitle">
  <div class="modal-background" onclick="UserLoginModal.close()"></div>

  <div class="modal-card user-login-card">
    <!-- Header -->
    <header class="modal-card-head user-login-header">
      <div class="is-flex is-align-items-center">
        <div class="user-login-brand-icon mr-3">
          <img src="<?= base_url('assets/icons/helpdesk.svg') ?>" alt="HC Helpdesk">
        </div>
        <div>
          <div class="user-login-title-row">
            <h2 id="userLoginTitle" class="user-login-title">Masuk HC Helpdesk</h2>
            <span class="tag is-warning is-light is-rounded has-text-weight-bold user-login-pill">Portal Karyawan</span>
          </div>
          <p class="is-size-7 has-text-grey user-login-subtitle">Pelaporan & Permohonan Karyawan</p>
        </div>
      </div>
      <button type="button" class="delete is-medium ml-auto" aria-label="close"
        onclick="UserLoginModal.close()"></button>
    </header>

    <!-- Body -->
    <section class="modal-card-body user-login-body">
      <!-- HC Eazy Banner -->
      <div class="hceazy-login-banner mb-4">
        <img src="<?= base_url('assets/images/hceazy.png') ?>" alt="HC Eazy" class="hceazy-logo-img">
        <span class="has-text-weight-bold is-size-6 has-text-dark">Masuk dengan menggunakan akun HC Eazy</span>
      </div>

      <!-- Alert Notification -->
      <div id="userLoginAlert" class="notification is-danger is-light is-hidden py-3 px-4 mb-4">
        <button class="delete is-small" onclick="this.parentElement.classList.add('is-hidden')"></button>
        <span id="userLoginAlertMessage"></span>
      </div>

      <!-- Form -->
      <form id="userLoginForm" data-login-url="<?= base_url('login') ?>" data-csrf-token-name="<?= csrf_token() ?>"
        onsubmit="UserLoginModal.handleSubmit(event)">
        <!-- CSRF Token (Security Best Practice CI4) -->
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="userLoginCsrf">

        <!-- Username / NIP -->
        <div class="field mb-4">
          <div class="is-flex is-justify-content-space-between is-align-items-baseline mb-1">
            <label for="userLoginNip" class="label is-small mb-0">Username (NIP)</label>
            <span class="has-text-grey-light is-size-7">Wajib diisi</span>
          </div>
          <div class="control has-icons-left">
            <input type="text" id="userLoginNip" name="username" class="input" placeholder="contoh: 00011234"
              autocomplete="username" required>
            <span class="icon is-small is-left">
              <img src="<?= base_url('assets/icons/keycard.svg') ?>" alt="NIP" style="width: 18px;">
            </span>
          </div>
        </div>

        <!-- Password -->
        <div class="field mb-4">
          <div class="is-flex is-justify-content-space-between is-align-items-baseline mb-1">
            <label for="userLoginPassword" class="label is-small mb-0">Kata Sandi</label>
            <span class="has-text-grey-light is-size-7">Wajib diisi</span>
          </div>
          <div class="control has-icons-left has-icons-right">
            <input type="password" id="userLoginPassword" name="password" class="input"
              placeholder="Masukkan kata sandi HC Eazy Anda" autocomplete="current-password" required>
            <span class="icon is-small is-left">
              <img src="<?= base_url('assets/icons/keypass.svg') ?>" alt="Password" style="width: 18px;">
            </span>
            <span class="icon is-small is-right is-clickable" onclick="UserLoginModal.togglePassword()"
              title="Lihat password">
              <i id="userPasswordEyeIcon" class="fas fa-eye has-text-grey"></i>
            </span>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="btnUserLoginSubmit" class="button is-fullwidth btn-submit-user-login mt-4 mb-4">
          <span id="userLoginBtnText">Masuk ke Portal HC Helpdesk Karyawan</span>
          <span class="icon is-small ml-2" id="userLoginBtnArrow">
            <i class="fas fa-arrow-right"></i>
          </span>
        </button>

        <!-- Help Notice Callout -->
        <div class="login-notice-box">
          <span class="icon is-small mr-2 has-text-link mt-1">
            <i class="fas fa-info-circle"></i>
          </span>
          <p class="is-size-7 has-text-grey-dark mb-0">
            Kendala akun HC Eazy terkunci, lupa Password, atau tidak menerima OTP?
            <a href="<?= base_url('pusat-bantuan') ?>" class="has-text-link has-text-weight-semibold">Formulir Reset
              Mandiri &rarr;</a>
          </p>
        </div>
      </form>
    </section>
  </div>
</div>