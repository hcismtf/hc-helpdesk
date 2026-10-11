/**
 * Module: HC User Login Modal Handler
 * Clean Architecture, Smooth Bottom-Sheet & Zero-Glitch Mobile Virtual Keyboard
 */

function openUserLoginModal() {
  var modal = document.getElementById('userLoginModal');
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
  if (e && e.target && e.target.id === 'userLoginModal') {
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
  if (btn) btn.disabled = true;
  if (btnText) btnText.textContent = 'Memverifikasi...';
  if (btnArrow) btnArrow.style.display = 'none';
  if (btnSpinner) btnSpinner.style.display = 'inline-block';
  if (alertBox) alertBox.style.display = 'none';

  var loginUrl = (form && form.dataset.loginUrl) ? form.dataset.loginUrl : '/login';
  var csrfName = (form && form.dataset.csrfTokenName) ? form.dataset.csrfTokenName : 'csrf_test_name';
  var csrfInput = document.getElementById('userLoginCsrf');
  var csrfToken = csrfInput ? csrfInput.value : '';

  var formData = new URLSearchParams();
  formData.append('username', nip);
  formData.append('password', password);
  if (csrfToken) {
    formData.append(csrfName, csrfToken);
  }

  fetch(loginUrl, {
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
            // Simpan data users table ke localStorage dengan nama 'userRef' (Terenkripsi)
            if (result.data.user && typeof CryptoHelper !== 'undefined') {
              CryptoHelper.setUserRef(result.data.user);
            }
          } catch (err) { }
        }
        setTimeout(function () {
          if (result.data.redirect) {
            window.location.href = result.data.redirect;
          } else {
            window.location.reload();
          }
        }, 500);
      } else {
        if (btn) btn.disabled = false;
        if (btnText) btnText.textContent = 'Masuk ke Portal HC Helpdesk Karyawan';
        if (btnArrow) btnArrow.style.display = 'inline-block';
        if (btnSpinner) btnSpinner.style.display = 'none';
        if (alertBox) {
          alertBox.textContent = result.data.message || 'Username atau Kata Sandi salah.';
          alertBox.style.display = 'block';
        }
      }
    })
    .catch(function () {
      if (btn) btn.disabled = false;
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

// Namespace alias for consistency
window.UserLoginModal = {
  open: openUserLoginModal,
  close: closeUserLoginModal,
  togglePassword: toggleUserPasswordVisibility,
  handleSubmit: handleUserLoginSubmit
};