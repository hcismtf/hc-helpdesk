<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HC Helpdesk - Portal Bantuan Divisi Human Capital PT Mandiri Tunas Finance</title>

  <!-- Google Fonts: Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Landing CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=' . time()) ?>">
</head>

<body>

  <!-- ========================================================================
       1. TOP NAVBAR (Global Component)
       ======================================================================== -->
  <?= view('components/public_navbar', ['activePage' => 'home']) ?>

  <!-- ========================================================================
       2. MAIN CONTENT
       ======================================================================== -->
  <main class="main-wrapper">

    <!-- Top Grid: Left Hero & Actions, Right Track Ticket -->
    <div class="top-grid">

      <!-- LEFT COLUMN -->
      <div class="hero-column">

        <!-- Hero Banner Card -->
        <section class="hero-banner-card">
          <div class="hero-pill-badge">
            Sistem Layanan Mandiri Karyawan PT Mandiri Tunas Finance
          </div>
          <h1 class="hero-headline">
            Selamat Datang di portal <span class="highlight-brand">HC Helpdesk</span>
          </h1>
          <p class="hero-description">
            Satu portal terpadu untuk bantuan layanan kepegawaian, pengajuan tiket kendala, panduan SOP, dan resolusi
            akun HC Eazy.
          </p>

          <!-- Primary Call-to-Action (Fast Ticket Creation) -->
          <div class="hero-cta-group">
            <a href="<?= base_url('ticket/create') ?>" class="btn-hero-create-ticket">
              <span class="btn-hero-icon-box">
                <i class="fas fa-plus"></i>
              </span>
              <span>Buat Tiket Bantuan</span>
              <i class="fas fa-arrow-right btn-hero-arrow"></i>
            </a>
          </div>
        </section>

        <!-- 3 Quick Action Cards Row -->
        <div class="quick-actions-row">

          <!-- Quick Card 1: Reset Sandi HC Eazy -->
          <div class="quick-card">
            <div class="quick-icon-box icon-blue">
              <img src="<?= base_url('assets/icons/reset.svg') ?>" alt="Reset Sandi">
            </div>
            <div class="quick-card-info">
              <div class="quick-card-text">
                <div class="quick-card-title">Reset Sandi Aplikasi HC Eazy</div>
                <div class="quick-card-subtitle">Ganti/lupa password</div>
              </div>
              <a href="<?= base_url('ticket/create?req_type=Reset%20Password') ?>" class="btn-quick-action action-reset-pw">
                Form Reset Password
              </a>
            </div>
          </div>

          <!-- Quick Card 2: Pusat Bantuan & FAQ -->
          <div class="quick-card">
            <div class="quick-icon-box icon-gray">
              <img src="<?= base_url('assets/icons/shield-question.svg') ?>" alt="Pusat Bantuan">
            </div>
            <div class="quick-card-info">
              <div class="quick-card-text">
                <div class="quick-card-title">Pusat Bantuan & FAQ</div>
                <div class="quick-card-subtitle">Cari panduan kekaryawanan</div>
              </div>
              <a href="<?= base_url('pusat-bantuan') ?>" class="btn-quick-action action-faq">
                Buka Pusat Bantuan HC
              </a>
            </div>
          </div>

          <!-- Quick Card 3: Hotline Darurat HC -->
          <div class="quick-card">
            <div class="quick-icon-box icon-orange">
              <img src="<?= base_url('assets/icons/call.png') ?>" alt="Hotline HC">
            </div>
            <div class="quick-card-info">
              <div class="quick-card-text">
                <div class="quick-card-title">
                  Hotline Darurat HC
                  <i class="fas fa-question-circle info-tip" onclick="showHotlineModal()" title="Info kontak PIC"></i>
                </div>
                <div class="quick-card-subtitle">[nomor handphone PIC Ticketing]</div>
              </div>
              <div class="badge-operational action-hotline" onclick="showHotlineModal()">
                Jam Operasional 08.30 - 17.30
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT COLUMN: Lacak Status Tiket -->
      <aside class="track-column" id="lacak-tiket">
        <div class="track-ticket-card">
          <div class="track-header">
            <div class="track-icon-box">
              <img src="<?= base_url('assets/icons/global-search.svg') ?>" alt="Lacak Tiket">
            </div>
            <div>
              <h2 class="track-title">Lacak Status Tiket</h2>
              <div class="track-subtitle">Cek progres penanganan tiket secara instan</div>
            </div>
          </div>

          <p class="track-instruction">
            Sudah pernah mengajukan keluhan atau tiket bantuan sebelumnya? Masukkan nomor referensi untuk melacak status
            SLA.
          </p>

          <form id="trackTicketForm" class="track-form" onsubmit="handleTrackTicket(event)">
            <div class="form-group-landing">
              <label for="track_ticket_no">Nomor Tiket</label>
              <input type="text" id="track_ticket_no" name="ticket_no" placeholder="CTH: #HC-2025-0042" required
                autocomplete="off">
            </div>

            <div class="form-group-landing">
              <label for="track_email">Email Pelapor</label>
              <input type="email" id="track_email" name="email" placeholder="nama@perusahaan.co.id" autocomplete="off">
            </div>

            <button type="submit" class="btn-track-submit" id="btnTrackSubmit">
              <img src="<?= base_url('assets/icons/doc-search.svg') ?>" alt="Search">
              <span>Lacak Progres Penanganan</span>
            </button>
          </form>
        </div>
      </aside>

    </div>

    <!-- ========================================================================
         3. SECTION: LAYANAN & BANTUAN KEPEGAWAIAN
         ======================================================================== -->
    <section class="services-section">
      <div class="section-header">
        <div>
          <div class="section-tag">Kategori Layanan Populer</div>
          <h2 class="section-title">Layanan & Bantuan Kepegawaian</h2>
          <p class="section-subtitle">Pilih kategori layanan cepat untuk memproses tiket bantuan Anda.</p>
        </div>
        <div class="carousel-controls">
          <button type="button" class="btn-carousel-nav" id="btnPrev" aria-label="Previous"
            onclick="scrollCarousel(-1)">
            <i class="fas fa-arrow-left"></i>
          </button>
          <button type="button" class="btn-carousel-nav" id="btnNext" aria-label="Next" onclick="scrollCarousel(1)">
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- Carousel Cards Track -->
      <div class="services-carousel-wrapper">
        <div class="services-carousel-track" id="servicesCarousel">

          <!-- Card 1: Presensi -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-presensi">PRESENSI</span>
              <h3 class="service-card-title">Koreksi Kehadiran & Presensi</h3>
              <p class="service-card-desc">Sinkronisasi kehadiran HC Eazy, cuti khusus, dan jam kerja.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Koreksi%20Kehadiran%20&%20Presensi') ?>"
                class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 1 Hari</span>
            </div>
          </div>

          <!-- Card 2: Surat Keterangan -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-surat">SURAT KETERANGAN</span>
              <h3 class="service-card-title">Surat Keterangan Kepegawaian</h3>
              <p class="service-card-desc">Ajukan tiket untuk mengurus surat keterangan kepegawaian sesuai kebutuhan
                Anda.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Surat%20Keterangan%20Kepegawaian') ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 3 Hari</span>
            </div>
          </div>

          <!-- Card 3: Payroll -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-payroll">PAYROLL</span>
              <h3 class="service-card-title">Slip Gaji & Bukti Pajak</h3>
              <p class="service-card-desc">Permintaan bukti potong PPh 21 (1721-A1), klarifikasi tunjangan, dan rincian
                payroll berkala.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Slip%20Gaji%20&%20Bukti%20Pajak') ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 2 Hari</span>
            </div>
          </div>

          <!-- Card 4: Administrasi -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-administrasi">ADMINISTRASI</span>
              <h3 class="service-card-title">Surat Keterangan Kerja</h3>
              <p class="service-card-desc">Penerbitan surat pengantar bank, pengajuan KPR, visa dinas, dan verifikasi
                kepegawaian.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Surat%20Keterangan%20Kerja') ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 2 Hari</span>
            </div>
          </div>

          <!-- Card 5: Hubungan Kerja -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-hubungan">HUBUNGAN KERJA</span>
              <h3 class="service-card-title">Konsultasi & Pengaduan HR</h3>
              <p class="service-card-desc">Saluran konfidensial hubungan industrial, etika kerja, dan mediasi
                permasalahan internal.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Konsultasi%20&%20Pengaduan%20HR') ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 1 Hari</span>
            </div>
          </div>

          <!-- Card 6: Finansial -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-finansial">FINANSIAL</span>
              <h3 class="service-card-title">Pinjaman & Fasilitas Finansial</h3>
              <p class="service-card-desc">Fasilitas pembiayaan karyawan MTF, pinjaman darurat, serta fasilitas
                kepemilikan kendaraan karyawan.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Pinjaman%20&%20Fasilitas%20Finansial') ?>"
                class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 3 Hari</span>
            </div>
          </div>

          <!-- Card 7: Karir & Mutasi -->
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill pill-karir">KARIR & MUTASI</span>
              <h3 class="service-card-title">Mutasi & Penugasan Kerja</h3>
              <p class="service-card-desc">Pengurusan biaya relokasi dinas, mutasi cabang, dan administrasi penugasan
                wilayah operasional.</p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= base_url('ticket/create?req_type=Mutasi%20&%20Penugasan%20Kerja') ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla">SLA 3 Hari</span>
            </div>
          </div>

        </div>
      </div>

      <!-- Carousel Pagination Dots -->
      <div class="carousel-dots" id="carouselDots">
        <span class="carousel-dot active" onclick="goToSlide(0)"></span>
        <span class="carousel-dot" onclick="goToSlide(1)"></span>
        <span class="carousel-dot" onclick="goToSlide(2)"></span>
      </div>
    </section>

    <!-- ========================================================================
         4. CORPORATE CULTURE VALUES (MTF)
         ======================================================================== -->
    <section class="corporate-values-section">
      <div class="values-grid">
        <div class="value-item">
          <img src="<?= base_url('assets/images/kepercayaan 1.png') ?>" alt="Kepercayaan">
          <span>KEPERCAYAAN</span>
        </div>
        <div class="value-item">
          <img src="<?= base_url('assets/images/kewirausahaan polos 1.png') ?>" alt="Kewirausahaan">
          <span>KEWIRAUSAHAAN</span>
        </div>
        <div class="value-item">
          <img src="<?= base_url('assets/images/inovatif 1.png') ?>" alt="Inovatif">
          <span>INOVATIF</span>
        </div>
        <div class="value-item">
          <img src="<?= base_url('assets/images/kegembiraan 1.png') ?>" alt="Kegembiraan">
          <span>KEGEMBIRAAN</span>
        </div>
      </div>
    </section>

  </main>

  <!-- ========================================================================
       5. FOOTER
       ======================================================================== -->
  <footer class="landing-footer">
    <div class="header-container footer-container">
      <div>
        &copy; 2026 Human Capital Helpdesk Services. All rights reserved.
      </div>
      <div class="footer-links">
        <a href="javascript:void(0)"
          onclick="alert('Kebijakan Privasi Human Capital PT Mandiri Tunas Finance.')">Privacy Policy</a>
        <a href="javascript:void(0)"
          onclick="alert('Syarat & Ketentuan Layanan Helpdesk HC PT Mandiri Tunas Finance.')">Terms of Service</a>
      </div>
    </div>
  </footer>

  <!-- ========================================================================
       6. MODAL: HASIL LACAK TIKET
       ======================================================================== -->
  <div class="landing-modal-overlay" id="trackResultModal">
    <div class="landing-modal">
      <div class="modal-header">
        <h3><i class="fas fa-ticket-alt" style="color: #2563EB; margin-right: 8px;"></i> Status Tiket Anda</h3>
        <button type="button" class="btn-close-modal" onclick="closeModal('trackResultModal')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="track-result-card" id="trackResultBody">
          <!-- Populated by JavaScript -->
        </div>
        <div style="margin-top: 18px; text-align: right;">
          <button type="button" class="btn-quick-action" style="width: auto; padding: 8px 20px; display: inline-block;"
            onclick="closeModal('trackResultModal')">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================
       7. MODAL: HOTLINE DARURAT HC
       ======================================================================== -->
  <div class="landing-modal-overlay" id="hotlineModal">
    <div class="landing-modal">
      <div class="modal-header">
        <h3><i class="fas fa-phone-alt" style="color: #D97706; margin-right: 8px;"></i> Hotline Darurat Human Capital
        </h3>
        <button type="button" class="btn-close-modal" onclick="closeModal('hotlineModal')">&times;</button>
      </div>
      <div class="modal-body">
        <p style="font-size: 13px; color: #475569; margin-bottom: 16px; line-height: 1.5;">
          Untuk kendala mendesak atau darurat operasional kepegawaian, hubungi kontak resmi PIC Ticketing di bawah ini:
        </p>
        <div class="track-result-card">
          <div class="result-row">
            <span class="result-label">PIC Layanan Helpdesk</span>
            <span class="result-val">Divisi Human Capital MTF</span>
          </div>
          <div class="result-row">
            <span class="result-label">WhatsApp Hotline</span>
            <span class="result-val"><a href="https://wa.me/6281119500000" target="_blank"
                style="color: #16A34A; text-decoration: underline;">+62 811-1950-0000</a></span>
          </div>
          <div class="result-row">
            <span class="result-label">Email Support</span>
            <span class="result-val">hc.helpdesk@mtf.co.id</span>
          </div>
          <div class="result-row">
            <span class="result-label">Jam Operasional</span>
            <span class="result-val">Senin - Jumat, 08.30 - 17.30 WIB</span>
          </div>
        </div>
        <div style="margin-top: 18px; text-align: right;">
          <button type="button" class="btn-quick-action" style="width: auto; padding: 8px 20px; display: inline-block;"
            onclick="closeModal('hotlineModal')">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================
       8. JAVASCRIPT LOGIC
       ======================================================================== -->
  <script>
    // Toggle Mobile Dropdown Menu
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileDropdownMenu');
      const toggle = document.getElementById('mobileMenuToggle');
      const icon = document.getElementById('toggleIcon');
      
      const isOpen = menu.classList.contains('show');
      if (isOpen) {
        closeMobileMenu();
      } else {
        menu.classList.add('show');
        toggle.classList.add('active');
        icon.className = 'fas fa-times';
      }
    }

    function closeMobileMenu() {
      const menu = document.getElementById('mobileDropdownMenu');
      const toggle = document.getElementById('mobileMenuToggle');
      const icon = document.getElementById('toggleIcon');
      
      if (menu) menu.classList.remove('show');
      if (toggle) toggle.classList.remove('active');
      if (icon) icon.className = 'fas fa-bars';
    }

    // Focus ke input lacak tiket saat klik menu navigasi
    function focusTrackInput(e) {
      e.preventDefault();
      const input = document.getElementById('track_ticket_no');
      if (input) {
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => input.focus(), 300);
      }
    }

    function focusTrackInputMobile(e) {
      e.preventDefault();
      closeMobileMenu();
      setTimeout(() => {
        const input = document.getElementById('track_ticket_no');
        if (input) {
          input.scrollIntoView({ behavior: 'smooth', block: 'center' });
          setTimeout(() => input.focus(), 350);
        }
      }, 200);
    }

    // Modal Control
    function showHotlineModal() {
      document.getElementById('hotlineModal').style.display = 'flex';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    window.onclick = function (event) {
      if (event.target.classList.contains('landing-modal-overlay')) {
        event.target.style.display = 'none';
      }
      
      const menu = document.getElementById('mobileDropdownMenu');
      const toggle = document.getElementById('mobileMenuToggle');
      if (menu && menu.classList.contains('show')) {
        if (!menu.contains(event.target) && !toggle.contains(event.target)) {
          closeMobileMenu();
        }
      }
    }

    // Handle Track Ticket Submit
    function handleTrackTicket(e) {
      e.preventDefault();
      const ticketNo = document.getElementById('track_ticket_no').value.trim();
      const email = document.getElementById('track_email').value.trim();
      const btn = document.getElementById('btnTrackSubmit');

      if (!ticketNo) {
        alert('Harap masukkan nomor tiket.');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memeriksa status...';

      fetch('<?= base_url('track-ticket') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
          'ticket_no': ticketNo,
          'email': email
        })
      })
        .then(response => response.json())
        .then(result => {
          btn.disabled = false;
          btn.innerHTML = '<img src="<?= base_url('assets/icons/doc-search.svg') ?>" alt="Search"> <span>Lacak Progres Penanganan</span>';

          if (result.status === 'success') {
            const t = result.data;
            const statusClass = 'status-' + (t.ticket_status ? t.ticket_status.toLowerCase().replace(' ', '_') : 'open');

            let content = `
            <div class="result-row">
              <span class="result-label">Nomor Tiket</span>
              <span class="result-val">#${t.id.substring(0, 13)}...</span>
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

            document.getElementById('trackResultBody').innerHTML = content;
            document.getElementById('trackResultModal').style.display = 'flex';
          } else {
            alert(result.message || 'Tiket tidak ditemukan. Periksa kembali nomor tiket Anda.');
          }
        })
        .catch(err => {
          btn.disabled = false;
          btn.innerHTML = '<img src="<?= base_url('assets/icons/doc-search.svg') ?>" alt="Search"> <span>Lacak Progres Penanganan</span>';
          alert('Terjadi kesalahan saat memproses data. Silakan coba beberapa saat lagi.');
        });
    }

    // Carousel Navigation
    const carousel = document.getElementById('servicesCarousel');
    const dots = document.querySelectorAll('.carousel-dot');

    function scrollCarousel(direction) {
      if (!carousel) return;
      const scrollAmount = 300 * direction;
      carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }

    function goToSlide(index) {
      if (!carousel) return;
      const cardWidth = carousel.scrollWidth / 3;
      carousel.scrollTo({ left: cardWidth * index, behavior: 'smooth' });
      updateDots(index);
    }

    function updateDots(activeIndex) {
      dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === activeIndex);
      });
    }

    if (carousel) {
      carousel.addEventListener('scroll', () => {
        const scrollPercentage = carousel.scrollLeft / (carousel.scrollWidth - carousel.clientWidth);
        let activeIdx = 0;
        if (scrollPercentage > 0.6) activeIdx = 2;
        else if (scrollPercentage > 0.25) activeIdx = 1;
        updateDots(activeIdx);
      });
    }

  </script>

  <!-- User Login Popup Modal Component -->
  <?= view('components/user_login_modal') ?>

</body>

</html>