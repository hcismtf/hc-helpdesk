<?php
/**
 * Ticket Dashboard Control Strip Component
 *
 * Modern Linear / Jira style filter bar:
 * - Quick Search input with ⌘K keyboard shortcut
 * - Priority, Category/Type, Status, and Per Page dropdowns
 * - Quick Filter pills (All, Only Open, In Progress, Expiring SLA)
 * - View mode toggle (Table View vs Kanban Swimlane Grid)
 *
 * @var string $search
 * @var string $priority
 * @var string $type
 * @var string $status
 * @var int    $perPage
 * @var array  $requestTypes
 */
?>
<div class="ticket-control-strip">
    <!-- Top Row: Quick Search + Filter Dropdowns -->
    <div class="control-strip-top">
        <div class="quick-search-box">
            <svg class="search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="ticketSearchInput" placeholder="Search ticket key, summary, or requester..." value="<?= esc($search ?? '') ?>" onkeydown="if(event.key==='Enter') applyFilters()">
        </div>

        <div class="strip-dropdowns">
            <!-- Priority Dropdown (Dynamic from SLA Table) -->
            <div class="strip-select-wrap">
                <label class="select-prefix">Priority:</label>
                <select id="filterPriority" onchange="applyFilters()">
                    <option value="">All</option>
                    <?php if (!empty($slaList)): ?>
                        <?php foreach ($slaList as $s): ?>
                            <?php 
                            $pName = is_array($s) ? ($s['priority'] ?? '') : ($s->priority ?? '');
                            $pVal  = strtolower($pName);
                            ?>
                            <option value="<?= esc($pVal) ?>" <?= strtolower((string)($priority ?? '')) === $pVal ? 'selected' : '' ?>>
                                <?= esc(ucfirst($pName)) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="urgent" <?= ($priority ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        <option value="high" <?= ($priority ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="medium" <?= ($priority ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="low" <?= ($priority ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Type / Category Dropdown -->
            <div class="strip-select-wrap">
                <label class="select-prefix">Type:</label>
                <select id="filterType" onchange="applyFilters()">
                    <option value="">All Categories</option>
                    <?php if (!empty($requestTypes)): ?>
                        <?php foreach ($requestTypes as $rt): ?>
                            <?php $rtName = is_array($rt) ? $rt['name'] : $rt->name; ?>
                            <option value="<?= esc($rtName) ?>" <?= ($type ?? '') === $rtName ? 'selected' : '' ?>><?= esc($rtName) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Status Dropdown -->
            <div class="strip-select-wrap">
                <label class="select-prefix">Status:</label>
                <select id="filterStatus" onchange="applyFilters()">
                    <option value="">All</option>
                    <?php 
                    $filterStatusDefs = \App\Entities\TicketStatus::getDefinitions();
                    foreach ($filterStatusDefs as $fsCode => $fsDef): 
                    ?>
                        <option value="<?= esc($fsCode) ?>" <?= ($status ?? '') === $fsCode ? 'selected' : '' ?>>
                            <?= esc($fsDef['name'] ?? ucfirst($fsCode)) ?>
                        </option>
                    <?php endforeach; ?>
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
