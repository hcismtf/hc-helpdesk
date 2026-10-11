<!-- FAQ Accordion List (Static + Dynamic Database Support) -->
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

    <!-- Optional: Dynamic Database FAQs -->
    <?php if (!empty($dbFaqs) || !empty($faqs)): ?>
      <?php 
        $activeFaqs = $dbFaqs ?? $faqs ?? [];
        $counter = 5;
        foreach ($activeFaqs as $f): 
          $q = $f['question'] ?? '';
          $a = $f['answer'] ?? '';
          if (empty($q)) continue;
      ?>
        <div class="faq-item" data-category="general" data-keywords="<?= esc(strtolower($q . ' ' . $a)) ?>">
          <button type="button" class="faq-question-btn" onclick="toggleFaqItem(this)">
            <div class="faq-num-badge"><?= sprintf('%02d', $counter++) ?></div>
            <div class="faq-question-text">
              <div class="faq-question-title"><?= esc($q) ?></div>
              <div class="faq-question-meta">Kategori: Pusat Bantuan MTF</div>
            </div>
            <i class="fas fa-chevron-down faq-toggle-icon"></i>
          </button>
          <div class="faq-answer-collapse">
            <div class="faq-answer-inner">
              <div class="faq-answer-box">
                <p><?= nl2br(esc($a)) ?></p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div id="noFaqFound" style="display: none; text-align: center; padding: 36px 20px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
      <i class="fas fa-search" style="font-size: 24px; color: #94a3b8; margin-bottom: 8px;"></i>
      <p style="font-weight: 600; font-size: 14px; margin-top: 6px;">Tidak ada artikel atau FAQ yang cocok dengan kata kunci tersebut.</p>
      <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Coba gunakan kata kunci lain atau ajukan tiket bantuan langsung melalui form tiket.</p>
    </div>

  </div>
</div>
