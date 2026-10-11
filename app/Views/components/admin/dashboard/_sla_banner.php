<?php
/**
 * Admin Dashboard SLA Overview Component with Sparkline Trend
 *
 * @var string $avgResponseStr
 * @var string $avgResolutionStr
 * @var float  $slaRate
 */
?>
<div class="sla-overview-strip">
    <!-- Left Side: 3 Connected Metric Cards -->
    <div class="sla-metrics-group">
        <!-- Card 1: AVG Response Time -->
        <div class="sla-item metric-card active" data-metric="incoming"
            onclick="switchMiniChart('incoming', this)" title="Klik untuk melihat Tren Tiket Masuk">
            <div class="card-top-row">
                <div class="card-icon-title">
                    <div class="sla-icon-pill resp-pill">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
        <div class="sla-item metric-card" data-metric="resolved" onclick="switchMiniChart('resolved', this)"
            title="Klik untuk melihat Tren Tiket Selesai">
            <div class="card-top-row">
                <div class="card-icon-title">
                    <div class="sla-icon-pill reso-pill">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
        <div class="sla-item metric-card" data-metric="sla" onclick="switchMiniChart('sla', this)"
            title="Klik untuk melihat Tren Kepatuhan SLA %">
            <div class="card-top-row">
                <div class="card-icon-title">
                    <div class="sla-icon-pill comp-pill <?= $slaRate >= 80 ? 'good' : ($slaRate >= 60 ? 'warn' : 'danger') ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                        <div class="sla-progress-fill <?= $slaRate >= 80 ? 'good' : ($slaRate >= 60 ? 'warn' : 'danger') ?>"
                            style="width: <?= min(100, max(0, (int) $slaRate)) ?>%;"></div>
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
                <span id="chartActiveLabel">7-Day Trend: Incoming</span>
            </div>
            <div class="mini-chart-toggles">
                <button type="button" class="mini-toggle active" data-metric="incoming"
                    onclick="switchMiniChart('incoming', this)">Incoming</button>
                <button type="button" class="mini-toggle" data-metric="resolved"
                    onclick="switchMiniChart('resolved', this)">Resolved</button>
                <button type="button" class="mini-toggle" data-metric="sla"
                    onclick="switchMiniChart('sla', this)">SLA %</button>
            </div>
        </div>
        <div class="mini-chart-canvas-wrap">
            <canvas id="slaMiniSparkline"></canvas>
        </div>
    </div>
</div>
