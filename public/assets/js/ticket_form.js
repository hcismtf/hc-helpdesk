const btnTicket = document.getElementById('btn-ticket');
const btnFaq = document.getElementById('btn-faq');
const ticketSection = document.getElementById('ticket-section');
const faqSection = document.getElementById('faq-section');
const faqTitle = document.getElementById('faq-title'); 

btnTicket.addEventListener('click', () => {
    btnTicket.classList.add('active');
    btnFaq.classList.remove('active');
    ticketSection.classList.remove('d-none');
    faqSection.classList.add('d-none');
    faqTitle.style.display = 'none';
});

btnFaq.addEventListener('click', () => {
    btnFaq.classList.add('active');
    btnTicket.classList.remove('active');
    faqSection.classList.remove('d-none');
    ticketSection.classList.add('d-none');
    faqTitle.style.display = 'block';
});
document.getElementById('confirmBtn').onclick = function(e) {
  // Ambil data dari form
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

  fetch(form.action, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // Modal konfirmasi hilang, modal sukses tampil
    document.getElementById('ticketConfirmModal').style.display = 'none';
    document.getElementById('successConfirmModal').style.display = 'flex';
    // Setelah 2 detik, reload halaman
    setTimeout(function(){
      window.location.href = "/";
    }, 2000);
  })
  .catch(error => {
    alert('Gagal submit ticket!');
    document.getElementById('ticketConfirmModal').style.display = 'none';
  });
};

// Tutup modal dengan tombol ESC
document.addEventListener('keydown', function(e){
  if(e.key === "Escape") {
    document.getElementById('ticketConfirmModal').style.display = 'none';
    document.getElementById('successConfirmModal').style.display = 'none';
  }
});