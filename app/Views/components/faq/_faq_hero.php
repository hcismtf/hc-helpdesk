<!-- FAQ Hero Search Section -->
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
