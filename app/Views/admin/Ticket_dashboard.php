<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/Ticket_dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
</head>
<body>

    <?php $active = 'tickets'; include('navbar.php'); ?>
    <div class="main-content" id="main-content">
        <div class="page-title">Ticket</div>
        <div class="breadcrumb">
            <a href="<?= base_url('admin/dashboard') ?>" style="color:black;text-decoration:none;">Home</a> &gt;
            <a href="<?= base_url('admin/Ticket_dashboard') ?>" style="color:black;text-decoration:none;">Ticket</a>
        </div>
        <div class="tickets-box">
            <div class="tickets-header">
                <div class="tickets-header-left">All Tickets</div>
                <div class="tickets-header-right">
                    <span style="font-weight:600; font-size:16px;">Showing Data</span>
                    <select id="perPage" onchange="applyFilter()">
                        <?php foreach ([10,20,50,100] as $n): ?>
                            <option value="<?= $n ?>" <?= isset($perPage) && $perPage==$n?'selected':'' ?>><?= $n ?></option>
                        <?php endforeach ?>
                    </select>
                    <button onclick="openFilterModal()" class="btn-filter">Filter</button>
                    <div class="search-box">
                        <input type="text" placeholder="Search Ticket" id="searchTicket" onkeyup="searchTicketTable()">
                        <span class="search-icon">&#128269;</span>
                    </div>
                </div>
            </div>
            <table class="tickets-table" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>NIP</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= isset($ticket['id']) ? esc($ticket['id']) : '-' ?></td>
                            <td><?= isset($ticket['emp_nip']) ? esc($ticket['emp_nip']) : '-' ?></td>
                            <td><?= isset($ticket['req_type']) ? esc($ticket['req_type']) : '-' ?></td>
                            <td>
                                <?php
                                    if (!empty($ticket['created_date'])) {
                                        echo esc(date('d/m/Y H:i:s', strtotime($ticket['created_date'])));
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (isset($ticket['ticket_status']) && $ticket['ticket_status'] == 'in_progress') {
                                        echo 'In Progress';
                                    } else {
                                        echo isset($ticket['ticket_status']) ? esc($ticket['ticket_status']) : '-';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $priority = isset($ticket['ticket_priority']) ? ucfirst($ticket['ticket_priority']) : '-';
                                    if ($priority !== '-') {
                                        echo '<span class="priority-badge priority-' . $priority . '">' . esc($priority) . '</span>';
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/Ticket_detail/' . esc($ticket['id'])) ?>">
                                    <button class="btn-open">Open</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No tickets found.</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="pagination">
                <?= isset($pager) ? $pager->links('tickets', 'default_full') : '' ?>
            </div>
        </div>
    </div>
    <div id="filterModal" class="modal-user" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.2); z-index:999;">
        <div class="modal-user-content" style="
            background:#fff; 
            position:absolute; 
            top:50%; left:50%; 
            transform:translate(-50%,-50%);
            padding:32px; 
            border-radius:18px; 
            max-width:420px; 
            box-shadow:0 2px 16px rgba(0,0,0,0.12);">
            <span style="position:absolute; top:18px; right:18px; font-size:22px; cursor:pointer;" onclick="closeFilterModal()">&times;</span>
            <div style="font-size:22px; font-weight:600; margin-bottom:18px;">Filter</div>
            <form id="filterForm" method="get" action="">
                <div style="display:flex; gap:16px; margin-bottom:18px;">
                    <div style="flex:1;">
                        <label style="font-weight:500;">Priority</label>
                        <select id="filterPriority" name="priority" style="width:100%; padding:10px; border-radius:18px; border:1px solid #ccc;">
                            <option value="">All</option>
                            <option value="urgent" <?= isset($priority) && $priority=='urgent'?'selected':'' ?>>Urgent</option>
                            <option value="high" <?= isset($priority) && $priority=='high'?'selected':'' ?>>High</option>
                            <option value="medium" <?= isset($priority) && $priority=='medium'?'selected':'' ?>>Medium</option>
                            <option value="low" <?= isset($priority) && $priority=='low'?'selected':'' ?>>Low</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; gap:16px; margin-bottom:18px;">
                    <div style="flex:1;">
                        <label style="font-weight:500;">Start Date</label>
                        <input type="date" id="filterStartDate" name="start" value="<?= esc($start ?? '') ?>" style="width:100%; padding:10px; border-radius:18px; border:1px solid #ccc;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-weight:500;">End Date</label>
                        <input type="date" id="filterEndDate" name="end" value="<?= esc($end ?? '') ?>" style="width:100%; padding:10px; border-radius:18px; border:1px solid #ccc;">
                    </div>
                </div>
                <div style="display:flex; gap:18px; justify-content:center; margin-top:18px;">
                    <button type="submit" class="btn-filter">Apply Filter</button>
                    <button type="button" onclick="resetFilter()" class="btn-filter">Reset Filter</button>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= base_url('assets/js/auto_logout.js') ?>"></script>   
    <script>
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
        function applyFilter() {
            var params = [];
            params.push('per_page='+document.getElementById('perPage').value);
            window.location = '?' + params.join('&');
        }
        function openFilterModal() {
            document.getElementById('filterModal').style.display = 'block';
        }
        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }
        function resetFilter() {
            document.getElementById('filterForm').reset();
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
        }

        // Validasi End Date agar tidak kurang dari Start Date
        document.addEventListener('DOMContentLoaded', function() {
            var startDateInput = document.getElementById('filterStartDate');
            var endDateInput = document.getElementById('filterEndDate');
            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    if (endDateInput.value && endDateInput.value < startDateInput.value) {
                        endDateInput.value = startDateInput.value;
                    }
                    endDateInput.setAttribute('min', startDateInput.value);
                });
                endDateInput.addEventListener('change', function() {
                    if (startDateInput.value && endDateInput.value < startDateInput.value) {
                        endDateInput.value = startDateInput.value;
                    }
                });
            }
        });
    </script>
</body>
</html>