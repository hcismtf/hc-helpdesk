/**
 * Admin Dashboard Interactive Module
 * Menangani mini sparkline chart (Chart.js), switch chart metric, dan dialog filter modal.
 * 
 * Mengikuti Clean Frontend Module Architecture (rule.md)
 */

(function () {
    'use strict';

    // Inisialisasi sparkline chart
    function initSparklineChart() {
        var chartDataEl = document.getElementById('dashboardChartData');
        var canvas = document.getElementById('slaMiniSparkline');

        if (!chartDataEl || !canvas || typeof Chart === 'undefined') {
            return;
        }

        var trendDates = [];
        var trendIncoming = [];
        var trendResolved = [];
        var trendSla = [];

        try {
            trendDates = JSON.parse(chartDataEl.getAttribute('data-dates') || '[]');
            trendIncoming = JSON.parse(chartDataEl.getAttribute('data-incoming') || '[]');
            trendResolved = JSON.parse(chartDataEl.getAttribute('data-resolved') || '[]');
            trendSla = JSON.parse(chartDataEl.getAttribute('data-sla') || '[]');
        } catch (e) {
            console.error('Error parsing dashboard chart data:', e);
        }

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
                            label: function (context) {
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
            // Update button toggle states
            document.querySelectorAll('.mini-toggle').forEach(function (el) {
                if (el.getAttribute('data-metric') === metric) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });

            // Update connected SLA metric card states
            document.querySelectorAll('.sla-item.metric-card').forEach(function (card) {
                if (card.getAttribute('data-metric') === metric) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });

            // Update title and pulse indicator
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
    }

    // Modal filter handler
    window.openFilterModal = function () {
        var modal = document.getElementById('filterModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(function () {
                modal.focus();
            }, 100);
        }
    };

    window.closeFilterModal = function () {
        var modal = document.getElementById('filterModal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    window.applyFilter = function () {
        var params = [];
        var form = document.getElementById('filterForm');
        if (form) {
            for (var i = 0; i < form.elements.length; i++) {
                var el = form.elements[i];
                if (el.name && el.value) {
                    params.push(el.name + '=' + encodeURIComponent(el.value));
                }
            }
        }
        var perPageEl = document.getElementById('perPage');
        if (perPageEl) {
            params.push('per_page=' + encodeURIComponent(perPageEl.value));
        }
        window.location = '?' + params.join('&');
    };

    window.resetFilter = function () {
        var perPageEl = document.getElementById('perPage');
        var perPage = perPageEl ? perPageEl.value : 10;
        window.location = '?per_page=' + encodeURIComponent(perPage);
    };

    // Table quick search handlers
    window.searchTicketTable = function () {
        var input = document.getElementById("searchTicket");
        if (!input) return;
        var filter = input.value.trim();
        var url = new URL(window.location.href);

        if (filter === '') {
            url.searchParams.delete('search');
        } else {
            url.searchParams.set('search', filter);
        }
        url.searchParams.set('page', 1);
        window.location = url.pathname + '?' + url.searchParams.toString();
    };

    window.handleSearchKeypress = function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchTicketTable();
        }
    };

    // Keyboard & date constraints
    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") {
            closeFilterModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        initSparklineChart();

        var startInput = document.getElementById('startDate');
        var endInput = document.getElementById('endDate');
        if (startInput && endInput) {
            function updateEndMin() {
                endInput.min = startInput.value;
                if (endInput.value && endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
            }
            startInput.addEventListener('change', updateEndMin);
            if (startInput.value) updateEndMin();
        }
    });
})();
