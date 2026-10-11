<?php
/**
 * Ticket Detail Component: Top Action Bar
 * 
 * Baris perintah atas: Kembali ke antrean tiket, Assign to Me, Interactive Status Pill, Escalate, & Action Dropdown.
 *
 * @var array       $ticket             Data tiket utama
 * @var string      $uniqueUrl          Token unik URL tiket
 * @var string      $statusKey          Status key normalisasi
 * @var string      $currentStatusLabel Label status saat ini
 * @var array       $statusDefinitions  Definisi status tiket
 */
?>
<div class="detail-top-bar">
    <div class="detail-breadcrumb-group">
        <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="btn-back-queue">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Ticket Queue</span>
            <span class="kbd-esc">ESC</span>
        </a>
    </div>

    <div class="detail-actions-right">
        <!-- Quick Assign Me -->
        <button type="button" class="btn-command" onclick="quickAssignToMe('<?= session('user_id') ?>')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="22" y1="11" x2="16" y2="11"></line>
            </svg>
            <span>Assign to Me</span>
        </button>

        <!-- Interactive Status Dropdown -->
        <div class="status-dropdown-wrapper" id="ticketStatusDropdownWrapper">
            <button type="button" 
                    class="status-select-pill status-dropdown-trigger status-<?= esc($statusKey) ?>" 
                    id="ticketStatusTriggerBtn" 
                    onclick="toggleTicketStatusDropdown(event)" 
                    title="Klik untuk mengubah status tiket"
                    aria-haspopup="true" 
                    aria-expanded="false">
                <span class="status-dot" id="ticketStatusDot"></span>
                <span id="ticketStatusTriggerText">Status: <strong><?= esc($currentStatusLabel) ?></strong></span>
                <svg class="status-chevron-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="status-dropdown-menu" id="ticketStatusDropdownMenu" role="menu">
                <div class="status-dropdown-header">Ubah Status Tiket</div>
                <?php foreach ($statusDefinitions as $sCode => $sDef): ?>
                    <?php $isActive = ($sCode === $statusKey); ?>
                    <button type="button" 
                            class="status-dropdown-item <?= $isActive ? 'is-selected' : '' ?>" 
                            data-status="<?= esc($sCode) ?>"
                            onclick="changeTicketStatusQuick('<?= esc($sCode) ?>', '<?= esc($sDef['label']) ?>')">
                        <div class="status-item-left">
                            <span class="status-item-dot <?= esc($sDef['dotClass']) ?>"></span>
                            <span><?= esc($sDef['label']) ?></span>
                        </div>
                        <svg class="status-item-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Escalate Button -->
        <button type="button" class="btn-command btn-escalate" onclick="openEscalateModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Escalate</span>
        </button>

        <!-- Reusable Action Dropdown Menu -->
        <?= view('components/common/_action_dropdown', [
            'id' => 'detail_top_action',
            'items' => [
                [
                    'label'   => 'Change Ticket Status',
                    'icon'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>',
                    'onClick' => 'showReplyStatusModal()'
                ],
                [
                    'label'   => 'Print / Export PDF',
                    'icon'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>',
                    'onClick' => 'window.print()'
                ]
            ]
        ]) ?>
    </div>
</div>
