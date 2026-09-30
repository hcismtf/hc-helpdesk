<?php

namespace App\Services;

use DateTime;
use App\Models\SlaModel;
use App\Models\UserModel;

class TicketDetailService
{
    protected TicketMetricsService $metricsService;

    public function __construct()
    {
        $this->metricsService = new TicketMetricsService();
    }

    /**
     * Compute comprehensive detail view data for a ticket
     *
     * @param array $ticket
     * @param array $repliesRaw
     * @param array $attachments
     * @param UserModel $userModel
     * @return array
     */
    public function buildDetailViewData(array $ticket, array $repliesRaw, array $attachments, UserModel $userModel): array
    {
        // 1. Resolve Assigned Specialist
        $assignedUser = null;
        $assignedSpecialist = [
            'name'       => 'Unassigned',
            'initials'   => '--',
            'department' => 'Helpdesk Support',
            'role_title' => 'Support Specialist',
            'id'         => null
        ];

        if (!empty($ticket['assigned_to'])) {
            $assignedUser = $userModel->getUserWithRole($ticket['assigned_to']);
            if ($assignedUser) {
                $assignedSpecialist = [
                    'name'       => $assignedUser['name'],
                    'initials'   => strtoupper(substr(trim($assignedUser['name']), 0, 2)),
                    'department' => 'Technical Support',
                    'role_title' => $assignedUser['role_name'] ?? 'Support Specialist',
                    'id'         => $assignedUser['id']
                ];
            }
        }

        // 2. SLA Calculations
        $priority = strtolower($ticket['ticket_priority'] ?? 'medium');
        $slaModel = new SlaModel();
        $slaRow = $slaModel->where('LOWER(priority)', $priority)->first();

        $slaResolutionHours = $slaRow ? (float)$slaRow['resolution_time'] : 24.0;
        $slaResponseHours   = $slaRow ? (float)$slaRow['response_time'] : 8.0;

        $createdTime = !empty($ticket['created_date']) ? strtotime($ticket['created_date']) : time();
        $targetResolutionTime = $createdTime + ($slaResolutionHours * 3600);
        $firstResponseTime = !empty($ticket['first_response_at']) ? strtotime($ticket['first_response_at']) : null;
        $resolvedTime = (!empty($ticket['finish_date']) && in_array(strtolower($ticket['ticket_status']), ['closed', 'done'])) 
            ? strtotime($ticket['finish_date']) 
            : null;

        // Elapsed / duration
        $currentTime = $resolvedTime ?: time();
        $elapsedSeconds = max(0, $currentTime - $createdTime);
        $elapsedFormatted = $this->metricsService->formatDuration($elapsedSeconds);
        $targetFormatted = ($slaResolutionHours >= 1 ? (int)$slaResolutionHours . 'h ' : '') . (($slaResolutionHours * 60) % 60 ? ((int)($slaResolutionHours * 60) % 60) . 'm' : '00m');

        // SLA Compliance Status
        $isCompliant = ($currentTime <= $targetResolutionTime);
        $slaPercentage = min(100, max(5, round(($elapsedSeconds / max(1, ($slaResolutionHours * 3600))) * 100)));
        if ($resolvedTime && $isCompliant) {
            $slaBadgeText = "SLA Met ({$elapsedFormatted})";
            $slaBadgeClass = "sla-met";
        } elseif (!$isCompliant) {
            $slaBadgeText = "SLA Breached";
            $slaBadgeClass = "sla-breached";
        } else {
            $slaBadgeText = "SLA In Progress";
            $slaBadgeClass = "sla-pending";
        }

        // Lifecycle response metrics
        $firstResponseFormatted = '--';
        if ($firstResponseTime) {
            $respDuration = max(0, $firstResponseTime - $createdTime);
            $firstResponseFormatted = date('H:i', $firstResponseTime) . ' (' . $this->metricsService->formatDuration($respDuration) . ')';
        }

        $resolvedFormatted = '--';
        if ($resolvedTime) {
            $resDuration = max(0, $resolvedTime - $createdTime);
            $resolvedFormatted = date('H:i', $resolvedTime) . ' (' . $this->metricsService->formatDuration($resDuration) . ')';
        }

        // 3. Process Activity & Replies (Chat Timeline + Audit System Events)
        $activities = [];
        $commentsCount = 0;
        $auditCount = 0;

        foreach ($repliesRaw as $r) {
            $text = $r['reply'] ?? '';
            $isSystem = (strpos($text, 'Status changed to') !== false || strpos($text, 'Assigned to') !== false);

            if ($isSystem) {
                $auditCount++;
                $activities[] = [
                    'type'       => 'audit',
                    'text'       => $text,
                    'created_at' => $r['created_at'],
                    'time_ago'   => $this->timeAgo($r['created_at']),
                    'formatted_date' => date('d M Y, H:i', strtotime($r['created_at'])) . ' WIB'
                ];
            } else {
                $commentsCount++;
                $isUser = ($r['submitted_by'] === 'user' || $r['submitted_by'] == ($ticket['emp_id'] ?? ''));
                if ($isUser) {
                    $authorName = $ticket['emp_name'] ?? 'Requester';
                    $roleBadge = 'Ticket Reporter';
                    $badgeClass = 'badge-reporter';
                    $avatarBg = 'avatar-f';
                } else {
                    $admin = $userModel->find($r['submitted_by']);
                    $authorName = $admin ? $admin['name'] : ($assignedSpecialist['name'] !== 'Unassigned' ? $assignedSpecialist['name'] : 'Support Agent');
                    $roleBadge = 'Support Lead';
                    $badgeClass = 'badge-support';
                    $avatarBg = 'avatar-am';
                }

                $activities[] = [
                    'type'           => 'comment',
                    'is_user'        => $isUser,
                    'author'         => $authorName,
                    'initials'       => strtoupper(substr(trim($authorName), 0, 2)),
                    'avatar_bg'      => $avatarBg,
                    'role_badge'     => $roleBadge,
                    'badge_class'    => $badgeClass,
                    'created_at'     => $r['created_at'],
                    'formatted_date' => date('d M Y, H:i', strtotime($r['created_at'])) . ' WIB',
                    'time_ago'       => $this->timeAgo($r['created_at']),
                    'text'           => $text
                ];
            }
        }

        // 4. Requester Profile & Avatar Initials
        $reqName = $ticket['emp_name'] ?? 'Employee';
        $reqInitials = strtoupper(substr(trim($reqName), 0, 2));

        return [
            'ticket'               => $ticket,
            'assignedSpecialist'   => $assignedSpecialist,
            'sla' => [
                'resolution_hours' => $slaResolutionHours,
                'response_hours'   => $slaResponseHours,
                'elapsed_formatted'=> $elapsedFormatted,
                'target_formatted' => $targetFormatted,
                'percentage'       => $slaPercentage,
                'is_compliant'     => $isCompliant,
                'badge_text'       => $slaBadgeText,
                'badge_class'      => $slaBadgeClass,
                'first_response'   => $firstResponseFormatted,
                'resolved_time'    => $resolvedFormatted
            ],
            'lifecycle' => [
                'created'          => !empty($ticket['created_date']) ? date('d M Y, H:i', strtotime($ticket['created_date'])) : '-',
                'first_response'   => $firstResponseFormatted,
                'resolved'         => $resolvedFormatted,
                'updated_ago'      => !empty($ticket['modified_date']) ? $this->timeAgo($ticket['modified_date']) : $this->timeAgo($ticket['created_date'] ?? 'now')
            ],
            'requester' => [
                'name'             => $reqName,
                'initials'         => $reqInitials,
                'nip'              => $ticket['emp_nip'] ?? '-',
                'email'            => $ticket['email'] ?? '-',
                'phone'            => $ticket['wa_no'] ?? '-',
                'position'         => $ticket['emp_position'] ?? 'Employee Staff',
                'department'       => 'Operations & Logistics',
                'location'         => 'Jakarta HQ, Floor 8'
            ],
            'activities'           => $activities,
            'commentsCount'        => $commentsCount,
            'auditCount'           => $auditCount,
            'attachments'          => $attachments
        ];
    }

    /**
     * Convert timestamp to relative human readable string (e.g. 35m ago, 2h ago)
     *
     * @param string $datetime
     * @return string
     */
    public function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = max(0, time() - $time);

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('d M Y', $time);
    }
}
