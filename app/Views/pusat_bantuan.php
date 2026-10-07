<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ & Help Center - HC Helpdesk PT Mandiri Tunas Finance</title>
  
  <!-- Google Fonts: Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Landing Base & FAQ Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=' . time()) ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/faq.css?v=' . time()) ?>">
</head>
<body>

  <!-- ========================================================================
       1. TOP NAVBAR (Global Component)
       ======================================================================== -->
  <?= view('components/public_navbar', ['activePage' => 'faq']) ?>

  <!-- ========================================================================
       2. MAIN CONTENT
       ======================================================================== -->
  <main class="main-wrapper">

    <!-- Hero Search Section -->
    <section class="faq-hero-section">
      <div class="faq-hero-badge">
        Portal Bantuan Divisi Human Capital
      </div>
      <h1 class="faq-hero-title">
        Ada yang bisa kami bantu seputar layanan <br>
        <span class="highlight-blue">Human Capital</span>?
      </h1>
      <p class="faq-hero-desc">
        Temukan jawaban cepat untuk pertanyaan umum, panduan aplikasi HC Eazy, alur pengajuan tiket, dan SLA penanganan kendala kepegawaian Anda secara transparan.
      </p>

      <!-- Search Box -->
      <div class="faq-search-box">
        <i class="fas fa-search faq-search-icon"></i>
        <input type="text" id="faqSearchInput" class="faq-search-input" placeholder="Cari topik atau kendala (reset sandi, absensi, slip gaji)..." onkeyup="filterFaq()" autocomplete="off">
        <button type="button" class="btn-faq-search" onclick="filterFaq()">
          <i class="fas fa-search"></i>
          <span>Cari Solusi</span>
        </button>
      </div>

      <!-- Popular Keywords -->
      <div class="faq-popular-row">
        <span class="popular-label">
          <i class="fas fa-arrow-trend-up" style="color: #2563EB;"></i> Populer:
        </span>
        <a href="javascript:void(0)" class="keyword-chip" onclick="searchByKeyword('Reset Password HC Eazy')">Reset Password HC Eazy</a>
        <a href="javascript:void(0)" class="keyword-chip" onclick="searchByKeyword('Koreksi Absensi')">Koreksi Absensi</a>
        <a href="javascript:void(0)" class="keyword-chip" onclick="searchByKeyword('Jadwal Gaji & THR')">Jadwal Gaji & THR</a>
        <a href="javascript:void(0)" class="keyword-chip" onclick="searchByKeyword('Klaim Medis / Asuransi')">Klaim Medis / Asuransi</a>
        <a href="javascript:void(0)" class="keyword-chip" onclick="searchByKeyword('SLA Tiket')">SLA Tiket</a>
      </div>
    </section>

    <!-- Section: Eksplorasi Berdasarkan Topik Permasalahan -->
    <section class="faq-categories-section">
      <div class="faq-section-header">
        <div>
          <div class="section-tag">Kategori Bantuan</div>
          <h2 class="section-title">Eksplorasi Berdasarkan Topik Permasalahan</h2>
        </div>
        <div style="font-size: 12px; color: #94A3B8; font-weight: 600;">
          Menampilkan 36 panduan
        </div>
      </div>

      <div class="categories-grid">
        
        <!-- Category Card 1: GENERAL -->
        <div class="category-card" onclick="filterByCategory('general')">
          <div class="category-card-top">
            <div class="category-icon-box cat-icon-blue">
              <i class="fas fa-book-open"></i>
            </div>
            <span class="category-article-count">12 Artikel</span>
          </div>
          <div>
            <h3 class="category-card-title">GENERAL</h3>
            <p class="category-card-desc">Informasi seputar kebijakan kerja, benefit karyawan, aturan jam kerja kantor, dan SOP operasional HC resmi.</p>
          </div>
          <a href="javascript:void(0)" class="category-card-link">
            Lihat Panduan &rarr;
          </a>
        </div>

        <!-- Category Card 2: PENGURUSAN TIKET -->
        <div class="category-card" onclick="filterByCategory('tiket')">
          <div class="category-card-top">
            <div class="category-icon-box cat-icon-indigo">
              <i class="fas fa-ticket-alt"></i>
            </div>
            <span class="category-article-count">8 Artikel</span>
          </div>
          <div>
            <h3 class="category-card-title">PENGURUSAN TIKET</h3>
            <p class="category-card-desc">Panduan lengkap membuat tiket bantuan, format dokumen lampiran pendukung, serta monitoring tiket kendala.</p>
          </div>
          <a href="javascript:void(0)" class="category-card-link">
            Lihat Panduan &rarr;
          </a>
        </div>

        <!-- Category Card 3: SLA & TIMELINES -->
        <div class="category-card" onclick="filterByCategory('sla')">
          <div class="category-card-top">
            <div class="category-icon-box cat-icon-cyan">
              <i class="fas fa-stopwatch"></i>
            </div>
            <span class="category-article-count">6 Artikel</span>
          </div>
          <div>
            <h3 class="category-card-title">SLA & TIMELINES</h3>
            <p class="category-card-desc">Ketentuan eskalasi waktu penyelesaian berdasarkan prioritas dan jenis pengajuan layanan kepegawaian.</p>
          </div>
          <a href="javascript:void(0)" class="category-card-link">
            Lihat Panduan &rarr;
          </a>
        </div>

        <!-- Category Card 4: MY ACCOUNT -->
        <div class="category-card" onclick="filterByCategory('account')">
          <div class="category-card-top">
            <div class="category-icon-box cat-icon-purple">
              <i class="fas fa-user-shield"></i>
            </div>
            <span class="category-article-count">10 Artikel</span>
          </div>
          <div>
            <h3 class="category-card-title">MY ACCOUNT</h3>
            <p class="category-card-desc">Panduan akun aplikasi HC Eazy, pembukaan blokir akun, lupa PIN presensi, dan update data profil master.</p>
          </div>
          <a href="javascript:void(0)" class="category-card-link">
            Lihat Panduan &rarr;
          </a>
        </div>

      </div>
    </section>

    <!-- Section: Pertanyaan yang sering ditanyakan & Sidebar -->
    <section class="faq-main-section">
      <div class="faq-main-grid">

        <!-- LEFT: FAQ Accordion List -->
        <div class="faq-content-column">
          <div class="faq-accordion-header">
            <h2 class="section-title">Pertanyaan yang sering ditanyakan</h2>
            <span style="font-size: 11.5px; color: #94A3B8; font-weight: 600;">
              Paling sering ditanyakan 7 hari terakhir
            </span>
          </div>

          <div class="faq-list" id="faqAccordionList">

            <!-- FAQ 1 (Default Open) -->
            <div class="faq-item active" data-category="account" data-keywords="reset kata sandi akun terkunci lupa password hc eazy pin">
              <button type="button" class="faq-question-btn" onclick="toggleFaqItem(this)">
                <div class="faq-num-badge">01</div>
                <div class="faq-question-text">
                  <div class="faq-question-title">Bagaimana prosedur reset kata sandi atau akun terkunci di HC Eazy?</div>
                  <div class="faq-question-meta">Kategori: My Account &bull; Diperbarui 3 hari lalu</div>
                </div>
                <i class="fas fa-chevron-down faq-toggle-icon"></i>
              </button>
              <div class="faq-answer-collapse">
                <div class="faq-answer-inner">
                  <div class="faq-answer-box">
                    <p><strong>Jika akun Anda mengalami kendala sandi :</strong></p>
                    <ul>
                      <li>Buka aplikasi HC Eazy versi mobile atau web browser internal.</li>
                      <li>Klik tombol <strong>"Lupa Kata Sandi?"</strong> pada layar masuk utama.</li>
                      <li>Masukkan NIP (Nomor Induk Pegawai) aktif Anda.</li>
                      <li>Tautan pembaruan sandi otomatis dikirimkan ke email resmi MTF Anda dalam kurun waktu &lt; 2 menit.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item" data-category="sla" data-keywords="sla waktu standar penyelesaian prioritas tiket jam hari kerja">
              <button type="button" class="faq-question-btn" onclick="toggleFaqItem(this)">
                <div class="faq-num-badge">02</div>
                <div class="faq-question-text">
                  <div class="faq-question-title">Berapa lama standar Service Level Agreement (SLA) setiap kategori tiket?</div>
                  <div class="faq-question-meta">Kategori: SLA & Timelines &bull; Standar Nasional HC 2025</div>
                </div>
                <i class="fas fa-chevron-down faq-toggle-icon"></i>
              </button>
              <div class="faq-answer-collapse">
                <div class="faq-answer-inner">
                  <div class="faq-answer-box">
                    <p><strong>Standar SLA Helpdesk Human Capital MTF diatur sebagai berikut :</strong></p>
                    <ul>
                      <li><strong>Koreksi Kehadiran & Presensi :</strong> 1 Hari Kerja (24 Jam)</li>
                      <li><strong>Surat Keterangan Kerja & Bank :</strong> 2 Hari Kerja</li>
                      <li><strong>Slip Gaji & Bukti Pajak :</strong> 2 Hari Kerja</li>
                      <li><strong>Surat Keterangan Kepegawaian :</strong> 3 Hari Kerja</li>
                      <li><strong>Pinjaman & Fasilitas Finansial :</strong> 3 Hari Kerja</li>
                      <li><strong>Kendala Darurat & Sistem :</strong> Ditangani langsung oleh PIC terkait dalam &lt; 4 jam.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item" data-category="tiket" data-keywords="melacak lacak tracking cek status tiket tanpa login guest">
              <button type="button" class="faq-question-btn" onclick="toggleFaqItem(this)">
                <div class="faq-num-badge">03</div>
                <div class="faq-question-text">
                  <div class="faq-question-title">Bagaimana cara melacak tiket jika saya tidak login ke portal?</div>
                  <div class="faq-question-meta">Kategori: Ticket Submission &bull; Panduan Akses Cepat</div>
                </div>
                <i class="fas fa-chevron-down faq-toggle-icon"></i>
              </button>
              <div class="faq-answer-collapse">
                <div class="faq-answer-inner">
                  <div class="faq-answer-box">
                    <p><strong>Anda dapat melacak tiket secara instan tanpa perlu login :</strong></p>
                    <ul>
                      <li>Kunjungi Beranda portal HC Helpdesk di <a href="<?= base_url('/') ?>" style="color: #2563EB; font-weight: 600;">Beranda</a>.</li>
                      <li>Gunakan formulir <strong>"Lacak Status Tiket"</strong> pada kolom sebelah kanan.</li>
                      <li>Masukkan <strong>Nomor Tiket</strong> referensi Anda (contoh: <code>#HC-2025-0042</code>) dan Email Pelapor.</li>
                      <li>Klik <strong>"Lacak Progres Penanganan"</strong> untuk memantau status pengerjaan, PIC, dan target SLA.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- FAQ 4 (General) -->
            <div class="faq-item" data-category="general" data-keywords="benefit asuransi klaim rawat jalan kacamata spj operasional kantor kebijakan umum">
              <button type="button" class="faq-question-btn" onclick="toggleFaqItem(this)">
                <div class="faq-num-badge">04</div>
                <div class="faq-question-text">
                  <div class="faq-question-title">Bagaimana prosedur pengajuan klaim rawat jalan dan kacamata karyawan?</div>
                  <div class="faq-question-meta">Kategori: General &bull; Kebijakan Benefit HC MTF</div>
                </div>
                <i class="fas fa-chevron-down faq-toggle-icon"></i>
              </button>
              <div class="faq-answer-collapse">
                <div class="faq-answer-inner">
                  <div class="faq-answer-box">
                    <p><strong>Alur pengajuan klaim benefit kesehatan karyawan :</strong></p>
                    <ul>
                      <li>Pastikan kuitansi asli dokter/optik bertanggal maksimal 30 hari kalender.</li>
                      <li>Lampirkan resep kacamata atau diagnosa dokter resmi beserta rincian biaya.</li>
                      <li>Unggah dokumen klaim melalui menu <strong>Benefit & Klaim</strong> pada aplikasi HC Eazy.</li>
                      <li>Tim Human Capital akan memverifikasi dan memproses penggantian (reimbursement) ke rekening payroll dalam 5 hari kerja.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div id="noFaqFound" style="display: none; text-align: center; padding: 36px 20px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
              <i class="fas fa-search" style="font-size: 24px; color: #94a3b8; margin-bottom: 8px;"></i>
              <p style="font-weight: 600; font-size: 14px; margin-top: 6px;">Tidak ada artikel atau FAQ yang cocok dengan kata kunci tersebut.</p>
              <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Coba gunakan kata kunci lain atau ajukan tiket bantuan langsung melalui form tiket.</p>
            </div>

          </div>
        </div>

        <!-- RIGHT: Sidebar Info Cards -->
        <aside class="faq-sidebar">

          <!-- Card 1: Masih Membutuhkan Bantuan? -->
          <div class="faq-help-card">
            <div class="faq-help-icon-box">
              <i class="fas fa-question-circle"></i>
            </div>
            <h3 class="faq-help-title">Masih Membutuhkan Bantuan?</h3>
            <p class="faq-help-desc">
              Belum menemukan solusi yang Anda cari di daftar FAQ? Buat tiket laporan langsung atau pantau tiket yang sedang berjalan.
            </p>
            <a href="<?= base_url('ticket/create') ?>" class="btn-sidebar-primary">
              <i class="fas fa-plus-circle"></i>
              <span>Masuk & Buat Tiket</span>
            </a>
            <a href="<?= base_url('/#lacak-tiket') ?>" class="btn-sidebar-secondary">
              <i class="fas fa-search"></i>
              <span>Lacak Status Tiket</span>
            </a>
          </div>

          <!-- Card 2: Kontak Layanan Darurat HC -->
          <div class="faq-hotline-card">
            <div class="hotline-header">
              <i class="fas fa-phone-alt"></i>
              <span>Kontak Layanan Darurat HC</span>
            </div>

            <!-- Internal Hotline -->
            <div class="hotline-box">
              <div class="hotline-box-label">
                <i class="fas fa-phone-volume"></i>
                <span>Internal Hotline Ext.</span>
              </div>
              <div class="hotline-box-val">1104 / 1105</div>
            </div>

            <!-- WhatsApp -->
            <div class="hotline-box box-wa">
              <div class="hotline-box-label">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp Helpdesk 24/7</span>
              </div>
              <div class="hotline-box-val">
                <a href="https://wa.me/6281199887766" target="_blank" style="color: inherit; text-decoration: none;">
                  +62 811-9988-7766
                </a>
              </div>
            </div>

            <!-- Hours Notice -->
            <div class="hotline-hours">
              <i class="far fa-clock" style="margin-top: 2px;"></i>
              <div>
                Jam Operasional Reguler:<br>
                <strong>Senin - Jumat: 08:00 - 17:00 WIB</strong>
              </div>
            </div>
          </div>

        </aside>

      </div>
    </section>

    <!-- ========================================================================
         3. CORPORATE CULTURE VALUES (MTF)
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
       4. FOOTER
       ======================================================================== -->
  <footer class="landing-footer">
    <div class="header-container footer-container">
      <div>
        &copy; 2026 Human Capital Helpdesk Services. All rights reserved.
      </div>
      <div class="footer-links">
        <a href="javascript:void(0)" onclick="alert('Kebijakan Privasi Human Capital PT Mandiri Tunas Finance.')">Privacy Policy</a>
        <a href="javascript:void(0)" onclick="alert('Syarat & Ketentuan Layanan Helpdesk HC PT Mandiri Tunas Finance.')">Terms of Service</a>
      </div>
    </div>
  </footer>

  <!-- ========================================================================
       5. JAVASCRIPT LOGIC
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

    window.onclick = function (event) {
      const menu = document.getElementById('mobileDropdownMenu');
      const toggle = document.getElementById('mobileMenuToggle');
      if (menu && menu.classList.contains('show')) {
        if (!menu.contains(event.target) && !toggle.contains(event.target)) {
          closeMobileMenu();
        }
      }
    }

    // Toggle Accordion Item
    function toggleFaqItem(btn) {
      const item = btn.closest('.faq-item');
      const isActive = item.classList.contains('active');
      
      // Close other items for a clean accordion experience (or comment out if multiple can open)
      document.querySelectorAll('.faq-item').forEach(el => {
        if (el !== item) el.classList.remove('active');
      });

      if (isActive) {
        item.classList.remove('active');
      } else {
        item.classList.add('active');
      }
    }

    // Realtime Search & Filter
    function filterFaq() {
      const query = document.getElementById('faqSearchInput').value.toLowerCase().trim();
      const items = document.querySelectorAll('.faq-item');
      let visibleCount = 0;

      items.forEach(item => {
        const text = (item.innerText + ' ' + (item.getAttribute('data-keywords') || '')).toLowerCase();
        if (!query || text.includes(query)) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      const noFound = document.getElementById('noFaqFound');
      if (noFound) {
        noFound.style.display = visibleCount === 0 ? 'block' : 'none';
      }
    }

    // Smoothly scroll directly to "Pertanyaan yang sering ditanyakan" header (Gambar 3)
    function scrollToFaqHeader() {
      const header = document.querySelector('.faq-accordion-header');
      if (header) {
        const navbar = document.querySelector('.landing-header');
        const navHeight = navbar ? navbar.offsetHeight : 64;
        const targetPos = header.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = targetPos - navHeight - 16;

        window.scrollTo({
          top: Math.max(0, offsetPosition),
          behavior: 'smooth'
        });
      }
    }

    // Filter by keyword chip
    function searchByKeyword(keyword) {
      const input = document.getElementById('faqSearchInput');
      input.value = keyword;
      filterFaq();
      scrollToFaqHeader();
    }

    // Filter by category card
    function filterByCategory(cat) {
      const items = document.querySelectorAll('.faq-item');
      let visibleCount = 0;

      items.forEach(item => {
        const itemCat = item.getAttribute('data-category') || '';
        if (cat === 'all' || itemCat.includes(cat)) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      // Clear search input
      document.getElementById('faqSearchInput').value = '';
      
      const noFound = document.getElementById('noFaqFound');
      if (noFound) {
        noFound.style.display = visibleCount === 0 ? 'block' : 'none';
      }

      // Automatically open the first matching FAQ item for instant view
      const firstVisible = Array.from(items).find(item => item.style.display !== 'none');
      if (firstVisible) {
        items.forEach(el => el.classList.remove('active'));
        firstVisible.classList.add('active');
      }

      scrollToFaqHeader();
    }

    // Natural 1:1 Swipe/Drag Helper (works with mouse on desktop/devtools & touch on mobile)
    function enableDragSwipe(container) {
      if (!container) return;
      let isDown = false;
      let startX = 0;
      let scrollLeft = 0;
      let hasDragged = false;

      container.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        isDown = true;
        hasDragged = false;
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
      });

      window.addEventListener('mouseup', () => {
        if (!isDown) return;
        isDown = false;
        setTimeout(() => { hasDragged = false; }, 50);
      });

      container.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        const x = e.pageX - container.offsetLeft;
        const walk = x - startX; // Exact 1:1 natural movement
        if (Math.abs(walk) > 5) {
          hasDragged = true;
        }
        container.scrollLeft = scrollLeft - walk;
      });

      // Prevent accidental card click/navigation when user is dragging
      container.addEventListener('click', (e) => {
        if (hasDragged) {
          e.preventDefault();
          e.stopPropagation();
        }
      }, true);
    }

  </script>

  <!-- User Login Popup Modal Component -->
  <?= view('components/user_login_modal') ?>

</body>
</html>