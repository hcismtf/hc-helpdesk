const btnTicket = document.getElementById('btn-ticket');
const btnFaq = document.getElementById('btn-faq');
const ticketSection = document.getElementById('ticket-section');
const faqSection = document.getElementById('faq-section');

btnTicket.addEventListener('click', () => {
  btnTicket.classList.add('active');
  btnFaq.classList.remove('active');
  ticketSection.classList.remove('d-none');
  faqSection.classList.add('d-none');
});

btnFaq.addEventListener('click', () => {
  btnFaq.classList.add('active');
  btnTicket.classList.remove('active');
  faqSection.classList.remove('d-none');
  ticketSection.classList.add('d-none');
});

// Block input NIP (only numbers)
document.getElementById('emp_id').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

// Block input No HP (only numbers)
document.getElementById('wa_no').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

// Real-time email format validation
document.getElementById('email').addEventListener('input', function(e) {
  const emailValue = this.value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  // Remove previous error styling
  this.style.borderColor = '';
  
  // Show error if has value but invalid format
  if (emailValue.length > 0 && !emailRegex.test(emailValue)) {
    this.style.borderColor = '#e74c3c';
  } else if (emailValue.length > 0 && emailRegex.test(emailValue)) {
    this.style.borderColor = '#22c55e';
  }
});

document.getElementById('confirmBtn').onclick = function(e) {
  //  Validasi form 
  const requiredFields = [
    'emp_name',
    'emp_id',
    'email',
    'wa_no',
    'req_type',
    'subject'
  ];

  let isValid = true;
  requiredFields.forEach(id => {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      isValid = false;
    }
  });

  if (!isValid) {
    showGlobalInvalid('Harap lengkapi semua field yang wajib diisi sebelum melanjutkan.');
    return; // stop di sini
  }

  // Validasi ukuran file (max 5MB)
  const fileInput = document.getElementById('attachment');
  if (fileInput.files.length > 0) {
    const file = fileInput.files[0];
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    
    if (file.size > maxFileSize) {
      showGlobalInvalid('Ukuran file lampiran tidak boleh melebihi 5MB. File Anda: ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB');
      return;
    }
  }

  // ✅ Kalau valid, isi data ke modal konfirmasi
  document.getElementById('modalEmpName').textContent = document.getElementById('emp_name').value;
  document.getElementById('modalEmpId').textContent = document.getElementById('emp_id').value;
  document.getElementById('modalEmail').textContent = document.getElementById('email').value;
  document.getElementById('modalWaNo').textContent = document.getElementById('wa_no').value;
  document.getElementById('modalReqType').textContent = document.getElementById('req_type').value;
  document.getElementById('modalSubject').textContent = document.getElementById('subject').value;
  document.getElementById('modalMessage').textContent = document.getElementById('message').value;

  document.getElementById('ticketConfirmModal').style.display = 'flex';
};

// Tutup modal jika klik "Cek Lagi"
document.getElementById('cancelTicketConfirm').onclick = function() {
  document.getElementById('ticketConfirmModal').style.display = 'none';
};

// Submit form via AJAX jika klik "Ya, data sudah benar"
document.getElementById('submitTicketConfirm').onclick = function() {
  var form = document.getElementById('ticketForm');
  var formData = new FormData(form);

  // Close confirmation modal
  document.getElementById('ticketConfirmModal').style.display = 'none';
  
  // Show loading modal
  showLoadingModal();

  fetch(form.action, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (response.ok) {
      // Hide loading modal langsung
      hideLoadingModal();
      document.getElementById('successConfirmModal').style.display = 'flex';
      setTimeout(() => window.location.href = "/HC-Helpdesk/public/ticket/create", 2000);
    } else {
      throw new Error('Server error');
    }
  })
  .catch(error => {
    hideLoadingModal();
    showGlobalInvalid('Gagal mengirim ticket. Silakan coba lagi.');
  });
};

// Tutup modal dengan tombol ESC
document.addEventListener('keydown', function(e){
  if(e.key === "Escape") {
    document.getElementById('ticketConfirmModal').style.display = 'none';
    document.getElementById('successConfirmModal').style.display = 'none';
    closeGlobalInvalid();
  }
});
