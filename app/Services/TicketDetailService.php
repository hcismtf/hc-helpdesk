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
        $resolvePicUser = function(?string $userId, string $defaultDept, string $defaultRole) use ($userModel, $repliesRaw): array {
            $picData = [
                'name'       => 'Unassigned',
                'initials'   => '--',
                'department' => $defaultDept,
                'role_title' => $defaultRole,
                'id'         => null,
                'is_assigned'=> false,
            ];

            if (empty($userId)) {
                return $picData;
            }

            $u = $userModel->getUserWithRole($userId);
            if (!$u) {
                $uRow = $userModel->where('employee_no', $userId)->orWhere('username', $userId)->first();
                if ($uRow) {
                    $u = $userModel->getUserWithRole($uRow['id']);
                }
            }

            if ($u) {
                $nameParts = preg_split('/\s+/', trim((string) $u['name']));
                $initials = (count($nameParts) >= 2) 
                    ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                    : strtoupper(substr($nameParts[0], 0, 2));

                $roleTitle = !empty($u['role_name'])
                    ? $u['role_name']
                    : (!empty($u['job_title'])
                        ? $u['job_title']
                        : (!empty($u['position']) ? $u['position'] : $defaultRole));

                $picData = [
                    'name'       => $u['name'],
                    'initials'   => $initials,
                    'department' => $defaultDept,
                    'role_title' => $roleTitle,
                    'id'         => $u['id'],
                    'is_assigned'=> true,
                ];
            }

            return $picData;
        };

        // PIC Helpdesk ID
        $picHelpdeskId = !empty($ticket['pic_helpdesk_id']) ? $ticket['pic_helpdesk_id'] : (!empty($ticket['assigned_to']) ? $ticket['assigned_to'] : null);
        // Fallback dari replies jika belum tersimpan langsung
        if (empty($picHelpdeskId) && !empty($repliesRaw)) {
            for ($i = count($repliesRaw) - 1; $i >= 0; $i--) {
                $r = $repliesRaw[$i];
                $rAssigned = is_object($r) ? ($r->assigned_to ?? null) : ($r['assigned_to'] ?? null);
                if (!empty($rAssigned)) {
                    $picHelpdeskId = $rAssigned;
                    break;
                }
            }
        }
        $assignedPicHelpdesk = $resolvePicUser($picHelpdeskId, 'Helpdesk Support', 'PIC Helpdesk');
        $assignedSpecialist = $assignedPicHelpdesk; // backward compatibility

        // PIC Developer / BA ID (Optional)
        $picDevBaId = !empty($ticket['pic_dev_ba_id']) ? $ticket['pic_dev_ba_id'] : null;
        $assignedPicDevBa = $resolvePicUser($picDevBaId, 'Development & BA', 'Developer / BA');

        // 2. SLA Calculations
        $priority = strtolower($ticket['ticket_priority'] ?? 'medium');
        $slaModel = new SlaModel();
        $slaRow = $slaModel->where('LOWER(priority)', $priority)->first();

        $slaResolutionHours = $slaRow ? (float)$slaRow['resolution_time'] : 24.0;
        $slaResponseHours   = $slaRow ? (float)$slaRow['response_time'] : 8.0;

        $createdTime = !empty($ticket['created_date']) ? strtotime($ticket['created_date']) : time();
        $targetResolutionTime = $createdTime + ($slaResolutionHours * 3600);

        // Ambil first response dan resolved time dinamis dari responses jika ada
        $firstResponseTime = !empty($ticket['first_response_at']) ? strtotime($ticket['first_response_at']) : null;
        $resolvedTime = (!empty($ticket['finish_date']) && in_array(strtolower($ticket['ticket_status'] ?? ''), ['closed', 'done', 'resolved'])) 
            ? strtotime($ticket['finish_date']) 
            : null;

        if (!$firstResponseTime && !empty($repliesRaw)) {
            $sortedReplies = $repliesRaw;
            usort($sortedReplies, fn($a, $b) => strtotime($a['created_at'] ?? $a['created_date'] ?? 'now') <=> strtotime($b['created_at'] ?? $b['created_date'] ?? 'now'));
            $firstReply = $sortedReplies[0] ?? null;
            if ($firstReply) {
                $firstResponseTime = strtotime($firstReply['created_at'] ?? $firstReply['created_date']);
            }
        }

        if (!$resolvedTime && !empty($repliesRaw)) {
            foreach ($repliesRaw as $rep) {
                $repStatus = strtolower(is_object($rep) ? ($rep->getStatus() ?? '') : ($rep['status'] ?? ''));
                if (in_array($repStatus, ['resolved', 'done', 'closed'])) {
                    $resolvedTime = strtotime(is_object($rep) ? (string) ($rep->getCreatedDate() ?? 'now') : ($rep['created_date'] ?? ($rep['created_at'] ?? 'now')));
                    break;
                }
            }
        }

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

        // Ambil riwayat dari ticket_audit_log
        try {
            $auditModel = new \App\Models\TicketAuditLogModel();
            $auditLogs = $auditModel->where('ticket_id', $ticket['id'])->orderBy('created_date', 'asc')->findAll();
            
            // Helper cache lokal untuk mencari nama user berdasarkan ID atau NIP
            $userNameCache = [];
            $getUserName = function(?string $raw) use (&$userNameCache, $userModel): string {
                if (empty($raw) || $raw === '-' || $raw === 'Unassigned') return 'Unassigned';
                if (isset($userNameCache[$raw])) return $userNameCache[$raw];

                // Coba cari by ID UUID
                $found = $userModel->find($raw);
                if (!$found) {
                    // Coba cari by employee_no (NIP) atau username
                    $found = $userModel->where('employee_no', $raw)->orWhere('username', $raw)->first();
                }

                if ($found) {
                    $name = is_object($found) ? ($found->name ?? $found->getName()) : ($found['name'] ?? $raw);
                    $userNameCache[$raw] = $name;
                    return $name;
                }

                $userNameCache[$raw] = $raw;
                return $raw;
            };

            foreach ($auditLogs as $al) {
                $auditCount++;
                $alDate = is_object($al) ? (string) $al->getCreatedDate() : ($al['created_date'] ?? date('Y-m-d H:i:s'));
                $rawActor = is_object($al) ? $al->getActorName() : ($al['actor_name'] ?? 'System');
                $reason = is_object($al) ? $al->getReasonNotes() : ($al['reason_notes'] ?? '');
                $field = is_object($al) ? $al->getFieldName() : ($al['field_name'] ?? '');
                $oldV = is_object($al) ? $al->getOldValue() : ($al['old_value'] ?? '');
                $newV = is_object($al) ? $al->getNewValue() : ($al['new_value'] ?? '');

                // Pastikan nama actor adalah Nama Lengkap (bukan NIP / ID numerik)
                $actor = $getUserName($rawActor);

                // Jika field adalah assigned_to atau specialist, resolve old_value & new_value ke Nama Lengkap
                if (in_array(strtolower((string)$field), ['assigned_to', 'specialist', 'pic', 'assignee'])) {
                    $fieldLabel = 'specialist';
                    $oldVLabel = $getUserName($oldV);
                    $newVLabel = $getUserName($newV);
                } elseif (strtolower((string)$field) === 'status') {
                    $fieldLabel = 'status';
                    $oldVLabel = \App\Entities\TicketStatus::getLabel($oldV);
                    $newVLabel = \App\Entities\TicketStatus::getLabel($newV);
                } elseif (strtolower((string)$field) === 'priority') {
                    $fieldLabel = 'priority';
                    $oldVLabel = ucfirst(strtolower((string)$oldV));
                    $newVLabel = ucfirst(strtolower((string)$newV));
                } else {
                    $fieldLabel = str_replace('_', ' ', (string)$field);
                    $oldVLabel = $oldV ?: '-';
                    $newVLabel = $newV ?: '-';
                }

                $text = "{$actor} changed {$fieldLabel} from '{$oldVLabel}' to '{$newVLabel}'";
                if (!empty($reason)) {
                    $text .= " &bull; Note: {$reason}";
                }

                $activities[] = [
                    'type'           => 'audit',
                    'text'           => $text,
                    'created_at'     => $alDate,
                    'time_ago'       => $this->timeAgo($alDate),
                    'formatted_date' => date('d M Y, H:i', strtotime($alDate)) . ' WIB'
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal memuat audit logs: ' . $e->getMessage());
        }

        $internalNotesCount = 0;
        $publicCommentsCount = 0;

        foreach ($repliesRaw as $r) {
            $text = is_object($r) ? ($r->getReply() ?? $r->reply ?? '') : ($r['reply'] ?? '');
            $isInternal = (int) (is_object($r) ? ($r->isInternal() ? 1 : 0) : ($r['is_internal'] ?? 0));
            $isSystem = (stripos($text, 'Status changed to') !== false 
                || stripos($text, 'changed status to') !== false 
                || stripos($text, 'Assigned to') !== false
                || stripos($text, 'assigned specialist') !== false);

            $replyDate = is_object($r) 
                ? (string) ($r->getCreatedDate() ?? $r->created_date ?? date('Y-m-d H:i:s')) 
                : ($r['created_date'] ?? ($r['created_at'] ?? date('Y-m-d H:i:s')));

            if ($isSystem) {
                // Bersihkan embel-embel "via Kanban Board" atau "via Kanban"
                $cleanText = preg_replace('/\s+via\s+Kanban(\s+Board)?/i', '', $text);
                
                // Jika teks belum mencantumkan siapa pengubahnya, cantumkan author_name
                $authorName = is_object($r) ? ($r->getAuthorName() ?? $r->author_name) : ($r['author_name'] ?? null);
                if (!empty($authorName) && !str_starts_with($cleanText, $authorName) && stripos($cleanText, 'Status changed to') === 0) {
                    $cleanText = $authorName . ' ' . lcfirst($cleanText);
                }

                $auditCount++;
                $activities[] = [
                    'type'       => 'audit',
                    'text'       => $cleanText,
                    'created_at' => $replyDate,
                    'time_ago'   => $this->timeAgo($replyDate),
                    'formatted_date' => date('d M Y, H:i', strtotime($replyDate)) . ' WIB'
                ];
            } else {
                $commentsCount++;
                if ($isInternal === 1) {
                    $internalNotesCount++;
                } else {
                    $publicCommentsCount++;
                }

                $responseAuthor = is_object($r) ? ($r->getAuthorName() ?? $r->author_name) : ($r['author_name'] ?? null);
                $responseUserId = is_object($r) ? ($r->getUserId() ?? $r->user_id) : ($r['user_id'] ?? ($r['submitted_by'] ?? null));

                $isUser = ($responseUserId === 'user')
                    || (!empty($responseUserId) && $responseUserId == ($ticket['reporter_id'] ?? ''))
                    || (!empty($responseAuthor) && $responseAuthor == ($ticket['created_by'] ?? ''));

                if ($isUser) {
                    $authorName = $responseAuthor ?: ($ticket['created_by'] ?? 'Requester');
                    $roleBadge = 'Ticket Reporter';
                    $badgeClass = 'badge-reporter';
                    $avatarBg = 'avatar-f';
                } else {
                    if (empty($responseAuthor) && !empty($responseUserId)) {
                        $admin = $userModel->find($responseUserId);
                        if ($admin) {
                            $responseAuthor = $admin['name'];
                        }
                    }
                    $authorName = $responseAuthor ?: ($assignedSpecialist['name'] !== 'Unassigned' ? $assignedSpecialist['name'] : 'Support Agent');
                    $roleBadge = $isInternal ? 'Internal Staff' : 'Support Lead';
                    $badgeClass = $isInternal ? 'badge-internal' : 'badge-support';
                    $avatarBg = $isInternal ? 'avatar-internal' : 'avatar-am';
                }

                $activities[] = [
                    'type'           => 'comment',
                    'is_user'        => $isUser,
                    'is_internal'    => $isInternal === 1,
                    'author'         => $authorName,
                    'initials'       => strtoupper(substr(trim($authorName), 0, 2)),
                    'avatar_bg'      => $avatarBg,
                    'role_badge'     => $roleBadge,
                    'badge_class'    => $badgeClass,
                    'created_at'     => $replyDate,
                    'formatted_date' => date('d M Y, H:i', strtotime($replyDate)) . ' WIB',
                    'time_ago'       => $this->timeAgo($replyDate),
                    'text'           => $text
                ];
            }
        }

        // Urutkan seluruh activities berdasarkan timestamp ascending
        usort($activities, fn($a, $b) => strtotime($a['created_at']) <=> strtotime($b['created_at']));

        // 4. Requester Profile & Avatar Initials
        $reqName = $ticket['emp_name'] ?? 'Employee';
        $reqInitials = strtoupper(substr(trim($reqName), 0, 2));

        return [
            'ticket'               => $ticket,
            'assignedSpecialist'   => $assignedSpecialist,
            'picHelpdesk'          => $assignedPicHelpdesk,
            'picDevBa'             => $assignedPicDevBa,
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
                'resolved_time'    => $resolvedFormatted,
                'created_ts'       => $createdTime,
                'target_ts'        => (int) $targetResolutionTime,
                'resolved_ts'      => $resolvedTime,
                'is_finished'      => !empty($resolvedTime)
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
            'internalNotesCount'   => $internalNotesCount,
            'publicCommentsCount'  => $publicCommentsCount,
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
    public function timeAgo(?string $datetime = null): string
    {
        if (empty($datetime)) {
            return 'Just now';
        }
        $time = strtotime($datetime);
        if (!$time) {
            return 'Just now';
        }
        $diff = max(0, time() - $time);

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('d M Y', $time);
    }
}
