/**
 * Landing Page Module Script
 * Handles ticket tracking AJAX, carousel controls, and modal interactions.
 */

// Modal Controls
function showHotlineModal() {
  const modal = document.getElementById('hotlineModal');
  if (modal) modal.style.display = 'flex';
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.style.display = 'none';
}

// Window click to close modals when clicking on overlay backdrop
window.addEventListener('click', function (event) {
  if (event.target.classList.contains('landing-modal-overlay')) {
    event.target.style.display = 'none';
  }
});

// Handle Track Ticket Submit
function handleTrackTicket(e) {
  e.preventDefault();
  const ticketInput = document.getElementById('track_ticket_no');
  const emailInput = document.getElementById('track_email');
  const btn = document.getElementById('btnTrackSubmit');

  if (!ticketInput) return;
  const ticketNo = ticketInput.value.trim();
  const email = emailInput ? emailInput.value.trim() : '';

  if (!ticketNo) {
    alert('Harap masukkan nomor tiket.');
    return;
  }

  btn.disabled = true;
  const originalBtnHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Memeriksa status...</span>';

  // Obtain CSRF token if available
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-Requested-With': 'XMLHttpRequest'
  };
  if (csrfMeta) {
    headers['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
  }

  fetch(window.location.origin + '/track-ticket', {
    method: 'POST',
    headers: headers,
    body: new URLSearchParams({
      'ticket_no': ticketNo,
      'email': email
    })
  })
    .then(response => response.json())
    .then(result => {
      btn.disabled = false;
      btn.innerHTML = originalBtnHtml;

      if (result.status === 'success') {
        const t = result.data;
        const statusClass = 'status-' + (t.ticket_status ? t.ticket_status.toLowerCase().replace(/\s+/g, '_') : 'open');

        let content = `
          <div class="result-row">
            <span class="result-label">Nomor Tiket</span>
            <span class="result-val">#${t.id ? t.id.substring(0, 13) : '-'}...</span>
          </div>
          <div class="result-row">
            <span class="result-label">Status Tiket</span>
            <span class="result-val"><span class="status-badge ${statusClass}">${t.ticket_status || 'Open'}</span></span>
          </div>
          <div class="result-row">
            <span class="result-label">Kategori Layanan</span>
            <span class="result-val">${t.req_type || '-'}</span>
          </div>
          <div class="result-row">
            <span class="result-label">Subjek Kendala</span>
            <span class="result-val">${t.subject || '-'}</span>
          </div>
          <div class="result-row">
            <span class="result-label">Prioritas</span>
            <span class="result-val">${t.ticket_priority || 'Normal'}</span>
          </div>
          <div class="result-row">
            <span class="result-label">Waktu Pengajuan</span>
            <span class="result-val">${t.created_date || '-'}</span>
          </div>
          <div class="result-row">
            <span class="result-label">Estimasi Target SLA</span>
            <span class="result-val">${t.due_date || 'Dalam Antrean'}</span>
          </div>
        `;

        const resultBody = document.getElementById('trackResultBody');
        const modal = document.getElementById('trackResultModal');
        if (resultBody) resultBody.innerHTML = content;
        if (modal) modal.style.display = 'flex';
      } else {
        alert(result.message || 'Tiket tidak ditemukan. Periksa kembali nomor tiket Anda.');
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerHTML = originalBtnHtml;
      alert('Terjadi kesalahan saat memproses data. Silakan coba beberapa saat lagi.');
    });
}

// Carousel Navigation
function scrollCarousel(direction) {
  const carousel = document.getElementById('servicesCarousel');
  if (!carousel) return;
  const scrollAmount = 300 * direction;
  carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

function goToSlide(index) {
  const carousel = document.getElementById('servicesCarousel');
  if (!carousel) return;
  const cardWidth = carousel.scrollWidth / 3;
  carousel.scrollTo({ left: cardWidth * index, behavior: 'smooth' });
  updateDots(index);
}

function updateDots(activeIndex) {
  const dots = document.querySelectorAll('.carousel-dot');
  dots.forEach((dot, idx) => {
    dot.classList.toggle('active', idx === activeIndex);
  });
}

// Auto bind carousel scroll event
document.addEventListener('DOMContentLoaded', function () {
  const carousel = document.getElementById('servicesCarousel');
  if (carousel) {
    carousel.addEventListener('scroll', () => {
      const maxScroll = carousel.scrollWidth - carousel.clientWidth;
      if (maxScroll <= 0) return;
      const scrollPercentage = carousel.scrollLeft / maxScroll;
      let activeIdx = 0;
      if (scrollPercentage > 0.6) activeIdx = 2;
      else if (scrollPercentage > 0.25) activeIdx = 1;
      updateDots(activeIdx);
    });
  }
});
