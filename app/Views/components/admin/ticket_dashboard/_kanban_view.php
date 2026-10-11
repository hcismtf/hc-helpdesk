<?php
/**
 * Ticket Dashboard Kanban Swimlane View Component
 *
 * Horizontal drag-and-drop swimlanes:
 * - Open Lane (Oldest first)
 * - In Progress Lane (Oldest first)
 * - Closed / Done Lane (Newest first)
 * With per-area date filter and pagination
 *
 * @var array $kanbanTickets
 * @var array $tickets
 */

$swimlaneDataSource = $kanbanTickets ?? $tickets ?? [];

$openTicketsList = [];
$inProgressTicketsList = [];
$closedTicketsList = [];

if (!empty($swimlaneDataSource)) {
    foreach ($swimlaneDataSource as $t) {
        $st = strtolower(trim($t['ticket_status'] ?? 'open'));
        if ($st === 'in_progress') {
            $inProgressTicketsList[] = $t;
        } elseif ($st === 'closed' || $st === 'done') {
            $closedTicketsList[] = $t;
        } else {
            $openTicketsList[] = $t;
        }
    }
}

// 1. Area Open: Sort by created_date ASC (terlama di awal)
usort($openTicketsList, function($a, $b) {
    $tA = !empty($a['created_date']) ? strtotime($a['created_date']) : 0;
    $tB = !empty($b['created_date']) ? strtotime($b['created_date']) : 0;
    return $tA <=> $tB;
});

// 2. Area In Progress: Sort by created_date ASC (terlama di awal)
usort($inProgressTicketsList, function($a, $b) {
    $tA = !empty($a['created_date']) ? strtotime($a['created_date']) : 0;
    $tB = !empty($b['created_date']) ? strtotime($b['created_date']) : 0;
    return $tA <=> $tB;
});

// 3. Area Closed / Done: Sort by modified_date DESC (terbaru di awal)
usort($closedTicketsList, function($a, $b) {
    $mA = !empty($a['modified_date']) ? strtotime($a['modified_date']) : (!empty($a['finish_date']) ? strtotime($a['finish_date']) : (!empty($a['created_date']) ? strtotime($a['created_date']) : 0));
    $mB = !empty($b['modified_date']) ? strtotime($b['modified_date']) : (!empty($b['finish_date']) ? strtotime($b['finish_date']) : (!empty($b['created_date']) ? strtotime($b['created_date']) : 0));
    return $mB <=> $mA;
});
?>
<div id="ticketGridView" class="ticket-view-section" style="display:none;">
    <div class="kanban-board-container">

        <!-- 1. SWIMLANE: AREA OPEN -->
        <div class="kanban-swimlane lane-open" data-lane-status="open">
            <div class="swimlane-header">
                <div class="swimlane-title-group">
                    <span class="swimlane-indicator-dot dot-open"></span>
                    <span class="swimlane-title">Ticket Open</span>
                    <span class="swimlane-count-badge" id="count-open"><?= count($openTicketsList) ?> tickets</span>
                </div>
                <div class="swimlane-controls">
                    <!-- Filter Date per Area -->
                    <div class="swimlane-date-filter" title="Filter by date">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <select class="swimlane-date-select" id="date-filter-open" onchange="filterAreaByDate('open', this.value)">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">This Month</option>
                        </select>
                    </div>
                    <!-- Pagination per Area -->
                    <div class="swimlane-pagination">
                        <span class="swimlane-page-info" id="page-info-open">1 / 1</span>
                        <div class="swimlane-nav-arrows">
                            <button type="button" class="btn-swimlane-nav" id="btn-prev-open" onclick="changeAreaPage('open', -1)" title="Previous Page" disabled>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button type="button" class="btn-swimlane-nav" id="btn-next-open" onclick="changeAreaPage('open', 1)" title="Next Page">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swimlane-cards-track" id="track-open" data-lane-status="open">
                <?php if (!empty($openTicketsList)): ?>
                    <?php foreach ($openTicketsList as $ticket): ?>
                        <?= view('components/admin/ticket_dashboard/_kanban_card', ['ticket' => $ticket]) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swimlane-empty-placeholder">Drag ticket here to set status to Open</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2. SWIMLANE: AREA IN PROGRESS -->
        <div class="kanban-swimlane lane-in-progress" data-lane-status="in_progress">
            <div class="swimlane-header">
                <div class="swimlane-title-group">
                    <span class="swimlane-indicator-dot dot-in-progress"></span>
                    <span class="swimlane-title">Ticket In Progress</span>
                    <span class="swimlane-count-badge" id="count-in_progress"><?= count($inProgressTicketsList) ?> tickets</span>
                </div>
                <div class="swimlane-controls">
                    <!-- Filter Date per Area -->
                    <div class="swimlane-date-filter" title="Filter by date">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <select class="swimlane-date-select" id="date-filter-in_progress" onchange="filterAreaByDate('in_progress', this.value)">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">This Month</option>
                        </select>
                    </div>
                    <!-- Pagination per Area -->
                    <div class="swimlane-pagination">
                        <span class="swimlane-page-info" id="page-info-in_progress">1 / 1</span>
                        <div class="swimlane-nav-arrows">
                            <button type="button" class="btn-swimlane-nav" id="btn-prev-in_progress" onclick="changeAreaPage('in_progress', -1)" title="Previous Page" disabled>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button type="button" class="btn-swimlane-nav" id="btn-next-in_progress" onclick="changeAreaPage('in_progress', 1)" title="Next Page">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swimlane-cards-track" id="track-in_progress" data-lane-status="in_progress">
                <?php if (!empty($inProgressTicketsList)): ?>
                    <?php foreach ($inProgressTicketsList as $ticket): ?>
                        <?= view('components/admin/ticket_dashboard/_kanban_card', ['ticket' => $ticket]) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swimlane-empty-placeholder">Drag ticket here to set status to In Progress</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. SWIMLANE: AREA CLOSED / DONE -->
        <div class="kanban-swimlane lane-closed" data-lane-status="closed">
            <div class="swimlane-header">
                <div class="swimlane-title-group">
                    <span class="swimlane-indicator-dot dot-closed"></span>
                    <span class="swimlane-title">Ticket Closed / Done</span>
                    <span class="swimlane-count-badge" id="count-closed"><?= count($closedTicketsList) ?> tickets</span>
                </div>
                <div class="swimlane-controls">
                    <!-- Filter Date per Area -->
                    <div class="swimlane-date-filter" title="Filter by date">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <select class="swimlane-date-select" id="date-filter-closed" onchange="filterAreaByDate('closed', this.value)">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">This Month</option>
                        </select>
                    </div>
                    <!-- Pagination per Area -->
                    <div class="swimlane-pagination">
                        <span class="swimlane-page-info" id="page-info-closed">1 / 1</span>
                        <div class="swimlane-nav-arrows">
                            <button type="button" class="btn-swimlane-nav" id="btn-prev-closed" onclick="changeAreaPage('closed', -1)" title="Previous Page" disabled>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button type="button" class="btn-swimlane-nav" id="btn-next-closed" onclick="changeAreaPage('closed', 1)" title="Next Page">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swimlane-cards-track" id="track-closed" data-lane-status="closed">
                <?php if (!empty($closedTicketsList)): ?>
                    <?php foreach ($closedTicketsList as $ticket): ?>
                        <?= view('components/admin/ticket_dashboard/_kanban_card', ['ticket' => $ticket]) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swimlane-empty-placeholder">Drag ticket here to set status to Closed / Done</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
