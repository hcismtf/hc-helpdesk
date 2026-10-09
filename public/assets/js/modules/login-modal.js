/**
 * Module: HC User Login Modal Handler
 */
const UserLoginModal = (() => {
  const modalEl = document.getElementById('userLoginModal');
  const formEl = document.getElementById('userLoginForm');
  const alertEl = document.getElementById('userLoginAlert');
  const alertMsgEl = document.getElementById('userLoginAlertMessage');
  const submitBtn = document.getElementById('btnUserLoginSubmit');
  const nipInput = document.getElementById('userLoginNip');
  const pwdInput = document.getElementById('userLoginPassword');
  const eyeIcon = document.getElementById('userPasswordEyeIcon');

  function open() {
    if (!modalEl) return;
    hideAlert();
    modalEl.classList.add('is-active');
    document.documentElement.classList.add('is-clipped'); // Native Bulma body scroll lock
    setTimeout(() => { if (nipInput) nipInput.focus(); }, 100);
  }

  function close() {
    if (!modalEl) return;
    modalEl.classList.remove('is-active');
    document.documentElement.classList.remove('is-clipped');
  }

  function showAlert(msg) {
    if (!alertEl || !alertMsgEl) return;
    alertMsgEl.textContent = msg;
    alertEl.classList.remove('is-hidden');
  }

  function hideAlert() {
    if (alertEl) alertEl.classList.add('is-hidden');
  }

  function togglePassword() {
    if (!pwdInput || !eyeIcon) return;
    const isPass = pwdInput.type === 'password';
    pwdInput.type = isPass ? 'text' : 'password';
    eyeIcon.className = isPass ? 'fas fa-eye-slash has-text-grey' : 'fas fa-eye has-text-grey';
  }

  function handleSubmit(e) {
    e.preventDefault();
    hideAlert();

    const username = (nipInput.value || '').trim();
    const password = pwdInput.value || '';
    const csrfToken = document.getElementById('userLoginCsrf')?.value;

    if (!username || !password) {
      showAlert('Harap masukkan Username (NIP) dan kata sandi Anda.');
      return;
    }

    submitBtn.classList.add('is-loading');
    submitBtn.disabled = true;

    const bodyData = new URLSearchParams({ username, password });
    if (csrfToken) bodyData.append('<?= csrf_token() ?>', csrfToken);

    fetch(loginUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: bodyData.toString()
      })
      .then(res => res.json())
      .then(res => {
        submitBtn.classList.remove('is-loading');
        submitBtn.disabled = false;

        if (res.status === 'success') {
          if (res.access_token) {
            try {
              localStorage.setItem('user_access_token', res.access_token);
              if (res.refresh_token) localStorage.setItem('user_refresh_token', res.refresh_token);
            } catch (err) {}
          }
          window.location.href = res.redirect || window.location.href;
        } else {
          showAlert(res.message || 'Username atau kata sandi tidak valid.');
        }
      })
      .catch(() => {
        submitBtn.classList.remove('is-loading');
        submitBtn.disabled = false;
        showAlert('Terjadi kesalahan jaringan/sistem. Silakan coba beberapa saat lagi.');
      });
  }

  function initKeyboardWatchers() {
    if (!window.visualViewport) return;
    const initialH = window.visualViewport.height;

    window.visualViewport.addEventListener('resize', () => {
      if (window.innerWidth <= 640 && modalEl) {
        const isKeyboard = (initialH - window.visualViewport.height) > 120;
        modalEl.classList.toggle('keyboard-open', isKeyboard);
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modalEl?.classList.contains('is-active')) {
        close();
      }
    });
  }

  initKeyboardWatchers();

  return { open, close, togglePassword, handleSubmit };
})();

window.openUserLoginModal = UserLoginModal.open;
window.closeUserLoginModal = UserLoginModal.close;