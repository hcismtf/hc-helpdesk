<?php
/**
 * Reusable Modern Data Table Component
 *
 * @var string       $title             Table box title (optional, default: '')
 * @var string       $tableId           HTML ID of table (optional, default: 'dataTable')
 * @var bool         $showControls      Whether to render top controls bar (default: true)
 * @var bool         $showSearch        Whether to render search box (default: true)
 * @var string       $searchPlaceholder Placeholder for search input (default: 'Search Ticket')
 * @var string       $searchHandler     JS function on keyup (default: 'searchTicketTable()')
 * @var string       $searchInputId     ID of search input (default: 'searchTicket')
 * @var bool         $showPerPage       Whether to show per-page dropdown (default: true)
 * @var int          $perPage           Current per-page value (default: 10)
 * @var array        $perPageOptions    Options array (default: [10, 20, 50, 100])
 * @var string       $perPageHandler    JS function onchange (default: 'applyFilter()')
 * @var bool         $showFilterBtn     Whether to show Filter modal trigger button (default: true)
 * @var string       $filterHandler     JS function on click (default: 'openFilterModal()')
 * @var array        $columns           Associative array of columns [key => Label or Config Array]
 * @var array        $rows              List of associative rows / records
 * @var string       $emptyMessage      Message when no rows found (default: 'No records found.')
 * @var string       $paginationHTML    Rendered pagination HTML string (optional)
 * @var string       $detailUrlBase     Base URL for action open buttons (default: 'admin/ticket_detail')
 * @var callable|null $customRowRenderer Custom row callback function($row, $columns): string
 */

