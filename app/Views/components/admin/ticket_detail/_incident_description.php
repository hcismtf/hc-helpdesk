<?php
/**
 * Ticket Detail Component: Incident Description & Attachments
 *
 * Menampilkan pesan/laporan deskripsi awal pengajuan tiket dan daftar lampiran file.
 *
 * @var array $ticket      Data tiket utama
 * @var array $lifecycle   Informasi tanggal pembuatan
 * @var array $attachments Daftar file lampiran
 */
?>
<div class="incident-description-card">
    <div class="incident-card-header">
        <div class="incident-header-left">
            <div class="incident-icon-pill">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div>
                <h3 class="incident-title-text">Deskripsi Laporan Pengaduan</h3>
                <span class="incident-sub-text"><?= esc($lifecycle['created']) ?> WIB &bull; Dilaporkan oleh <?= esc($ticket['reporter_name'] ?? ($ticket['emp_name'] ?? 'User')) ?></span>
            </div>
        </div>
        <span
            class="incident-tag-code"><?= esc(!empty($ticket['ticket_no']) ? $ticket['ticket_no'] : ('INC-' . $ticket['id'])) ?></span>
    </div>

    <div class="incident-body-content">
        <?php
        $descText = !empty($ticket['description']) ? $ticket['description'] : (!empty($ticket['message']) ? $ticket['message'] : '');
        ?>
        <?= !empty($descText) ? nl2br(esc($descText)) : '<span style="color:#94a3b8; font-style:italic;">No description provided.</span>' ?>
    </div>

    <!-- Attachments Block -->
    <?php if (!empty($attachments)): ?>
        <div class="attachments-wrapper">
            <div class="attachments-header-row">
                <span class="attachments-count-title">Attachments (<?= count($attachments) ?> Files)</span>
            </div>
            <div class="attachments-cards-grid">
                <?php foreach ($attachments as $att): ?>
                    <div class="attachment-file-card">
                        <div class="attachment-file-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="13 2 13 9 20 9"></polyline>
                            </svg>
                        </div>
                        <div class="attachment-file-info">
                            <div class="attachment-file-name" title="<?= esc($att['file_name']) ?>">
                                <?= esc($att['file_name']) ?></div>
                            <div class="attachment-file-meta">Attachment File</div>
                            <div class="attachment-card-links">
                                <?php
                                $rawPath = ltrim($att['file_path'] ?? '', '/\\');
                                $viewUrl = base_url('attachment/view/' . $rawPath);
                                $downloadUrl = base_url('attachment/download/' . $rawPath);
                                ?>
                                <a href="javascript:void(0);" onclick="showAttachmentModal('<?= $viewUrl ?>')">Preview</a>
                                <span>&bull;</span>
                                <a href="<?= $downloadUrl ?>">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>