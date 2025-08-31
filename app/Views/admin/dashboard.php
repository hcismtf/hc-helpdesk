<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
    
</head>
<body>

    <?php $active = 'dashboard'; include('navbar.php'); ?>
    
    <div class="main-content" id="main-content">
        <div style="display:flex; justify-content:space-between;">
            <div>
                <h2>Welcome, <?= esc(session('username')) ?>!</h2>
                <div>Superadmin</div>
            </div>
            <div class="analytics-box">
                <div class="analytics-title">Analytics</div>
                <div class="analytics-list">
                    AVG Response time<br>
                    AVG Resolution time<br>
                    SLA Compliance Rate<br>
                </div>
                <div class="analytics-list" style="margin-top:6px;">
                    00 d 02 h 34 m<br>
                    00 d 02 h 34 m<br>
                    101%
                </div>
            </div>
        </div>
        <div class="stats-row">
            <div class="stat-card open">
                <div class="stat-title">Open</div>
                <div class="stat-value"><?= $openCount ?></div>
            </div>
            <div class="stat-card inprogress">
                <div class="stat-title">In Progress</div>
                <div class="stat-value"><?= $inProgressCount ?></div>
            </div>
            <div class="stat-card done">
                <div class="stat-title">Done</div>
                <div class="stat-value"><?= $doneCount ?></div>
            </div>
            <div class="stat-card total">
                <div class="stat-title">Total</div>
                <div class="stat-value"><?= $totalCount ?></div>
            </div>
        </div>
        <div class="tickets-box">
            <div class="ticket-headers" style="float: left">
                    <span style="font-weight:600; font-size:16px;">Open Tickets</span>
            </div>
            <div class="tickets-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; float: right;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <span style="font-weight:600; font-size:16px;">Showing Data</span>
                    <select id="perPage" onchange="applyFilter()" style="border-radius:20px; border:1px solid #ccc; padding:4px 18px; font-size:15px; background:#f5f6fa; color:#888; outline:none;">
                        <?php foreach ([10,20,50,100] as $n): ?>
                            <option value="<?= $n ?>" <?= $perPage==$n?'selected':'' ?>><?= $n ?></option>
                        <?php endforeach ?>
                    </select>
                    <button onclick="openFilterModal()" class="btn-filter" style="background:#234be7; color:#fff; border:none; border-radius:20px; padding:6px 24px; font-size:15px; font-weight:600; cursor:pointer;">Filter</button>
                </div>
                <div class="search-box" style="display:flex; align-items:center; background:#f5f6fa; border-radius:20px; padding:6px 16px; width:260px; margin-left: 35px;">
                    <input type="text" placeholder="Search Ticket" id="searchTicket" onkeyup="searchTicketTable()" style="border:none; background:transparent; outline:none; font-size:15px; width:100%;">
                    <span class="search-icon" style="font-size:18px; color:#888;">&#128269;</span>
                </div>
            </div>
            <!-- Filter Modal -->
            <div id="filterModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeFilterModal()">&times;</span>
                    <h3 style="margin-bottom:20px; padding-left:10px ;">Filter</h3>
                    <form id="filterForm" onsubmit="applyFilter();return false;">
                        <!-- Request Type -->
                        <div class="form-row">
                            <label>Request Type</label>
                            <select name="type">
                                <option value="">Select</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= esc($t['req_type']) ?>" <?= $type==$t['req_type']?'selected':'' ?>><?= esc($t['req_type']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <!-- Date Inputs -->
                        <div class="form-row dates-row">
                            <div class="date-col">
                                <label>Start Date</label>
                                <input type="date" name="start" id="startDate" value="<?= esc($start) ?>">
                            </div>
                            <div class="date-col">
                                <label>End Date</label>
                                <input type="date" name="end" id="endDate" value="<?= esc($end) ?>">
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="form-row btn-row">
                            <button type="submit" class="btn-blue">Apply Filter</button>
                            <button type="button" onclick="resetFilter()" class="btn-blue">Reset Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="tickets-table" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Due Date</th>
                        <th>Day Left</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($openTickets as $ticket): 
                    if ($ticket['ticket_status'] !== 'open') continue; // hanya tampilkan status open
                    $created = new DateTime($ticket['created_date']);
                    $due = clone $created;
                    $due->modify('+1 day');
                    $now = new DateTime();
                    $interval = $now < $due ? $now->diff($due) : false;
                ?>
                    <tr>
                        <td><?= esc($ticket['emp_name']) ?></td>
                        <td><?= esc($ticket['req_type']) ?></td>
                        <td><?= esc($created->format('d/m/Y H:i:s')) ?></td>
                        <td><?= esc($due->format('d/m/Y H:i:s')) ?></td>
                        <td>
                            <?php if ($interval): ?>
                                <?= $interval->d ?> Day <?= str_pad($interval->h,2,'0',STR_PAD_LEFT) ?>H <?= str_pad($interval->i,2,'0',STR_PAD_LEFT) ?>M <?= str_pad($interval->s,2,'0',STR_PAD_LEFT) ?>S
                            <?php else: ?>
                                0 Day 00H 00M 00S
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-open">Open</button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <div style="margin-top:24px;">
                <?= $pager->links('tickets', 'default_full') ?>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/admin/dashboard.js') ?>"></script>
</body>
</html>