$title = $title ?? '';
$tableId = $tableId ?? 'ticketsTable';
$showControls = $showControls ?? true;
$showSearch = $showSearch ?? true;
$searchPlaceholder = $searchPlaceholder ?? 'Search Ticket';
$searchHandler = $searchHandler ?? 'searchTicketTable()';
$searchInputId = $searchInputId ?? 'searchTicket';
$showPerPage = $showPerPage ?? true;
$perPage = $perPage ?? 10;
$perPageOptions = $perPageOptions ?? [10, 20, 50, 100];
$perPageHandler = $perPageHandler ?? 'applyFilter()';
$showFilterBtn = $showFilterBtn ?? true;
$filterHandler = $filterHandler ?? 'openFilterModal()';
$columns = $columns ?? [];
$rows = $rows ?? [];
$emptyMessage = $emptyMessage ?? 'No records found.';
$paginationHTML = $paginationHTML ?? '';
$detailUrlBase = $detailUrlBase ?? 'admin/ticket_detail';
$customRowRenderer = $customRowRenderer ?? null;
?>
<div class="tickets-box">
    <?php if ($title !== '' || $showControls): ?>
        <div class="tickets-header">
            <?php if ($title !== ''): ?>
                <div class="tickets-header-left"><?= esc($title) ?></div>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($showControls): ?>
                <div class="tickets-header-right">
                    <?php if ($showPerPage): ?>
                        <div class="per-page-wrapper">
                            <span class="control-label">Showing Data</span>
                            <select id="perPage" onchange="<?= esc($perPageHandler) ?>" class="select-per-page">
                                <?php foreach ($perPageOptions as $n): ?>
                                    <option value="<?= $n ?>" <?= (int) $perPage === (int) $n ? 'selected' : '' ?>><?= $n ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($showFilterBtn): ?>
                        <button type="button" onclick="<?= esc($filterHandler) ?>" class="btn-filter">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            <span>Filter</span>
                        </button>
                    <?php endif; ?>

                    <?php if ($showSearch): ?>
                        <div class="search-box">
                            <input type="text" placeholder="<?= esc($searchPlaceholder) ?>" id="<?= esc($searchInputId) ?>"
                                onkeyup="<?= esc($searchHandler) ?>">
                            <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="tickets-table" id="<?= esc($tableId) ?>">
            <thead>
                <tr>
                    <?php foreach ($columns as $colKey => $colDef):
                        $colLabel = is_array($colDef) ? ($colDef['label'] ?? $colKey) : $colDef;
                        ?>
                        <th><?= esc($colLabel) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <?php if (is_callable($customRowRenderer)): ?>
                            <?= $customRowRenderer($row, $columns) ?>
                        <?php else: ?>
                            <tr>
                                <?php foreach ($columns as $colKey => $colDef):
                                    $colType = is_array($colDef) ? ($colDef['type'] ?? 'text') : 'text';
                                    $val = $row[$colKey] ?? null;
                                    ?>
                                    <td>
                                        <?php if ($colType === 'action' || $colKey === 'action'): ?>
                                            <?php
                                            $actionUrl = is_array($colDef) && !empty($colDef['detail_url'])
                                                ? $colDef['detail_url']
                                                : $detailUrlBase;
                                            $rowId = $row['id'] ?? '';
                                            ?>
                                            <a href="<?= base_url($actionUrl . '/' . esc($rowId)) ?>" style="text-decoration:none;">
                                                <button type="button" class="btn-open">
                                                    <span>Open</span>
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="9 18 15 12 9 6"></polyline>
                                                    </svg>
                                                </button>
                                            </a>

                                        <?php elseif ($colType === 'requester' || $colKey === 'emp_name' || $colKey === 'requester'): ?>
                                            <div class="table-user-cell" style="display:flex; align-items:center; gap:10px;">
                                                <div class="table-user-avatar" style="width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%); color:#3730a3; font-weight:700; font-size:12px; display:flex; align-items:center; justify-content:center; border:1px solid #c7d2fe; flex-shrink:0;">
                                                    <?php
                                                    $rawName = $val ?? ($row['emp_name'] ?? 'U');
                                                    $parts = explode(' ', trim($rawName));
                                                    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                                    echo esc(!empty($initials) ? $initials : 'U');
                                                    ?>
                                                </div>
                                                <div class="table-user-info" style="display:flex; flex-direction:column; min-width:0;">
                                                    <span class="table-user-name" style="font-weight:700; color:#0f172a; font-size:13px; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= esc($val ?? '') ?>">
                                                        <?= esc($val ?? 'User') ?>
                                                    </span>
                                                    <?php if (!empty($row['emp_nip']) || !empty($row['ticket_no'])): ?>
                                                        <span class="table-user-sub" style="font-size:11px; color:#64748b; font-weight:500; margin-top:2px;">
                                                            <?= esc($row['emp_nip'] ?? $row['ticket_no'] ?? '') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        <?php elseif ($colType === 'category' || $colKey === 'req_type'): ?>
                                            <span class="table-category-pill" style="display:inline-flex; align-items:center; gap:5px; background:#f1f5f9; color:#334155; font-size:11.5px; font-weight:600; padding:3px 10px; border-radius:6px; border:1px solid #e2e8f0; white-space:nowrap;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#3b82f6; flex-shrink:0;"></span>
                                                <?= esc($val ?? 'HC Helpdesk') ?>
                                            </span>

                                        <?php elseif ($colType === 'priority' || $colKey === 'priority' || $colKey === 'ticket_priority'): ?>
                                            <?php
                                            $priorityVal = $val ?? $row['ticket_priority'] ?? '-';
                                            $priorityClean = ucfirst(trim($priorityVal));
                                            if ($priorityClean !== '-' && $priorityClean !== ''):
                                                ?>
                                                <span
                                                    class="priority-badge priority-<?= esc($priorityClean) ?>"><?= esc($priorityClean) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>

                                        <?php elseif ($colType === 'status' || $colKey === 'status' || $colKey === 'ticket_status'): ?>
                                            <?php
                                            $statusVal = $val ?? $row['ticket_status'] ?? '-';
                                            $statusLabel = ($statusVal === 'in_progress') ? 'In Progress' : ucwords(str_replace('_', ' ', $statusVal));
                                            ?>
                                            <span class="status-badge status-<?= esc(strtolower(str_replace(' ', '_', $statusVal))) ?>">
                                                <?= esc($statusLabel) ?>
                                            </span>

                                        <?php elseif ($colType === 'datetime' || in_array($colKey, ['created_date', 'due_date', 'finish_date'])): ?>
                                            <?php if (!empty($val)): ?>
                                                <?= esc(date('d/m/Y H:i:s', strtotime($val))) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>

                                        <?php elseif ($colType === 'day_left'): ?>
                                            <?php
                                            $dueField = is_array($colDef) ? ($colDef['due_field'] ?? 'due_date') : 'due_date';
                                            $dueVal = $row[$dueField] ?? null;
                                            $ticketStatus = strtolower($row['ticket_status'] ?? '');

                                            if (!empty($dueVal)):
                                                $now = new DateTime();
                                                $due = new DateTime($dueVal);
                                                $interval = $now->diff($due);

                                                $days = (int) $interval->days;
                                                $hours = (int) $interval->h;
                                                $minutes = (int) $interval->i;

                                                $parts = [];
                                                if ($days > 0)
                                                    $parts[] = $days . 'd';
                                                if ($hours > 0)
                                                    $parts[] = $hours . 'h';
                                                if ($minutes > 0 || empty($parts))
                                                    $parts[] = $minutes . 'm';
                                                $durationStr = implode(' ', $parts);

                                                if ($ticketStatus === 'closed' || $ticketStatus === 'done'):
                                                    if (!empty($row['finish_date']) && strtotime($row['finish_date']) <= strtotime($dueVal)):
                                                        echo '<span class="status-badge status-done">Resolved (On Time)</span>';
                                                    else:
                                                        echo '<span class="status-badge status-closed">Resolved</span>';
                                                    endif;
                                                elseif ($now <= $due):
                                                    echo '<span class="time-left">' . esc($durationStr) . '</span>';
                                                else:
                                                    echo '<span class="badge-overdue">OVERDUE (' . esc($durationStr) . ')</span>';
                                                endif;
                                            else:
                                                echo '<span class="text-muted">-</span>';
                                            endif;
                                            ?>

                                        <?php else: ?>
                                            <?= esc($val ?? '-') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($columns) ?>" style="text-align:center; color:#94a3b8; padding:32px 16px;">
                            <?= esc($emptyMessage) ?>
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