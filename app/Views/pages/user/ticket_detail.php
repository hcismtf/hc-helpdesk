<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="ticket-detail-page">

  <!-- 1. Breadcrumb Navigation & Top Action Row -->
  <div class="detail-nav-row">
    <div class="breadcrumbs-wrap">
      <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i> Dashboard</a>
      <span class="crumb-sep">/</span>
      <a href="<?= base_url('my/tickets') ?>">Riwayat Tiket & Pengajuan</a>
      <span class="crumb-sep">/</span>
      <span class="crumb-active font-mono">#<?= esc($ticketNo) ?></span>
    </div>

    <div class="detail-nav-actions">
      <a href="<?= base_url('dashboard') ?>" class="btn-nav-action">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
      </a>
      <button type="button" class="btn-nav-action" onclick="window.print()">
        <i class="fas fa-file-pdf"></i> Unduh PDF
      </button>
      <button type="button" class="btn-nav-action" id="btnShareTicket" data-url="<?= current_url() ?>">
        <i class="fas fa-share-alt"></i> Bagikan
      </button>
      <?php if (!\App\Entities\TicketStatus::isClosedOrResolved($status)): ?>
        <button type="button" class="btn-nav-action btn-danger-outline" id="btnOpenCloseModal">
          <i class="fas fa-times-circle"></i> Tutup Tiket
        </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- 2. Hero Overview Card -->
  <div class="ticket-hero-card">
    <div class="hero-main-content">
      <!-- Tag Row -->
      <div class="hero-tags-row">
        <span class="hero-tag tag-ticket-no">#<?= esc($ticketNo) ?></span>
        <span class="hero-tag <?= esc($statusClass) ?>">
          <?php if ($hasPulse): ?>
            <span class="pulse-dot"></span>
          <?php endif; ?>
          <?= esc($statusLabel) ?>
        </span>
        <span class="hero-tag tag-priority-<?= esc($priority) ?>">
          <i class="fas fa-exclamation mr-1"></i> <?= esc($priorityLabel) ?>
        </span>
        <span class="hero-tag tag-category">
          <i class="fas fa-layer-group mr-1"></i> <?= esc($categoryName) ?>
        </span>
      </div>

      <!-- Ticket Main Title -->
      <h1 class="hero-title"><?= esc($ticketTitle) ?></h1>

      <!-- Meta Info Row -->
      <div class="hero-meta-row">
        <div class="meta-item">
          <i class="far fa-user"></i>
          <span>Pelapor: <strong><?= esc($reporter['name']) ?></strong><?php if (!empty($reporter['department'])): ?> (<?= esc($reporter['department']) ?>)<?php endif; ?></span>
        </div>
        <div class="meta-item">
          <i class="far fa-calendar-alt"></i>
          <span>Diajukan: <strong><?= esc($createdFormatted) ?></strong></span>
        </div>
        <div class="meta-item">
          <i class="far fa-clock"></i>
          <span>Terakhir diperbarui: <strong><?= esc($timeAgoUpdated) ?></strong></span>
        </div>
      </div>
    </div>

    <!-- SLA Target Box (Amber Accent) -->
    <div class="hero-sla-box">
      <div class="sla-icon-badge">
        <i class="far fa-clock"></i>
      </div>
      <div class="sla-text-content">
        <div class="sla-label"><?= esc($sla['target_label']) ?></div>
        <div class="sla-value"><?= esc($sla['remaining_text']) ?></div>
      </div>
    </div>
  </div>

  <!-- 3. Two-Column Main Layout Grid -->
  <div class="ticket-detail-grid">

    <!-- LEFT COLUMN: Main Ticket Content & Thread -->
    <div class="ticket-left-col">

      <!-- Card 1: Problem Description & Initial Report -->
      <div class="detail-card desc-problem-card">
        <div class="card-header-clean">
          <div class="header-title-wrap">
            <div class="header-icon-circle icon-blue">
              <i class="far fa-file-alt"></i>
            </div>
            <h2>Deskripsi Masalah & Laporan Awal</h2>
          </div>
          <span class="header-badge-pill">PENGAJUAN TIKET #1</span>
        </div>

        <div class="card-body-clean">
          <!-- Text Body -->
          <div class="ticket-main-desc">
            <?php if (!empty($ticketDesc)): ?>
              <?= nl2br(esc($ticketDesc)) ?>
            <?php else: ?>
              <span class="has-text-grey is-italic">Tidak ada deskripsi rincian yang dilampirkan pada tiket ini.</span>
            <?php endif; ?>
          </div>

          <!-- Attachments Section (Hanya tampil jika ada berkas di DB) -->
          <?php if (!empty($attachments)): ?>
            <div class="attachments-section">
              <div class="attachments-headline">
                <span class="headline-title">
                  <i class="fas fa-paperclip"></i> Lampiran Pendukung (<?= count($attachments) ?> Berkas)
                </span>
                <span class="headline-total">Total: <?= esc($totalAttachmentSize) ?></span>
              </div>

              <div class="attachments-grid">
                <?php foreach ($attachments as $att): ?>
                  <div class="attachment-item-card">
                    <div class="att-icon-box <?= $att['is_image'] ? 'icon-image' : ($att['is_pdf'] ? 'icon-pdf' : 'icon-doc') ?>">
                      <?php if ($att['is_image']): ?>
                        <i class="fas fa-image"></i>
                      <?php elseif ($att['is_pdf']): ?>
                        <i class="fas fa-file-pdf"></i>
                      <?php else: ?>
                        <i class="far fa-file-alt"></i>
                      <?php endif; ?>
                    </div>

                    <div class="att-info-box">
                      <div class="att-file-name" title="<?= esc($att['file_name']) ?>">
                        <?= esc($att['file_name']) ?>
                      </div>
                      <div class="att-file-meta">
                        <?= esc($att['file_size_formatted']) ?> &bull; <?= esc($att['file_type_label']) ?>
                      </div>
                      <div class="att-actions-row">
                        <?php if ($att['is_image'] || $att['is_pdf']): ?>
                          <a href="<?= esc($att['preview_url']) ?>" target="_blank" rel="noopener">Pratinjau</a>
                          <span class="bullet-sep">&bull;</span>
                        <?php else: ?>
                          <a href="<?= esc($att['preview_url']) ?>" target="_blank" rel="noopener">Buka File</a>
                          <span class="bullet-sep">&bull;</span>
                        <?php endif; ?>
                        <a href="<?= esc($att['download_url']) ?>" download>Unduh</a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card 2: Activity History & Discussion -->
      <div class="detail-card activity-history-card">
        <div class="card-header-clean">
          <div class="header-title-wrap">
            <div class="header-icon-circle icon-teal">
              <i class="fas fa-comments"></i>
            </div>
            <h2>AKTIVITAS PENANGANAN & DISKUSI</h2>
          </div>
          <span class="header-badge-pill"><?= esc($historyCount) ?> Catatan Riwayat</span>
        </div>

        <div class="card-body-clean">
          <!-- Timeline Log Entries -->
          <div class="timeline-event-list">
            <?php foreach ($timelineEvents as $ev): ?>
              <div class="timeline-event-item">
                <div class="event-dot-icon">
                  <i class="far fa-clock"></i>
                </div>
                <div class="event-text">
                  <?= $ev['text'] ?>
                </div>
                <div class="event-time"><?= esc($ev['time']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Messages / Responses Thread -->
          <?php if (!empty($responses)): ?>
            <?php foreach ($responses as $msg): ?>
              <div class="response-message-card <?= $msg['is_staff'] ? 'message-staff' : 'message-user' ?>" id="msg-<?= esc($msg['id']) ?>">
                <div class="message-header">
                  <div class="author-info-wrap">
                    <div class="author-avatar-wrap">
                      <?php if ($msg['is_staff']): ?>
                        <div class="avatar-placeholder">
                          <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="online-indicator" title="Staff Helpdesk"></span>
                      <?php else: ?>
                        <div class="avatar-placeholder" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                          <i class="fas fa-user"></i>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="author-text">
                      <div class="name-row">
                        <span class="author-name"><?= esc($msg['author_name']) ?></span>
                        <span class="role-badge"><?= esc($msg['role_label']) ?></span>
                      </div>
                      <div class="author-subtitle">
                        <?= esc($msg['job_title']) ?> &bull; <?= esc($msg['time_ago']) ?>
                      </div>
                    </div>
                  </div>

                  <div class="message-timestamp">
                    <?= esc($msg['time_formatted']) ?>
                  </div>
                </div>

                <!-- Message Body -->
                <div class="message-body-text">
                  <?= nl2br(esc($msg['reply'])) ?>
                </div>

                <!-- Footer with Quote Action -->
                <div class="message-footer">
                  <button type="button" class="btn-quote-msg" data-quote="<?= esc($msg['reply']) ?>" data-author="<?= esc($msg['author_name']) ?>">
                    <i class="fas fa-reply"></i> Kutip Pesan Ini
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-discussion-state py-4 text-center" style="background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 12px; margin-top: 10px;">
              <i class="far fa-comments fa-2x" style="color: #cbd5e1; margin-bottom: 8px; display: inline-block;"></i>
              <p style="font-size: 13.5px; font-weight: 600; color: #475569; margin: 0 0 2px;">Belum Ada Balasan Diskusi</p>
              <p style="font-size: 12px; color: #94a3b8; margin: 0;">Gunakan formulir di bawah ini jika Anda ingin menambahkan informasi atau menanyakan progres tiket.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card 3: Reply / Confirmation Form -->
      <div class="detail-card reply-form-card">
        <div class="card-body-clean">
          <div class="reply-header-info">
            <div class="header-titles">
              <div class="reply-icon-box">
                <i class="fas fa-comment-dots"></i>
              </div>
              <div>
                <h2>Beri Tanggapan / Konfirmasi</h2>
                <p>Tuliskan tanggapan, pertanyaan, atau informasi tambahan untuk petugas HC.</p>
              </div>
            </div>
            <span class="urgent-reply-badge">Respons Tiket</span>
          </div>

          <form id="ticketReplyForm" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Rich Text Editor Toolbar -->
            <div class="editor-toolbar">
              <div class="toolbar-buttons">
                <button type="button" class="btn-tool" data-action="bold" title="Tebal (Ctrl+B)"><b>B</b></button>
                <button type="button" class="btn-tool" data-action="italic" title="Miring (Ctrl+I)"><i>I</i></button>
                <button type="button" class="btn-tool" data-action="strike" title="Coret"><s>S</s></button>
                <button type="button" class="btn-tool" data-action="ul" title="Daftar Poin"><i class="fas fa-list-ul"></i></button>
                <button type="button" class="btn-tool" data-action="ol" title="Daftar Nomor"><i class="fas fa-list-ol"></i></button>
                <button type="button" class="btn-tool" data-action="link" title="Sisipkan Tautan"><i class="fas fa-link"></i></button>
                <button type="button" class="btn-tool" data-action="code" title="Format Kode"><i class="fas fa-code"></i></button>
              </div>
              <div class="toolbar-hint">Format teks didukung</div>
            </div>

            <!-- Textarea for Reply -->
            <div class="reply-textarea-wrap">
              <textarea
                class="reply-textarea"
                id="replyTextarea"
                name="reply"
                rows="4"
                placeholder="Tuliskan respon atau informasi tambahan di sini..."
              ></textarea>
            </div>

            <!-- Selected Files List Container -->
            <div id="selectedFilesContainer"></div>

            <!-- Dropzone File Upload Area -->
            <div class="reply-dropzone-box" id="replyDropzone">
              <div class="dropzone-icon-circle">
                <i class="fas fa-cloud-upload-alt"></i>
              </div>
              <div class="dropzone-prompt-text">
                Tarik file dokumen pendukung ke sini, atau <span class="highlight">jelajahi file</span>
              </div>
              <div class="dropzone-sub-hint">
                Mendukung PNG, JPG, PDF, TXT atau LOG (Maks. 10MB per file)
              </div>
              <input type="file" id="replyFileInput" name="attachments[]" multiple style="display: none;" accept=".png,.jpg,.jpeg,.webp,.pdf,.txt,.log,.docx,.xlsx">
            </div>

            <!-- Footer: Security Info & Action Buttons -->
            <div class="reply-form-footer">
              <div class="security-note">
                <i class="fas fa-lock"></i>
                <span>Respon Anda terenkripsi dan disimpan aman dalam tiket internal.</span>
              </div>

              <div class="form-actions-btns">
                <button type="button" class="btn-draft" id="btnSaveDraft">
                  Simpan Draf
                </button>
                <button type="submit" class="btn-submit-reply" id="btnSubmitReply">
                  <span>Kirim Balasan & Lanjutkan Proses</span>
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: Sidebar Widgets -->
    <div class="ticket-right-col">

      <!-- Widget 1: Penanganan & PIC (Gambar 4 - Dinamis) -->
      <div class="sidebar-widget widget-pic-profile">
        <div class="widget-header">
          <h3 class="widget-title">PENANGANAN & PIC</h3>
          <?php if (!empty($pic['is_assigned'])): ?>
            <span class="widget-badge badge-online">Ditugaskan</span>
          <?php else: ?>
            <span class="widget-badge" style="color: #64748b; font-weight: 500;">Dalam Antrean</span>
          <?php endif; ?>
        </div>

        <div class="pic-card-box">
          <div class="pic-avatar">
            <?php if (!empty($pic['is_assigned'])): ?>
              <div class="avatar-placeholder">
                <?= esc($pic['initials'] ?: 'HC') ?>
              </div>
            <?php else: ?>
              <div class="avatar-placeholder" style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0;">
                <i class="fas fa-user-clock"></i>
              </div>
            <?php endif; ?>
          </div>
          <div class="pic-meta-info">
            <div class="pic-name"><?= esc($pic['name']) ?></div>
            <div class="pic-role"><?= esc($pic['role']) ?></div>
            <div class="pic-dept"><?= esc($pic['dept']) ?></div>
          </div>
        </div>

        <div class="pic-actions-box">
          <?php if (!empty($pic['is_assigned']) && !empty($pic['teams_url'])): ?>
            <a href="<?= esc($pic['teams_url']) ?>" target="_blank" rel="noopener" class="btn-teams-chat">
              <i class="fab fa-microsoft"></i> Hubungi via Microsoft Teams
            </a>
          <?php endif; ?>

          <?php if (!empty($pic['is_assigned']) && !empty($pic['email'])): ?>
            <a href="mailto:<?= esc($pic['email']) ?>" class="btn-email-link">
              <i class="far fa-envelope mr-1"></i> Kirim Email Langsung
            </a>
          <?php endif; ?>

          <?php if (empty($pic['is_assigned'])): ?>
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px; text-align: center; font-size: 12px; color: #64748b;">
              <i class="fas fa-info-circle mr-1 text-primary"></i> Tiket sedang menunggu penugasan petugas spesialis HC.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Widget 2: Pelacakan Tahapan (Gambar 3 - Dinamis) -->
      <div class="sidebar-widget widget-stepper-tracker">
        <div class="widget-header">
          <h3 class="widget-title">PELACAKAN TAHAPAN</h3>
          <span class="widget-badge badge-sla-time"><?= esc($sla['remaining_badge']) ?></span>
        </div>

        <!-- 5-Step Stepper -->
        <div class="stepper-timeline-list">
          <?php foreach ($stepperSteps as $st): ?>
            <div class="stepper-node-item">
              <div class="node-icon-bullet node-<?= esc($st['status']) ?>">
                <?php if ($st['status'] === 'completed'): ?>
                  <i class="fas fa-check"></i>
                <?php elseif ($st['status'] === 'active'): ?>
                  <i class="fas fa-hourglass-half"></i>
                <?php else: ?>
                  <span><?= esc($st['step']) ?></span>
                <?php endif; ?>
              </div>
              <div class="node-title"><?= esc($st['title']) ?></div>
              <div class="node-subtext"><?= esc($st['subtext']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- SLA Commitment & Progress Bar -->
        <div class="sla-commitment-footer">
          <div class="sla-progress-labels">
            <span><?= esc($sla['commitment_text']) ?></span>
            <span class="sla-spent-bold"><?= esc($sla['progress_label']) ?></span>
          </div>
          <div class="sla-progress-bar-bg">
            <div class="sla-progress-fill" style="width: <?= esc($sla['progress_percent']) ?>%;"></div>
          </div>
        </div>
      </div>

      <!-- Widget 3: Pusat Bantuan Terkait & Emergency Hotline -->
      <div class="sidebar-widget widget-related-faqs">
        <div class="widget-header">
          <h3 class="widget-title">PUSAT BANTUAN TERKAIT</h3>
          <i class="far fa-question-circle text-muted"></i>
        </div>

        <div class="faq-links-list">
          <?php foreach ($relatedFaqs as $faq): ?>
            <a href="<?= esc($faq['url']) ?>" class="faq-item-link">
              <div class="faq-link-title">
                <span><?= esc($faq['title']) ?></span>
                <i class="fas fa-chevron-right"></i>
              </div>
              <div class="faq-link-desc"><?= esc($faq['desc']) ?></div>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Hotline Card -->
        <div class="hotline-emergency-banner">
          <div class="hotline-star-icon">
            <i class="fas fa-headset"></i>
          </div>
          <div class="hotline-text-wrap">
            <div class="hotline-title">Butuh Bantuan Mendesak?</div>
            <div class="hotline-number">Hubungi Hotline HC Darurat: <strong>Ext. 4410</strong></div>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>

<!-- Modal: Konfirmasi Tutup Tiket -->
<div class="modal" id="modalCloseTicket">
  <div class="modal-background" onclick="closeCloseTicketModal()"></div>
  <div class="modal-card" style="max-width: 480px; border-radius: 16px; overflow: hidden;">
    <header class="modal-card-head" style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 20px 24px;">
      <p class="modal-card-title" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
        <i class="fas fa-exclamation-triangle text-warning mr-2"></i> Konfirmasi Penutupan Tiket
      </p>
      <button class="delete" aria-label="close" onclick="closeCloseTicketModal()"></button>
    </header>
    <section class="modal-card-body" style="padding: 24px;">
      <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
        Apakah kendala Anda pada tiket <strong>#<?= esc($ticketNo) ?></strong> telah terselesaikan? Menutup tiket menandakan bahwa bantuan lebih lanjut tidak lagi dibutuhkan.
      </p>
      <div class="field">
        <label class="label" style="font-size: 13px; font-weight: 600;">Catatan Penutupan (Opsional):</label>
        <div class="control">
          <textarea id="closeTicketReason" class="textarea" rows="3" placeholder="Contoh: Kendala sudah terselesaikan. Terima kasih."></textarea>
        </div>
      </div>
    </section>
    <footer class="modal-card-foot" style="background: #f8fafc; border-top: 1px solid #e2e8f0; justify-content: flex-end; gap: 10px; padding: 14px 24px;">
      <button type="button" class="button" onclick="closeCloseTicketModal()" style="border-radius: 8px;">Batal</button>
      <button type="button" class="button is-danger" id="btnConfirmCloseTicket" style="border-radius: 8px;">
        <i class="fas fa-check mr-1"></i> Ya, Tutup Tiket
      </button>
    </footer>
  </div>
</div>

<!-- Client Interaction Script -->
<script src="<?= base_url('assets/js/modules/ticket-detail-user.js?v=' . time()) ?>"></script>

<?= $this->endSection() ?>
