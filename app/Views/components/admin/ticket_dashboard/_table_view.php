<?php
/**
 * Ticket Dashboard Table View Component
 *
 * Menampilkan daftar tiket menggunakan table modern informatif:
 * - Title & Requester (Submitted by)
 * - PIC Helpdesk
 * - PIC Developer / BA
 * - Request Category
 * - Time Left (Real-time dynamic frontend countdown)
 * - Status (Quick select dropdown)
 * - SLA (Quick select dropdown)
 * - Action (Button Open / Reply)
 *
 * @var array  $tickets
 * @var int    $perPage
 * @var string $paginationHTML
 * @var array  $slaList
 */
$tickets = $tickets ?? [];
$perPage = $perPage ?? 12;
$paginationHTML = $paginationHTML ?? '';
$slaList = $slaList ?? [];

// Status definitions & colors aligned with Domain Entity TicketStatus
$rawStatusDefs = \App\Entities\TicketStatus::getDefinitions();
$statusDefs = [];
$statusOptions = [];
foreach ($rawStatusDefs as $sCode => $sDef) {
    $statusDefs[$sCode] = [
        'label' => $sDef['name'] ?? ucfirst($sCode),
        'class' => 'status-' . $sCode,
    ];
    $statusOptions[$sCode] = $sDef['name'] ?? ucfirst($sCode);
}

