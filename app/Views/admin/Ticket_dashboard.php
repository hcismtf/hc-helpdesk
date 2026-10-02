<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Management - Helpdesk Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/Ticket_dashboard.css') ?>?v=<?= time() ?>">
</head>
<body>

    <?php $active = 'tickets'; include('navbar.php'); ?>
    
    <div class="main-content" id="main-content">
        <!-- Top Header Component -->
        <?= view('components/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => 'Ticket Management',
            'pageTitle'        => 'Ticket List',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <!-- Modern Linear / Jira Style Filter & Control Strip -->
        <div class="ticket-control-strip">
            <!-- Top Row: Quick Search + Filter Dropdowns -->
            <div class="control-strip-top">
                <div class="quick-search-box">
                    <svg class="search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="ticketSearchInput" placeholder="Search ticket key, summary, or requester... (⌘K)" value="<?= esc($search ?? '') ?>" onkeydown="if(event.key==='Enter') applyFilters()">
                    <span class="search-shortcut-pill">⌘K</span>
                </div>

                <div class="strip-dropdowns">
                    <!-- Priority Dropdown -->
                    <div class="strip-select-wrap">
                        <label class="select-prefix">Priority:</label>
                        <select id="filterPriority" onchange="applyFilters()">
                            <option value="">All</option>
                            <option value="urgent" <?= ($priority ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            <option value="high" <?= ($priority ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                            <option value="medium" <?= ($priority ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="low" <?= ($priority ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>

                    <!-- Type / Category Dropdown -->
                    <div class="strip-select-wrap">
                        <label class="select-prefix">Type:</label>
                        <select id="filterType" onchange="applyFilters()">
                            <option value="">All Categories</option>
                            <?php if (!empty($requestTypes)): ?>
                                <?php foreach ($requestTypes as $rt): ?>
                                    <option value="<?= esc($rt['name']) ?>" <?= ($type ?? '') === $rt['name'] ? 'selected' : '' ?>><?= esc($rt['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="strip-select-wrap">
                        <label class="select-prefix">Status:</label>
                        <select id="filterStatus" onchange="applyFilters()">
                            <option value="">All</option>
                            <option value="open" <?= ($status ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="done" <?= ($status ?? '') === 'done' ? 'selected' : '' ?>>Done</option>
                            <option value="closed" <?= ($status ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <!-- Showing Per Page -->
                    <div class="strip-select-wrap">
                        <label class="select-prefix">Show:</label>
                        <select id="filterPerPage" onchange="handlePerPageChange(this.value)">
                            <option value="6" <?= (int)($perPage ?? 12) === 6 ? 'selected' : '' ?>>6</option>
                            <option value="10" <?= (int)($perPage ?? 12) === 10 ? 'selected' : '' ?>>10</option>
                            <option value="12" <?= (int)($perPage ?? 12) === 12 ? 'selected' : '' ?>>12</option>
                            <option value="20" <?= (int)($perPage ?? 12) === 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= (int)($perPage ?? 12) === 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Quick Filter Pills & View Switcher -->
            <div class="control-strip-bottom">
                <div class="quick-filters-group">
                    <span class="quick-filters-label">QUICK FILTERS:</span>
                    <button type="button" class="quick-pill <?= empty($status) && empty($priority) && empty($search) ? 'active' : '' ?>" onclick="setQuickFilter('all')">All Tickets</button>
                    <button type="button" class="quick-pill <?= ($status ?? '') === 'open' ? 'active' : '' ?>" onclick="setQuickFilter('open')">Only Open</button>
                    <button type="button" class="quick-pill <?= ($status ?? '') === 'in_progress' ? 'active' : '' ?>" onclick="setQuickFilter('in_progress')">In Progress</button>
                    <button type="button" class="quick-pill expiring-sla <?= ($priority ?? '') === 'urgent' ? 'active' : '' ?>" onclick="setQuickFilter('urgent')">
                        <span class="red-dot"></span>
                        <span>Expiring SLA / Urgent</span>
                    </button>
                </div>

                <div class="strip-view-controls">
                    <div class="view-toggle-group">
                        <button type="button" class="btn-view-toggle active" id="btnViewTable" onclick="switchViewMode('table')" title="Table View">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <span>Table</span>
                        </button>
                        <button type="button" class="btn-view-toggle" id="btnViewGrid" onclick="switchViewMode('grid')" title="Kanban Swimlane Board">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span>Kanban Grid</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. TABLE VIEW CONTAINER -->
        <div id="ticketTableView" class="ticket-view-section">
            <?= view('components/data_table', [
                'title'          => 'All Tickets',
                'tableId'        => 'ticketsTable',
                'showControls'   => false, // Controls are in the top strip
                'perPage'        => $perPage,
                'columns'        => [
                    'id'              => 'Ticket ID',
                    'emp_nip'         => 'NIP',
                    'emp_name'        => 'Requester',
                    'req_type'        => 'Type',
                    'created_date'    => ['label' => 'Created Date', 'type' => 'datetime'],
                    'day_left'        => ['label' => 'Day Left', 'type' => 'day_left', 'due_field' => 'due_date'],
                    'ticket_status'   => ['label' => 'Status', 'type' => 'status'],
                    'ticket_priority' => ['label' => 'Priority', 'type' => 'priority'],
                    'action'          => ['label' => 'Action', 'type' => 'action', 'detail_url' => 'admin/Ticket_detail'],
                ],
                'rows'           => $tickets,
                'emptyMessage'   => 'No tickets found matching the criteria.',
                'paginationHTML' => $paginationHTML ?? '',
            ]) ?>
        </div>

        <!-- 2. KANBAN SWIMLANE HORIZONTAL GRID VIEW (WITH DRAG & DROP) -->
        <div id="ticketGridView" class="ticket-view-section" style="display:none;">
            <?php
            // Use full kanban dataset if available, fallback to tickets
            $swimlaneDataSource = $kanbanTickets ?? $tickets ?? [];

            // Categorize tickets into swimlanes
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

            // Helper to render a card
            function renderKanbanCard($t) {
                $ticketId = $t['id'] ?? '';
                $initials = strtoupper(substr(trim($t['emp_name'] ?? 'U'), 0, 2));
                $statusVal = strtolower(trim($t['ticket_status'] ?? 'open'));
                $statusLabel = ($statusVal === 'in_progress') ? 'In Progress' : ucwords(str_replace('_', ' ', $statusVal));
                $priorityVal = ucfirst(trim($t['ticket_priority'] ?? ''));
                $createdDateYmd = !empty($t['created_date']) ? date('Y-m-d', strtotime($t['created_date'])) : '';
                $createdTimestamp = !empty($t['created_date']) ? strtotime($t['created_date']) : 0;
                $modifiedTimestamp = !empty($t['modified_date']) ? strtotime($t['modified_date']) : (!empty($t['finish_date']) ? strtotime($t['finish_date']) : $createdTimestamp);

                $dueVal = $t['due_date'] ?? null;
                $overdueHtml = '<span class="text-muted">-</span>';
                if (!empty($dueVal)) {
                    $now = new DateTime();
                    $due = new DateTime($dueVal);
                    $interval = $now->diff($due);
                    $days = (int) $interval->days;
                    $hours = (int) $interval->h;
                    $mins = (int) $interval->i;

                    $parts = [];
                    if ($days > 0) $parts[] = $days . 'd';
                    if ($hours > 0) $parts[] = $hours . 'h';
                    if ($mins > 0 || empty($parts)) $parts[] = $mins . 'm';
                    $durStr = implode(' ', $parts);

                    if ($statusVal === 'closed' || $statusVal === 'done') {
                        $overdueHtml = '<span class="status-badge status-done">Resolved</span>';
                    } elseif ($now <= $due) {
                        $overdueHtml = '<span class="time-left">' . esc($durStr) . '</span>';
                    } else {
                        $overdueHtml = '<span class="badge-overdue">OVERDUE (' . esc($durStr) . ')</span>';
                    }
                }
                $ticketDesc = !empty($t['message']) ? trim(strip_tags($t['message'])) : (!empty($t['description']) ? trim(strip_tags($t['description'])) : '-');
                ?>
                <div class="ticket-card" id="card-<?= esc($ticketId) ?>" data-ticket-id="<?= esc($ticketId) ?>" data-status="<?= esc($statusVal) ?>" data-created-ymd="<?= esc($createdDateYmd) ?>" data-created-ts="<?= esc($createdTimestamp) ?>" data-modified-ts="<?= esc($modifiedTimestamp) ?>" draggable="true">
                    <div class="ticket-card-header">
                        <div class="ticket-header-left">
                            <span class="ticket-id-tag">#<?= esc(substr($ticketId, 0, 8)) ?></span>
                            <div class="ticket-requester-info">
                                <span class="req-name" title="<?= esc($t['emp_name'] ?? '') ?>"><?= esc($t['emp_name'] ?? 'Unknown') ?></span>
                                <?php if (!empty($t['emp_nip']) && $t['emp_nip'] !== '-'): ?>
                                    <span class="req-nip">NIP: <?= esc($t['emp_nip']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($priorityVal) && $priorityVal !== '-'): ?>
                                    <span class="card-priority-tag priority-<?= strtolower(esc($priorityVal)) ?>">
                                        Priority: <?= esc($priorityVal) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-badges-right">
                            <span class="status-badge status-<?= esc($statusVal) ?>"><?= esc($statusLabel) ?></span>
                        </div>
                    </div>

                    <div class="ticket-card-body">
                        <!-- Title -->
                        <h4 class="ticket-card-title">
                            <a href="<?= base_url('admin/Ticket_detail/' . esc($ticketId)) ?>" title="<?= esc($t['subject'] ?? '') ?>">
                                <?= esc($t['subject'] ?? 'Untitled Ticket') ?>
                            </a>
                        </h4>

                        <!-- Deskripsi -->
                        <p class="ticket-card-desc" title="<?= esc($ticketDesc) ?>">
                            <?= esc($ticketDesc) ?>
                        </p>

                        <!-- Tags / Request Type -->
                        <?php if (!empty($t['req_type'])): ?>
                        <div class="ticket-card-tags">
                            <span class="ticket-type-pill"><?= esc($t['req_type']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="ticket-card-footer">
                        <div class="ticket-meta-left">
                            <?= $overdueHtml ?>
                            <span class="meta-date"><?= !empty($t['created_date']) ? esc(date('d M Y, H:i', strtotime($t['created_date']))) : '-' ?></span>
                        </div>
                        <a href="<?= base_url('admin/Ticket_detail/' . esc($ticketId)) ?>" style="text-decoration:none;">
                            <button type="button" class="btn-open">
                                <span>Open</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="kanban-board-container">
                <!-- 1. SWIMLANE: AREA OPEN -->
                <div class="kanban-swimlane lane-open" data-lane-status="open">
                    <div class="swimlane-header">
                        <div class="swimlane-title-group">
                            <span class="swimlane-indicator-dot dot-open"></span>
                            <span class="swimlane-title">Open</span>
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
                            <?php foreach ($openTicketsList as $t): renderKanbanCard($t); endforeach; ?>
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
                            <span class="swimlane-title">In Progress</span>
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
                            <?php foreach ($inProgressTicketsList as $t): renderKanbanCard($t); endforeach; ?>
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
                            <span class="swimlane-title">Closed / Done</span>
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
                            <?php foreach ($closedTicketsList as $t): renderKanbanCard($t); endforeach; ?>
                        <?php else: ?>
                            <div class="swimlane-empty-placeholder">Drag ticket here to set status to Closed / Done</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Toast Notification Element -->
    <div id="kanbanToast" class="kanban-toast" style="display:none;"></div>

    <script>
        // Apply search & dropdown filters
        function applyFilters() {
            var url = new URL(window.location.href);
            var params = url.searchParams;

            var searchVal = document.getElementById('ticketSearchInput').value.trim();
            var priorityVal = document.getElementById('filterPriority').value;
            var typeVal = document.getElementById('filterType').value;
            var statusVal = document.getElementById('filterStatus').value;
            var perPageVal = document.getElementById('filterPerPage').value;

            if (searchVal) params.set('search', searchVal); else params.delete('search');
            if (priorityVal) params.set('priority', priorityVal); else params.delete('priority');
            if (typeVal) params.set('type', typeVal); else params.delete('type');
            if (statusVal) params.set('status', statusVal); else params.delete('status');
            if (perPageVal) params.set('per_page', perPageVal);

            params.set('page', 1);
            window.location = url.pathname + '?' + params.toString();
        }

        function setQuickFilter(type) {
            var prioritySelect = document.getElementById('filterPriority');
            var statusSelect = document.getElementById('filterStatus');

            if (type === 'all') {
                prioritySelect.value = '';
                statusSelect.value = '';
            } else if (type === 'open') {
                statusSelect.value = 'open';
                prioritySelect.value = '';
            } else if (type === 'in_progress') {
                statusSelect.value = 'in_progress';
                prioritySelect.value = '';
            } else if (type === 'urgent') {
                prioritySelect.value = 'urgent';
                statusSelect.value = '';
            }
            applyFilters();
        }

        // Handle Per Page Dropdown Change (Applies to all card areas in Grid mode, or Table in Table mode)
        function handlePerPageChange(val) {
            var num = parseInt(val, 10) || 12;
            var currentMode = localStorage.getItem('ticket_view_mode') || 'table';

            // Apply page size to all 3 card areas
            ['open', 'in_progress', 'closed'].forEach(function(area) {
                if (areaState[area]) {
                    areaState[area].pageSize = num;
                    areaState[area].page = 1;
                }
            });
            localStorage.setItem('ticket_card_page_size', num);

            if (currentMode === 'grid') {
                // Instant update on grid without page reload
                updateSwimlaneCounts();
            } else {
                // In table mode, reload table with new per_page
                applyFilters();
            }
        }

        // Per-Area Pagination & Filter State
        var areaState = {
            open: { page: 1, pageSize: 12, dateFilter: 'all' },
            in_progress: { page: 1, pageSize: 12, dateFilter: 'all' },
            closed: { page: 1, pageSize: 12, dateFilter: 'all' }
        };

        // Filter cards in a specific area by date
        function filterAreaByDate(area, filterVal) {
            if (!areaState[area]) return;
            areaState[area].dateFilter = filterVal;
            areaState[area].page = 1;
            updateAreaView(area);
        }

        // Change page in a specific area
        function changeAreaPage(area, delta) {
            if (!areaState[area]) return;
            areaState[area].page += delta;
            updateAreaView(area);
        }

        // Check if card matches date filter
        function matchesDateFilter(card, filterVal) {
            if (filterVal === 'all') return true;

            var ymd = card.getAttribute('data-created-ymd');
            var ts = parseInt(card.getAttribute('data-created-ts'), 10) * 1000;
            if (!ts) return true;

            var now = new Date();
            var cardDate = new Date(ts);

            if (filterVal === 'today') {
                var todayYmd = now.toISOString().split('T')[0];
                return ymd === todayYmd;
            } else if (filterVal === '7days') {
                var sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(now.getDate() - 7);
                return cardDate >= sevenDaysAgo;
            } else if (filterVal === '30days') {
                var startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                return cardDate >= startOfMonth;
            }
            return true;
        }

        // Update single area cards, count, and pagination controls
        function updateAreaView(area) {
            var track = document.getElementById('track-' + area);
            if (!track || !areaState[area]) return;

            var state = areaState[area];
            var allCards = Array.from(track.querySelectorAll('.ticket-card'));
            var matchingCards = allCards.filter(function(card) {
                return matchesDateFilter(card, state.dateFilter);
            });

            // Sorting specification:
            // - open & in_progress: created_date ASC (terlama / oldest first)
            // - closed: modified_date DESC (terbaru / newest first)
            matchingCards.sort(function(a, b) {
                if (area === 'open' || area === 'in_progress') {
                    var tsA = parseInt(a.getAttribute('data-created-ts'), 10) || 0;
                    var tsB = parseInt(b.getAttribute('data-created-ts'), 10) || 0;
                    return tsA - tsB; // ASC (terlama di awal)
                } else {
                    var modA = parseInt(a.getAttribute('data-modified-ts'), 10) || parseInt(a.getAttribute('data-created-ts'), 10) || 0;
                    var modB = parseInt(b.getAttribute('data-modified-ts'), 10) || parseInt(b.getAttribute('data-created-ts'), 10) || 0;
                    return modB - modA; // DESC (terbaru di awal)
                }
            });

            // Re-order matching cards in DOM track
            matchingCards.forEach(function(card) {
                track.appendChild(card);
            });

            // Total count update
            var countBadge = document.getElementById('count-' + area);
            if (countBadge) {
                countBadge.textContent = matchingCards.length + ' tickets';
            }

            // Calculate pagination
            var totalPages = Math.ceil(matchingCards.length / state.pageSize);
            if (totalPages < 1) totalPages = 1;
            if (state.page > totalPages) state.page = totalPages;
            if (state.page < 1) state.page = 1;

            var startIndex = (state.page - 1) * state.pageSize;
            var endIndex = startIndex + state.pageSize;

            // Render visibility
            allCards.forEach(function(card) {
                card.style.display = 'none';
            });

            matchingCards.forEach(function(card, idx) {
                if (idx >= startIndex && idx < endIndex) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            // Empty placeholder management
            var placeholder = track.querySelector('.swimlane-empty-placeholder');
            if (matchingCards.length === 0) {
                var emptyMsg = state.dateFilter !== 'all' 
                    ? 'No tickets found for selected date' 
                    : 'Drag ticket here to set status to ' + (area === 'in_progress' ? 'In Progress' : (area === 'closed' ? 'Closed / Done' : 'Open'));
                if (!placeholder) {
                    var p = document.createElement('div');
                    p.className = 'swimlane-empty-placeholder';
                    p.textContent = emptyMsg;
                    track.appendChild(p);
                } else {
                    placeholder.textContent = emptyMsg;
                }
            } else if (placeholder) {
                placeholder.remove();
            }

            // Update Pagination UI
            var pageInfo = document.getElementById('page-info-' + area);
            if (pageInfo) {
                pageInfo.textContent = state.page + ' / ' + totalPages;
            }

            var btnPrev = document.getElementById('btn-prev-' + area);
            if (btnPrev) {
                btnPrev.disabled = (state.page <= 1);
            }

            var btnNext = document.getElementById('btn-next-' + area);
            if (btnNext) {
                btnNext.disabled = (state.page >= totalPages || matchingCards.length === 0);
            }
        }

        // Update all 3 areas
        function updateSwimlaneCounts() {
            ['open', 'in_progress', 'closed'].forEach(updateAreaView);
        }

        // View Mode Switcher (Table vs Grid)
        function switchViewMode(mode) {
            var tableView = document.getElementById('ticketTableView');
            var gridView = document.getElementById('ticketGridView');
            var btnTable = document.getElementById('btnViewTable');
            var btnGrid = document.getElementById('btnViewGrid');

            if (mode === 'grid') {
                if (tableView) tableView.style.display = 'none';
                if (gridView) gridView.style.display = 'block';
                if (btnTable) btnTable.classList.remove('active');
                if (btnGrid) btnGrid.classList.add('active');
                localStorage.setItem('ticket_view_mode', 'grid');
                updateSwimlaneCounts();
            } else {
                if (gridView) gridView.style.display = 'none';
                if (tableView) tableView.style.display = 'block';
                if (btnGrid) btnGrid.classList.remove('active');
                if (btnTable) btnTable.classList.add('active');
                localStorage.setItem('ticket_view_mode', 'table');
            }
        }

        // Toast Helper
        function showKanbanToast(message, isSuccess) {
            var toast = document.getElementById('kanbanToast');
            if (!toast) return;
            toast.textContent = message;
            toast.className = 'kanban-toast ' + (isSuccess ? 'toast-success' : 'toast-error');
            toast.style.display = 'flex';
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3000);
        }

        // Setup Drag & Drop Handlers
        function initDragAndDrop() {
            var cards = document.querySelectorAll('.ticket-card');
            var tracks = document.querySelectorAll('.swimlane-cards-track');

            cards.forEach(function(card) {
                card.addEventListener('dragstart', function(e) {
                    card.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', card.getAttribute('data-ticket-id'));
                    e.dataTransfer.setData('source-status', card.getAttribute('data-status'));
                });

                card.addEventListener('dragend', function() {
                    card.classList.remove('dragging');
                });
            });

            tracks.forEach(function(track) {
                track.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    track.classList.add('lane-drop-active');
                });

                track.addEventListener('dragleave', function() {
                    track.classList.remove('lane-drop-active');
                });

                track.addEventListener('drop', function(e) {
                    e.preventDefault();
                    track.classList.remove('lane-drop-active');

                    var ticketId = e.dataTransfer.getData('text/plain');
                    var sourceStatus = e.dataTransfer.getData('source-status');
                    var targetStatus = track.getAttribute('data-lane-status');

                    if (!ticketId || sourceStatus === targetStatus) return;

                    var card = document.getElementById('card-' + ticketId);
                    if (!card) return;

                    // Move DOM card
                    track.appendChild(card);
                    card.setAttribute('data-status', targetStatus);
                    card.setAttribute('data-modified-ts', Math.floor(Date.now() / 1000));

                    // Update card's status badge
                    var badge = card.querySelector('.status-badge');
                    if (badge) {
                        badge.className = 'status-badge status-' + targetStatus;
                        badge.textContent = (targetStatus === 'in_progress') ? 'In Progress' : (targetStatus === 'closed' ? 'Closed' : 'Open');
                    }

                    // Re-render both areas immediately
                    updateAreaView(sourceStatus);
                    updateAreaView(targetStatus);

                    // Send AJAX to server
                    var formData = new FormData();
                    formData.append('ticket_id', ticketId);
                    formData.append('status', targetStatus);

                    fetch('<?= base_url('admin/update_ticket_status') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            showKanbanToast('✓ ' + (data.message || 'Status updated successfully!'), true);
                        } else {
                            showKanbanToast('✕ ' + (data.message || 'Failed to update status'), false);
                            // Revert on error
                            var origTrack = document.getElementById('track-' + sourceStatus);
                            if (origTrack) {
                                origTrack.appendChild(card);
                                card.setAttribute('data-status', sourceStatus);
                                updateAreaView(sourceStatus);
                                updateAreaView(targetStatus);
                            }
                        }
                    })
                    .catch(function(err) {
                        showKanbanToast('✕ Network error while updating ticket status.', false);
                        var origTrack = document.getElementById('track-' + sourceStatus);
                        if (origTrack) {
                            origTrack.appendChild(card);
                            card.setAttribute('data-status', sourceStatus);
                            updateAreaView(sourceStatus);
                            updateAreaView(targetStatus);
                        }
                    });
                });
            });
        }

        // Keyboard Shortcut ⌘K / Ctrl+K
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                var searchInput = document.getElementById('ticketSearchInput');
                if (searchInput) searchInput.focus();
            }
        });

        // Initialize saved view mode, per-area pagination & drag drop
        document.addEventListener('DOMContentLoaded', function() {
            var savedMode = localStorage.getItem('ticket_view_mode') || 'table';
            var savedCardPageSize = parseInt(localStorage.getItem('ticket_card_page_size'), 10);
            var perPageSelect = document.getElementById('filterPerPage');

            if (savedCardPageSize) {
                ['open', 'in_progress', 'closed'].forEach(function(area) {
                    if (areaState[area]) {
                        areaState[area].pageSize = savedCardPageSize;
                    }
                });
                if (savedMode === 'grid' && perPageSelect) {
                    perPageSelect.value = savedCardPageSize;
                }
            } else if (perPageSelect) {
                var currentVal = parseInt(perPageSelect.value, 10) || 12;
                ['open', 'in_progress', 'closed'].forEach(function(area) {
                    if (areaState[area]) {
                        areaState[area].pageSize = currentVal;
                    }
                });
            }

            switchViewMode(savedMode);
            initDragAndDrop();
        });
    </script>
</body>
</html>