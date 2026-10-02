<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>?v=<?= time() ?>">

</head>

<body>

    <?php $active = 'dashboard';
    include('navbar.php'); ?>

    <div class="main-content" id="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => 'Analytics Overview',
            'pageTitle'        => 'Dashboard',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <!-- SLA Overview Banner with Connected Interactive Trend Chart -->
        <div class="sla-overview-strip">
            <!-- Left Side: 3 Connected Metric Cards -->
            <div class="sla-metrics-group">
                <!-- Card 1: AVG Response Time -->
                <div class="sla-item metric-card active" data-metric="incoming" onclick="switchMiniChart('incoming', this)" title="Click to view Incoming Trend">
                    <div class="card-top-row">
                        <div class="card-icon-title">
                            <div class="sla-icon-pill resp-pill">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <span class="sla-label">AVG Response Time</span>
                        </div>
                        <span class="card-micro-tag tag-blue">Speed</span>
                    </div>
                    <div class="card-bottom-row">
                        <span class="sla-val"><?= esc($avgResponseStr) ?></span>
                        <div class="card-sub-info">
                            <span class="sub-target">Target: &le; 8h</span>
                            <span class="metric-indicator-dot"></span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: AVG Resolution Time -->
                <div class="sla-item metric-card" data-metric="resolved" onclick="switchMiniChart('resolved', this)" title="Click to view Resolved Trend">
                    <div class="card-top-row">
                        <div class="card-icon-title">
                            <div class="sla-icon-pill reso-pill">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <span class="sla-label">AVG Resolution Time</span>
                        </div>
                        <span class="card-micro-tag tag-green">Resolved</span>
                    </div>
                    <div class="card-bottom-row">
                        <span class="sla-val"><?= esc($avgResolutionStr) ?></span>
                        <div class="card-sub-info">
                            <span class="sub-target">Target: &le; 24h</span>
                            <span class="metric-indicator-dot"></span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: SLA Compliance Rate -->
                <div class="sla-item metric-card" data-metric="sla" onclick="switchMiniChart('sla', this)" title="Click to view SLA % Trend">
                    <div class="card-top-row">
                        <div class="card-icon-title">
                            <div class="sla-icon-pill comp-pill <?= $slaRate >= 80 ? 'good' : ($slaRate >= 60 ? 'warn' : 'danger') ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                            <span class="sla-label">SLA Compliance</span>
                        </div>
                        <span class="card-micro-tag <?= $slaRate >= 80 ? 'tag-green' : ($slaRate >= 60 ? 'tag-warn' : 'tag-danger') ?>">
                            <?= $slaRate >= 80 ? 'Good' : ($slaRate >= 60 ? 'Moderate' : 'Low') ?>
                        </span>
                    </div>
                    <div class="card-bottom-row">
                        <div class="sla-val-wrap">
                            <span class="sla-val <?= $slaRate >= 80 ? 'text-success' : ($slaRate >= 60 ? 'text-warn' : 'text-danger') ?>"><?= esc($slaRate) ?>%</span>
                            <div class="sla-mini-progress">
                                <div class="sla-progress-fill <?= $slaRate >= 80 ? 'good' : ($slaRate >= 60 ? 'warn' : 'danger') ?>" style="width: <?= min(100, max(0, (int)$slaRate)) ?>%;"></div>
                            </div>
                        </div>
                        <div class="card-sub-info">
                            <span class="sub-target">Target: 80%</span>
                            <span class="metric-indicator-dot"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Connected Trend Chart -->
            <div class="sla-mini-chart-section">
                <div class="mini-chart-header">
                    <div class="mini-chart-title">
                        <span class="pulse-dot" id="chartPulseDot"></span>
                        <span id="chartActiveLabel">Graph Incoming</span>
                    </div>
                    <div class="mini-chart-toggles">
                        <button type="button" class="mini-toggle active" data-metric="incoming" onclick="switchMiniChart('incoming', this)">Incoming</button>
                        <button type="button" class="mini-toggle" data-metric="resolved" onclick="switchMiniChart('resolved', this)">Resolved</button>
                        <button type="button" class="mini-toggle" data-metric="sla" onclick="switchMiniChart('sla', this)">SLA %</button>
                    </div>
                </div>
                <div class="mini-chart-canvas-wrap">
                    <canvas id="slaMiniSparkline"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Overview Metric Cards -->
        <div class="modern-stats-grid">
            <!-- Open Tickets -->
            <div class="modern-stat-card card-open">
                <div class="stat-card-header">
                    <div class="stat-card-badge">Needs Action</div>
                    <div class="stat-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-number"><?= esc($openCount) ?></div>
                    <div class="stat-card-label">Open Tickets</div>
                </div>
                <div class="stat-card-footer">
                    <span class="stat-footer-text">Awaiting assignment & response</span>
                </div>
            </div>

            <!-- In Progress Tickets -->
            <div class="modern-stat-card card-inprogress">
                <div class="stat-card-header">
                    <div class="stat-card-badge">Active Handling</div>
                    <div class="stat-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-number"><?= esc($inProgressCount) ?></div>
                    <div class="stat-card-label">In Progress</div>
                </div>
                <div class="stat-card-footer">
                    <span class="stat-footer-text">Currently being handled by team</span>
                </div>
            </div>

            <!-- Done Tickets -->
            <div class="modern-stat-card card-done">
                <div class="stat-card-header">
                    <div class="stat-card-badge">Completed</div>
                    <div class="stat-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-number"><?= esc($doneCount) ?></div>
                    <div class="stat-card-label">Done / Resolved</div>
                </div>
                <div class="stat-card-footer">
                    <span class="stat-footer-text">Successfully finished & closed</span>
                </div>
            </div>

            <!-- Total Tickets -->
            <div class="modern-stat-card card-total">
                <div class="stat-card-header">
                    <div class="stat-card-badge">Total Volume</div>
                    <div class="stat-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-number"><?= esc($totalCount) ?></div>
                    <div class="stat-card-label">Total Tickets</div>
                </div>
                <div class="stat-card-footer">
                    <span class="stat-footer-text">All recorded tickets in period</span>
                </div>
            </div>
        </div>
        <!-- Reusable Dynamic Data Table Component -->
        <?= view('components/data_table', [
            'title'          => 'Open Tickets',
            'tableId'        => 'ticketsTable',
            'perPage'        => $perPage,
            'columns'        => [
                'emp_name'     => 'Name',
                'req_type'     => 'Type',
                'created_date' => ['label' => 'Created Date', 'type' => 'datetime'],
                'due_date'     => ['label' => 'Due Date', 'type' => 'datetime'],
                'day_left'     => ['label' => 'Day Left', 'type' => 'day_left', 'due_field' => 'due_date'],
                'action'       => ['label' => 'Action', 'type' => 'action', 'detail_url' => 'admin/Ticket_detail'],
            ],
            'rows'           => $openTickets,
            'emptyMessage'   => 'No open tickets found.',
            'paginationHTML' => $paginationHTML ?? '',
        ]) ?>

        <!-- Filter Modal -->
        <div id="filterModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeFilterModal()">&times;</span>
                <h3 style="margin-bottom:20px; padding-left:10px;">Filter</h3>
                <form id="filterForm" onsubmit="applyFilter();return false;">
                    <!-- Request Type -->
                    <div class="form-row">
                        <label>Request Type</label>
                        <select name="type">
                            <option value="">Select</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t['req_type']) ?>" <?= $type == $t['req_type'] ? 'selected' : '' ?>>
                                    <?= esc($t['req_type']) ?></option>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/admin/dashboard.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var trendDates = <?= json_encode($trendDates ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) ?>;
            var trendIncoming = <?= json_encode($trendIncoming ?? [0, 0, 0, 0, 0, 0, 0]) ?>;
            var trendResolved = <?= json_encode($trendResolved ?? [0, 0, 0, 0, 0, 0, 0]) ?>;
            var trendSla = <?= json_encode($trendSla ?? [100, 100, 100, 100, 100, 100, 100]) ?>;

            var metricMeta = {
                incoming: {
                    title: '7-Day Trend: Incoming Volume',
                    color: '#4f46e5',
                    dotColor: '#4f46e5'
                },
                resolved: {
                    title: '7-Day Trend: Resolved Volume',
                    color: '#10b981',
                    dotColor: '#10b981'
                },
                sla: {
                    title: '7-Day Trend: SLA Compliance Rate',
                    color: '#f59e0b',
                    dotColor: '#f59e0b'
                }
            };

            var datasets = {
                incoming: {
                    label: 'Incoming',
                    data: trendIncoming,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5
                },
                resolved: {
                    label: 'Resolved',
                    data: trendResolved,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5
                },
                sla: {
                    label: 'SLA %',
                    data: trendSla,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5
                }
            };

            var canvas = document.getElementById('slaMiniSparkline');
            if (!canvas) return;

            var miniChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: trendDates,
                    datasets: [datasets.incoming]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { left: 4, right: 4, top: 6, bottom: 2 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleFont: { size: 10, weight: '600' },
                            bodyFont: { size: 11, weight: 'bold' },
                            padding: 6,
                            cornerRadius: 6,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    var val = context.parsed.y;
                                    var activeBtn = document.querySelector('.mini-toggle.active');
                                    var activeMetric = activeBtn ? activeBtn.getAttribute('data-metric') : 'incoming';
                                    return activeMetric === 'sla' ? val + '%' : val + ' tickets';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false, beginAtZero: true }
                    }
                }
            });

            window.switchMiniChart = function (metric, triggerEl) {
                // Update toggles
                document.querySelectorAll('.mini-toggle').forEach(function (el) {
                    if (el.getAttribute('data-metric') === metric) {
                        el.classList.add('active');
                    } else {
                        el.classList.remove('active');
                    }
                });

                // Update metric cards
                document.querySelectorAll('.sla-item.metric-card').forEach(function (card) {
                    if (card.getAttribute('data-metric') === metric) {
                        card.classList.add('active');
                    } else {
                        card.classList.remove('active');
                    }
                });

                // Update title & dot color
                var meta = metricMeta[metric];
                if (meta) {
                    var labelEl = document.getElementById('chartActiveLabel');
                    if (labelEl) labelEl.textContent = meta.title;
                    var dotEl = document.getElementById('chartPulseDot');
                    if (dotEl) {
                        dotEl.style.background = meta.dotColor;
                        dotEl.style.boxShadow = '0 0 0 2px ' + meta.dotColor + '33';
                    }
                }

                if (datasets[metric]) {
                    miniChart.data.datasets = [datasets[metric]];
                    miniChart.update();
                }
            };
        });
    </script>
</body>
</html>