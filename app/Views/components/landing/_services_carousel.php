<!-- SECTION: LAYANAN & BANTUAN KEPEGAWAIAN -->
<section class="services-section">
  <div class="section-header">
    <div>
      <div class="section-tag">Kategori Layanan Populer</div>
      <h2 class="section-title">Layanan & Bantuan Kepegawaian</h2>
      <p class="section-subtitle">Pilih kategori layanan cepat untuk memproses tiket bantuan Anda.</p>
    </div>
    <div class="carousel-controls">
      <button type="button" class="btn-carousel-nav" id="btnPrev" aria-label="Previous" onclick="scrollCarousel(-1)">
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

      <?php if (!empty($popularServices)): ?>
        <?php foreach ($popularServices as $svc): ?>
          <?php
            $badge = !empty($svc['badge_tag']) ? $svc['badge_tag'] : (!empty($svc['category_name']) ? strtoupper($svc['category_name']) : 'LAYANAN HC');
            $slaHours = !empty($svc['sla_hours']) ? (int) $svc['sla_hours'] : 24;
            $slaText = ($slaHours >= 24) ? ('SLA ' . round($slaHours / 24) . ' Hari') : ('SLA ' . $slaHours . ' Jam');
            $catId = $svc['category_id'] ?? '';
            $createUrl = base_url('ticket/create?req_type=' . urlencode($svc['name']) . ($catId ? '&category_id=' . urlencode($catId) : ''));
          ?>
          <div class="service-card">
            <div class="service-card-top">
              <span class="service-pill" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:10.5px; padding:4px 10px; border-radius:999px; text-transform:uppercase; letter-spacing:0.5px; display:inline-block; margin-bottom:12px;">
                <?= esc($badge) ?>
              </span>
              <h3 class="service-card-title"><?= esc($svc['name']) ?></h3>
              <p class="service-card-desc"><?= esc($svc['description'] ?? 'Layanan terpadu Human Capital MTF.') ?></p>
            </div>
            <div class="service-card-bottom">
              <a href="<?= $createUrl ?>" class="service-link">
                Ajukan Tiketing &rarr;
              </a>
              <span class="service-sla"><?= esc($slaText) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback static jika belum ada data DB -->
        <div class="service-card">
          <div class="service-card-top">
            <span class="service-pill pill-presensi">PRESENSI</span>
            <h3 class="service-card-title">Koreksi Kehadiran & Presensi</h3>
            <p class="service-card-desc">Sinkronisasi kehadiran HC Eazy, cuti khusus, dan jam kerja.</p>
          </div>
          <div class="service-card-bottom">
            <a href="<?= base_url('ticket/create?req_type=Koreksi%20Kehadiran%20&%20Presensi') ?>" class="service-link">
              Ajukan Tiketing &rarr;
            </a>
            <span class="service-sla">SLA 1 Hari</span>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- Carousel Pagination Dots -->
  <div class="carousel-dots" id="carouselDots">
    <span class="carousel-dot active" onclick="goToSlide(0)"></span>
    <span class="carousel-dot" onclick="goToSlide(1)"></span>
    <span class="carousel-dot" onclick="goToSlide(2)"></span>
  </div>
</section>
