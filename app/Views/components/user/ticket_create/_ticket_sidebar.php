<?php
/**
 * Sidebar Component untuk Form Buat Tiket
 */
?>

<aside class="ticket-create-sidebar">

  <!-- Widget 1: Komitmen SLA -->
  <div class="sidebar-card mb-4">
    <div class="sidebar-card-header">
      <div class="sidebar-icon-circle bg-blue-subtle">
        <i class="fa-solid fa-stopwatch text-accent"></i>
      </div>
      <div>
        <h4 class="sidebar-card-title">Komitmen Standar SLA</h4>
        <p class="sidebar-card-subtitle">Waktu respon &amp; penyelesaian</p>
      </div>
    </div>
    <div class="sidebar-card-body">
      <ul class="sla-bullet-list">
        <li>
          <div class="sla-item-icon"><i class="fa-solid fa-clock text-info"></i></div>
          <div class="sla-item-text">
            <strong>Respon Awal:</strong>
            <span>Maksimal 4 jam kerja setelah tiket diterima.</span>
          </div>
        </li>
        <li>
          <div class="sla-item-icon"><i class="fa-solid fa-check-double text-success"></i></div>
          <div class="sla-item-text">
            <strong>Target Resolusi:</strong>
            <span>1 &ndash; 2 hari kerja (tergantung kategori &amp; pihak ketiga).</span>
          </div>
        </li>
        <li>
          <div class="sla-item-icon"><i class="fa-solid fa-calendar-day text-warning"></i></div>
          <div class="sla-item-text">
            <strong>Jam Operasional:</strong>
            <span>Senin &ndash; Jumat, 08:00 &ndash; 17:00 WIB.</span>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <!-- Widget 2: Tips Pengajuan -->
  <div class="sidebar-card mb-4">
    <div class="sidebar-card-header">
      <div class="sidebar-icon-circle bg-amber-subtle">
        <i class="fa-solid fa-lightbulb text-warning"></i>
      </div>
      <div>
        <h4 class="sidebar-card-title">Tips Tiket Cepat Selesai</h4>
        <p class="sidebar-card-subtitle">Panduan pengisian formulir</p>
      </div>
    </div>
    <div class="sidebar-card-body">
      <div class="tips-item">
        <div class="tips-badge">1</div>
        <div class="tips-text">Pastikan <strong>NIK</strong> sesuai data resmi kepegawaian MTF.</div>
      </div>
      <div class="tips-item">
        <div class="tips-badge">2</div>
        <div class="tips-text">Tuliskan tanggal kejadian &amp; nama rekan/atasan terkait jika ada.</div>
      </div>
      <div class="tips-item">
        <div class="tips-badge">3</div>
        <div class="tips-text">Lampirkan tangkapan layar jika mengalami kendala sistem/aplikasi.</div>
      </div>
    </div>
  </div>

  <!-- Widget 3: Hotline HC -->
  <div class="sidebar-card hotline-card">
    <div class="sidebar-card-header">
      <div class="sidebar-icon-circle bg-emerald-subtle">
        <i class="fa-solid fa-headset text-success"></i>
      </div>
      <div>
        <h4 class="sidebar-card-title">Butuh Bantuan Mendesak?</h4>
        <p class="sidebar-card-subtitle">Hubungi tim Helpdesk langsung</p>
      </div>
    </div>
    <div class="sidebar-card-body">
      <p class="is-size-7 has-text-grey mb-3">
        Jika mengalami kendala medis rawat inap darurat atau penutupan akses sistem kritis:
      </p>
      <div class="hotline-contact-box">
        <div class="hotline-line">
          <i class="fa-solid fa-phone mr-2"></i> Ekstensi Internal: <strong>4410</strong>
        </div>
        <div class="hotline-line">
          <i class="fa-brands fa-whatsapp mr-2"></i> WhatsApp: <strong>0811-9876-4410</strong>
        </div>
      </div>
      <a href="https://wa.me/6281198764410?text=Halo%20HC%20Helpdesk%20MTF" target="_blank" rel="noopener noreferrer" class="button is-small is-fullwidth btn-whatsapp-hotline mt-3">
        <i class="fa-brands fa-whatsapp mr-2"></i> Chat WhatsApp HC
      </a>
    </div>
  </div>

</aside>
