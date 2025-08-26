<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
    <style>
        body {
            margin: 0;
            background: #f5f6fa;
            font-family: 'Montserrat', sans-serif;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 70px;
            height: 100vh;
            background: #fff;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: width 0.2s;
            z-index: 100;
        }
        .sidebar.open {
            width: 220px;
        }
        .sidebar .toggle-btn {
            margin: 18px 0 30px 0;
            cursor: pointer;
            align-self: flex-end;
            margin-right: 10px;
            background: none;
            border: none;
            font-size: 22px;
        }
        .sidebar .nav-icons {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 30px;
            width: 100%;
            align-items: center;
        }
        .sidebar .nav-icons a {
            color: #444;
            text-decoration: none;
            font-size: 22px;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 0;
            justify-content: center;
            transition: background 0.15s;
        }
        .sidebar.open .nav-icons a {
            justify-content: flex-start;
            padding-left: 30px;
        }
        .sidebar .nav-icons a.active {
            background: #f5f6fa;
            border-left: 4px solid #3b82f6;
            color: #3b82f6;
        }
        .sidebar .bottom-btn {
            margin-top: auto;
            margin-bottom: 30px;
        }
        .sidebar .bottom-btn a {
            background: #ffe600;
            color: #222;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar.open .nav-label {
            display: inline;
            margin-left: 16px;
        }
        .nav-label {
            display: none;
            font-size: 16px;
        }
        /* Main content */
        .main-content {
            margin-left: 70px;
            transition: margin-left 0.2s;
            padding: 30px 40px;
        }
        .sidebar.open ~ .main-content {
            margin-left: 220px;
        }
        /* Responsive */
        @media (max-width: 700px) {
            .sidebar.open { width: 100vw; }
            .sidebar.open ~ .main-content { margin-left: 0; }
        }
        .stats-row {
            display: flex;
            gap: 24px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 24px 32px;
            min-width: 180px;
            flex: 1;
            display: flex;
            flex-direction: column;
            border-top: 3px solid #eee;
        }
        .stat-card.open { border-top: 3px solid #ffe600; }
        .stat-card.inprogress { border-top: 3px solid #444; }
        .stat-card.done { border-top: 3px solid #22c55e; }
        .stat-card.total { border-top: 3px solid #bbb; }
        .stat-title { font-size: 18px; color: #444; margin-bottom: 8px; }
        .stat-value { font-size: 38px; font-weight: bold; color: #111; }
        .analytics-box {
            position: absolute;
            right: 40px;
            top: 30px;
            text-align: right;
        }
        .analytics-title { font-weight: bold; }
        .analytics-list { font-size: 13px; color: #444; margin-top: 8px; }
        .tickets-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 24px;
            margin-top: 24px;
        }
        .tickets-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .tickets-table {
            width: 100%;
            border-collapse: collapse;
        }
        .tickets-table th, .tickets-table td {
            padding: 14px 12px;
            text-align: left;
            font-size: 15px;
        }
        .tickets-table th {
            color: #444;
            font-size: 15px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        .tickets-table td {
            border-bottom: 1px solid #f3f3f3;
        }
        .btn-open {
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 18px;
            padding: 4px 22px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 500;
        }
        .search-box {
            display: flex;
            align-items: center;
            background: #f5f6fa;
            border-radius: 18px;
            padding: 6px 16px;
            width: 260px;
        }
        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 15px;
            width: 100%;
        }
        .search-box .search-icon {
            font-size: 18px;
            color: #888;
        }
        /* Pagination wrapper */
        .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        }

        /* Default link */
        .pagination li a,
        .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        font-weight: 500;
        transition: background 0.2s ease;
        }

        /* Hover */
        .pagination li a:hover {
        background: #f0f0f0;
        }

        /* Active page */
        .pagination li.active span,
        .pagination li.active a {
        background: #4F46E5; /* biru seperti contoh */
        color: #fff;
        font-weight: 600;
        }

        /* Disabled (prev/next) */
        .pagination li.disabled span {
        color: #aaa;
        cursor: not-allowed;
        }

    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
            <span id="toggle-icon">&#9776;</span>
        </button>
        <div class="nav-icons">
            <a href="#" class="active" title="Dashboard">
                <span>📊</span>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="#" title="Tickets">
                <span>🎫</span>
                <span class="nav-label">Tickets</span>
            </a>
            <a href="#" title="Settings">
                <span>⚙️</span>
                <span class="nav-label">Settings</span>
            </a>
            <a href="#" title="Ideas">
                <span>💡</span>
                <span class="nav-label">Ideas</span>
            </a>
        </div>
        <div class="bottom-btn">
            <a href="<?= base_url('admin/logout') ?>" title="Logout">
                <span>🔒</span>
                <span class="nav-label">Logout</span>
            </a>
        </div>
    </div>
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
            <div id="filterModal" class="modal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.1); z-index:999; display:flex; align-items:center; justify-content:center;">
                <div class="modal-content" style="background:#fff; border-radius:18px; box-shadow:0 2px 16px rgba(0,0,0,0.13); width:480px; padding:32px; position:relative;">
                    <span class="close" onclick="closeFilterModal()" style="position:absolute; right:18px; top:18px; font-size:22px; cursor:pointer;">&times;</span>
                    
                    <h3 style="margin-bottom:20px;">Filter</h3>
                    
                    <form id="filterForm" onsubmit="applyFilter();return false;">
                        <!-- Request Type -->
                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:6px; font-size:14px;">Request Type</label>
                            <select name="type" style="width:100%;padding:10px 14px;border-radius:24px;border:1px solid #ccc; font-size:14px;">
                                <option value="">Select</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= esc($t['req_type']) ?>" <?= $type==$t['req_type']?'selected':'' ?>><?= esc($t['req_type']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!-- Date Inputs -->
                        <div style="display:flex; gap:16px; margin-bottom:8px;">
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:6px; font-size:14px;">Start Date</label>
                                <input type="date" name="start" value="<?= esc($start) ?>" style="width:100%;padding:10px 14px;border-radius:24px;border:1px solid #ccc; font-size:14px;">
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:6px; font-size:14px;">End Date</label>
                                <input type="date" name="end" value="<?= esc($end) ?>" style="width:100%;padding:10px 14px;border-radius:24px;border:1px solid #ccc; font-size:14px;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div style="margin-top:24px; text-align:center; display:flex; justify-content:center; gap:12px;">
                            <button type="submit" style="background:#234be7; color:#fff; border:none; padding:10px 20px; border-radius:24px; font-size:14px; cursor:pointer;">
                                Apply Filter
                            </button>
                            <button type="button" onclick="resetFilter()" style="background:#e7e7e7; color:#222; border:none; padding:10px 20px; border-radius:24px; font-size:14px; cursor:pointer;">
                                Reset Filter
                            </button>
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
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
        function searchTicketTable() {
            var input = document.getElementById("searchTicket");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("ticketsTable");
            var tr = table.getElementsByTagName("tr");
            for (var i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td");
                var show = false;
                for (var j = 0; j < td.length-1; j++) {
                    if (td[j] && td[j].innerText.toUpperCase().indexOf(filter) > -1) {
                        show = true;
                    }
                }
                tr[i].style.display = show ? "" : "none";
            }
        }
        function openFilterModal() {
            document.getElementById('filterModal').style.display = 'block';
        }
        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }
        function applyFilter() {
            var params = [];
            var form = document.getElementById('filterForm');
            for (var i=0; i<form.elements.length; i++) {
                var el = form.elements[i];
                if (el.name && el.value) params.push(el.name+'='+encodeURIComponent(el.value));
            }
            params.push('per_page='+document.getElementById('perPage').value);
            window.location = '?'+params.join('&');
        }
        function resetFilter() {
            window.location = '?per_page='+document.getElementById('perPage').value;
        }
    </script>
</body>
</html>