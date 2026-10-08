<?php

namespace App\Services;

use DateTime;
use App\Models\TicketModel;

class TicketMetricsService
{
    /**
     * Format duration in seconds to human readable string (e.g. 2d 3h 15m)
     *
     * @param float|int $seconds
     * @return string
     */
    public function formatDuration($seconds): string
    {
        $seconds = (int) round($seconds);
        if ($seconds <= 0) {
            return '0m';
        }

        $d = floor($seconds / 86400);
        $h = floor(($seconds % 86400) / 3600);
        $m = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($d > 0) $parts[] = $d . 'd';
        if ($h > 0) $parts[] = $h . 'h';
        if ($m > 0 || empty($parts)) $parts[] = $m . 'm';

        return implode(' ', $parts);
    }

    /**
     * Inject SLA details and calculate due_date for a list of tickets
     *
     * @param array $tickets
     * @param array $slaMap
     * @return array
     */
    public function injectSlaData(array $tickets, array $slaMap): array
    {
        foreach ($tickets as &$ticket) {
            $priority = $ticket['ticket_priority'] ?? '';
            $sla = $slaMap[$priority] ?? null;

            if ($sla) {
                $ticket['sla_response_time'] = $sla['response_time'];
                $ticket['sla_resolution_time'] = $sla['resolution_time'];
                if (empty($ticket['due_date']) && !empty($ticket['created_date'])) {
                    $created = new DateTime($ticket['created_date']);
                    $created->modify('+' . $sla['resolution_time'] . ' hours');
                    $ticket['due_date'] = $created->format('Y-m-d H:i:s');
                }
            } else {
                $ticket['sla_response_time'] = 24;
                $ticket['sla_resolution_time'] = 24;
                if (empty($ticket['due_date']) && !empty($ticket['created_date'])) {
                    $created = new DateTime($ticket['created_date']);
                    $created->modify('+24 hours');
                    $ticket['due_date'] = $created->format('Y-m-d H:i:s');
                }
            }
        }
        unset($ticket);

        return $tickets;
    }

    /**
     * Calculate performance statistics from closed tickets
     *
     * @param array $closedTickets
     * @return array
     */
    public function calculatePerformanceMetrics(array $closedTickets): array
    {
        $totalResponse = 0;
        $totalResolution = 0;
        $slaCompliant = 0;
        $countClosed = count($closedTickets);

        foreach ($closedTickets as $t) {
            if (!empty($t['first_response_at']) && !empty($t['created_date'])) {
                $responseTime = strtotime($t['first_response_at']) - strtotime($t['created_date']);
                $totalResponse += max(0, $responseTime);
            }

            if (!empty($t['finish_date']) && !empty($t['created_date'])) {
                $resolutionTime = strtotime($t['finish_date']) - strtotime($t['created_date']);
                $totalResolution += max(0, $resolutionTime);

                if (!empty($t['due_date']) && strtotime($t['finish_date']) <= strtotime($t['due_date'])) {
                    $slaCompliant++;
                }
            }
        }

        $avgResponse = $countClosed ? $totalResponse / $countClosed : 0;
        $avgResolution = $countClosed ? $totalResolution / $countClosed : 0;
        $slaRate = $countClosed ? (int) round(($slaCompliant / $countClosed) * 100) : 0;

        return [
            'avgResponseStr'   => $this->formatDuration($avgResponse),
            'avgResolutionStr' => $this->formatDuration($avgResolution),
            'slaRate'          => $slaRate,
        ];
    }

    /**
     * Get 7-day trend metrics for interactive chart
     *
     * @param TicketModel $ticketModel
     * @return array
     */
    public function get7DaysTrend(TicketModel $ticketModel): array
    {
        $trendDates = [];
        $trendIncoming = [];
        $trendResolved = [];
        $trendSla = [];

        for ($i = 6; $i >= 0; $i--) {
            $dayDate = date('Y-m-d', strtotime("-$i days"));
            $dayLabel = date('D (d/m)', strtotime("-$i days"));
            $trendDates[] = $dayLabel;

            $inCount = (clone $ticketModel)->where('DATE(created_date)', $dayDate)->countAllResults();
            $trendIncoming[] = $inCount;

            $db = \Config\Database::connect();
            $resolvedRow = $db->table('ticket_response tr')
                ->select('COUNT(DISTINCT tr.ticket_id) as total_resolved, SUM(CASE WHEN t.resolution_due_date IS NOT NULL AND tr.created_date <= t.resolution_due_date THEN 1 ELSE 0 END) as total_compliant')
                ->join('ticket t', 't.id = tr.ticket_id', 'inner')
                ->where('DATE(tr.created_date)', $dayDate)
                ->whereIn('LOWER(tr.status)', ['closed', 'done', 'resolved'])
                ->get()
                ->getRowArray();

            $resCount = (int) ($resolvedRow['total_resolved'] ?? 0);
            $compCount = (int) ($resolvedRow['total_compliant'] ?? 0);

            $trendResolved[] = $resCount;
            $slaPct = $resCount > 0 ? (int) round(($compCount / $resCount) * 100) : 100;
            $trendSla[] = $slaPct;
        }

        return [
            'trendDates'    => $trendDates,
            'trendIncoming' => $trendIncoming,
            'trendResolved' => $trendResolved,
            'trendSla'      => $trendSla
        ];
    }
}
