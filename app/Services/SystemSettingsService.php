<?php

namespace App\Services;

use App\Models\FaqModel;
use App\Models\RoleModel;
use App\Models\RolePermissionsModel;
use App\Models\PermissionsModel;
use App\Models\RequestTypeModel;
use App\Models\SlaModel;
use App\Models\UserModel;

class SystemSettingsService
{
    protected FaqModel $faqModel;
    protected RoleModel $roleModel;
    protected RolePermissionsModel $rolePermissionsModel;
    protected PermissionsModel $permissionsModel;
    protected RequestTypeModel $requestTypeModel;
    protected SlaModel $slaModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
        $this->roleModel = new RoleModel();
        $this->rolePermissionsModel = new RolePermissionsModel();
        $this->permissionsModel = new PermissionsModel();
        $this->requestTypeModel = new RequestTypeModel();
        $this->slaModel = new SlaModel();
        $this->userModel = new UserModel();
    }

    /**
     * Get summary counts for settings tabs header
     */
    public function getCounts(): array
    {
        return [
            'roles'        => $this->roleModel->countAllResults(),
            'slas'         => $this->slaModel->countAllResults(),
            'faqs'         => $this->faqModel->countAllResults(),
            'requestTypes' => $this->requestTypeModel->countAllResults(),
        ];
    }

    /**
     * Get enriched roles with member avatars and authorized permission badges
     */
    public function getEnrichedRoles(int $page = 1, int $perPage = 10): array
    {
        $total = $this->roleModel->countAllResults();
        $roles = $this->roleModel->orderBy('id', 'asc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        // Ambil semua user aktif beserta role_id-nya (1 user = 1 role)
        $allUsers = $this->userModel
            ->select('users.id, users.name, users.email, users.role_id')
            ->groupStart()
                ->where('users.is_deleted !=', 1)
                ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->findAll();

        $roleUsersMap = [];
        foreach ($allUsers as $u) {
            $rId = (string) ($u['role_id'] ?? '');
            if ($rId === '') {
                continue;
            }
            if (!isset($roleUsersMap[$rId])) {
                $roleUsersMap[$rId] = [];
            }
            $roleUsersMap[$rId][] = [
                'id'       => $u['id'],
                'name'     => $u['name'] ?? 'User',
                'initials' => strtoupper(substr(trim($u['name'] ?? 'U'), 0, 2))
            ];
        }

        foreach ($roles as &$role) {
            $rId = (string) $role['id'];
            
            // Menggunakan method relasi di RoleModel: getPermissions & getPermissionIds
            $permList = $this->roleModel->getPermissions($rId);
            $permIds  = $this->roleModel->getPermissionIds($rId);
            $permNames = array_column($permList, 'name');

            $role['permissions']    = $permList;
            $role['permission_ids'] = $permIds;
            $role['menu_access']    = implode(', ', $permNames);
            $role['members']        = $roleUsersMap[$rId] ?? [];
            $role['members_count']  = count($role['members']);

            // Human-readable scope descriptions
            $roleNameLower = strtolower($role['name'] ?? '');
            if (strpos($roleNameLower, 'super') !== false || strpos($roleNameLower, 'admin') !== false) {
                $role['scope_desc'] = 'Full unrestricted access across all tickets, system settings, and user provisioning.';
                $role['badge_type'] = 'SYSTEM DEFAULT';
            } elseif (strpos($roleNameLower, 'lead') !== false || strpos($roleNameLower, 'supervisor') !== false) {
                $role['scope_desc'] = 'Can triage, reassign, update SLA policies, and manage operational ticket workflows.';
                $role['badge_type'] = 'ROLE POLICY';
            } elseif (strpos($roleNameLower, 'agent') !== false || strpos($roleNameLower, 'support') !== false) {
                $role['scope_desc'] = 'Handles incoming customer inquiries, resolves assigned tickets, and creates internal notes.';
                $role['badge_type'] = 'ROLE POLICY';
            } elseif (strpos($roleNameLower, 'audit') !== false || strpos($roleNameLower, 'viewer') !== false) {
                $role['scope_desc'] = 'Read-only access for compliance reporting. Cannot alter tickets or system settings.';
                $role['badge_type'] = 'AUDIT';
            } else {
                $role['scope_desc'] = 'Standard workspace member with customized modular permissions.';
                $role['badge_type'] = 'CUSTOM ROLE';
            }
        }
        unset($role);

        return [
            'roles'      => $roles,
            'total'      => $total,
            'totalPages' => $totalPages,
            'page'       => $page,
            'perPage'    => $perPage
        ];
    }

    /**
     * Get enriched SLAs with tier badges and escalation triggers
     */
    public function getEnrichedSlas(int $page = 1, int $perPage = 10): array
    {
        $total = $this->slaModel->countAllResults();
        $slas = $this->slaModel->orderBy('id', 'asc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        foreach ($slas as &$sla) {
            $p = ucfirst(strtolower($sla['priority'] ?? 'Medium'));
            $sla['priority_clean'] = $p;

            switch ($p) {
                case 'Urgent':
                    $sla['tier_code'] = 'P1';
                    $sla['badge_class'] = 'tier-urgent';
                    $sla['escalation_trigger'] = 'Notify On-Call Lead via PagerDuty / WA';
                    $sla['business_hours'] = '24/7/365 Non-stop';
                    break;
                case 'High':
                    $sla['tier_code'] = 'P2';
                    $sla['badge_class'] = 'tier-high';
                    $sla['escalation_trigger'] = 'Escalate to Supervisor if 75% elapsed';
                    $sla['business_hours'] = 'Mon - Sat (08:00 - 20:00)';
                    break;
                case 'Medium':
                    $sla['tier_code'] = 'P3';
                    $sla['badge_class'] = 'tier-medium';
                    $sla['escalation_trigger'] = 'Reminder alert to assigned agent';
                    $sla['business_hours'] = 'Standard Workdays (08:30 - 17:30)';
                    break;
                case 'Low':
                default:
                    $sla['tier_code'] = 'P4';
                    $sla['badge_class'] = 'tier-low';
                    $sla['escalation_trigger'] = 'Standard ticket queue handling';
                    $sla['business_hours'] = 'Standard Workdays (08:30 - 17:30)';
                    break;
            }

            $respHours = (int) ($sla['response_time'] ?? 0);
            $sla['resp_formatted'] = ($respHours < 1) ? '30 mins' : ($respHours === 1 ? '1 hour' : $respHours . ' hours');

            $resHours = (int) ($sla['resolution_time'] ?? 0);
            $sla['res_formatted'] = ($resHours < 24) ? $resHours . ' hours' : round($resHours / 24, 1) . ' days (' . $resHours . 'h)';
        }
        unset($sla);

        return [
            'slas'       => $slas,
            'total'      => $total,
            'totalPages' => $totalPages,
            'page'       => $page,
            'perPage'    => $perPage
        ];
    }
}