// SLA Categories / priorities dynamically built from database table 'sla'
$slaDefs = [];
$slaOptions = [];
if (!empty($slaList)) {
    foreach ($slaList as $s) {
        $pName = is_array($s) ? ($s['priority'] ?? '') : ($s->priority ?? '');
        $pKey  = strtolower(trim($pName));
        if ($pKey !== '') {
            $slaDefs[$pKey] = [
                'label' => ucfirst($pName),
                'class' => 'priority-' . ucfirst($pName)
            ];
            $slaOptions[$pKey] = ucfirst($pName);
        }
    }
}
// Fallback if SLA table is empty
if (empty($slaOptions)) {
    $slaDefs = [
        'urgent' => ['label' => 'Urgent', 'class' => 'priority-Urgent'],
        'high'   => ['label' => 'High', 'class' => 'priority-High'],
        'medium' => ['label' => 'Medium', 'class' => 'priority-Medium'],
        'low'    => ['label' => 'Low', 'class' => 'priority-Low'],
    ];
    $slaOptions = [
        'urgent' => 'Urgent',
        'high'   => 'High',
        'medium' => 'Medium',
        'low'    => 'Low',
    ];
}
?>
<div id="ticketTableView" class="ticket-view-section">
    <div class="tickets-box">
        <div class="table-responsive">
            <table class="tickets-table table-ticket-modern" id="ticketsTable">
                <thead>
                    <tr>
                        <th style="min-width: 240px;">Title & Requester</th>
                        <th style="min-width: 150px;">PIC Helpdesk</th>
                        <th style="min-width: 150px;">PIC Developer / BA</th>
                        <th style="min-width: 140px;">Request Category</th>
                        <th style="min-width: 130px;">Time Left</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 130px;">SLA Category</th>
                        <th style="text-align: right; min-width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $t): 
                            $ticketId = $t['id'] ?? '';
                            $title = !empty($t['title']) ? $t['title'] : (!empty($t['subject']) ? $t['subject'] : 'Untitled Ticket');
                            $ticketNo = $t['ticket_no'] ?? substr($ticketId, 0, 8);
                            $requesterName = $t['emp_name'] ?? 'Karyawan';
                            $requesterNip = !empty($t['emp_nip']) && $t['emp_nip'] !== '-' ? $t['emp_nip'] : '';
                            
                            $picHelpdesk = $t['pic_helpdesk_name'] ?? 'Unassigned';
                            $picDevBa    = $t['pic_dev_ba_name'] ?? 'Unassigned';
                            $category    = $t['req_type'] ?? 'HC Helpdesk';
                            
                            $rawStatus   = strtolower((string)($t['ticket_status'] ?? $t['status'] ?? 'open'));
                            $curStatus   = isset($statusDefs[$rawStatus]) ? $rawStatus : 'open';
                            
                            $rawSla      = strtolower((string)($t['ticket_priority'] ?? $t['sla_status'] ?? 'medium'));
                            $curSla      = isset($slaDefs[$rawSla]) ? $rawSla : 'medium';
                            
                            $secondsLeft = isset($t['seconds_left']) && $t['seconds_left'] !== null ? (int)$t['seconds_left'] : null;
                            $dueDate     = $t['due_date'] ?? $t['resolution_due_date'] ?? null;
                            $isResolved  = in_array($curStatus, ['closed', 'done', 'resolved']);
                        ?>
                            <tr id="table-row-<?= esc($ticketId) ?>">
                                <!-- 1. Judul Ticket & Requester -->
                                <td>
                                    <div class="ticket-cell-title-group">
                                        <a href="<?= base_url('admin/ticket_detail/' . esc($ticketId)) ?>" class="ticket-table-title" title="<?= esc($title) ?>">
                                            <?= esc($title) ?>
                                        </a>
                                        <div class="ticket-table-sub">
                                            <span class="sub-ticket-no">#<?= esc($ticketNo) ?></span>
                                            <span class="sub-dot">&bull;</span>
                                            <span class="sub-requester">
                                                Submitted by <strong><?= esc($requesterName) ?></strong>
                                                <?php if (!empty($requesterNip)): ?>
                                                    (<?= esc($requesterNip) ?>)
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. PIC Helpdesk -->
                                <td>
                                    <div class="pic-pill-cell <?= $picHelpdesk === 'Unassigned' ? 'is-unassigned' : '' ?>">
                                        <span class="pic-dot"></span>
                                        <span class="pic-name" title="<?= esc($picHelpdesk) ?>"><?= esc($picHelpdesk) ?></span>
                                    </div>
                                </td>

                                <!-- 3. PIC Developer / BA -->
                                <td>
                                    <div class="pic-pill-cell <?= $picDevBa === 'Unassigned' ? 'is-unassigned' : '' ?>">
                                        <span class="pic-dot dot-dev"></span>
                                        <span class="pic-name" title="<?= esc($picDevBa) ?>"><?= esc($picDevBa) ?></span>
                                    </div>
                                </td>

                                <!-- 4. Request Category -->
                                <td>
                                    <span class="table-category-pill" title="<?= esc($category) ?>">
                                        <span class="cat-dot"></span>
                                        <span class="cat-text"><?= esc($category) ?></span>
                                    </span>
                                </td>

                                <!-- 5. Time Left (Client-side countdown) -->
                                <td>
                                    <div class="time-left-cell">
                                        <?php if ($isResolved): ?>
                                            <span class="time-badge time-badge-done">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <span>Resolved</span>
                                            </span>
                                        <?php elseif ($secondsLeft !== null): ?>
                                            <span class="time-badge countdown-pill <?= $secondsLeft < 0 ? 'is-overdue' : ($secondsLeft < 7200 ? 'is-warning' : '') ?>" 
                                                  id="countdown-<?= esc($ticketId) ?>" 
                                                  data-seconds-left="<?= (int)$secondsLeft ?>"
                                                  data-ticket-id="<?= esc($ticketId) ?>">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                <span class="countdown-text">Calculating...</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:12px;">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- 6. Status (Dropdown via Global Component) -->
                                <td>
                                    <div class="table-cell-select-container">
                                        <?= view('components/common/_basic_select', [
                                            'name'          => 'status_' . $ticketId,
                                            'id'            => 'status-select-' . $ticketId,
                                            'placeholder'   => 'Select Status',
                                            'selectedValue' => $curStatus,
                                            'options'       => $statusOptions,
                                            'onChange'      => "updateTicketStatusFromTable('" . esc($ticketId) . "', value)",
                                            'extraClass'    => 'table-custom-select table-status-custom-select'
                                        ]) ?>
                                    </div>
                                </td>

                                <!-- 7. SLA Category (Dropdown via Global Component) -->
                                <td>
                                    <div class="table-cell-select-container">
                                        <?= view('components/common/_basic_select', [
                                            'name'          => 'sla_' . $ticketId,
                                            'id'            => 'sla-select-' . $ticketId,
                                            'placeholder'   => 'Select SLA',
                                            'selectedValue' => $curSla,
                                            'options'       => $slaOptions,
                                            'onChange'      => "updateTicketSlaFromTable('" . esc($ticketId) . "', value)",
                                            'extraClass'    => 'table-custom-select table-sla-custom-select'
                                        ]) ?>
                                    </div>
                                </td>

                                <!-- 8. Action Button (Detail / Reply) -->
                                <td style="text-align: right;">
                                    <a href="<?= base_url('admin/ticket_detail/' . esc($ticketId)) ?>" class="btn-detail-reply" title="Detail & Reply Ticket">
                                        <span>Detail</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center; color:#94a3b8; padding:36px 16px;">
                                No tickets found matching the criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($paginationHTML)): ?>
            <div class="table-pagination-wrapper">
                <?= $paginationHTML ?>
            </div>
        <?php endif; ?>
    </div>
</div>
