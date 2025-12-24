<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Superadmin;

class PermissionFilter implements FilterInterface
{
    protected $permissionFileMap = [
        'tickets'         => ['ticket_dashboard', 'ticket_detail', 'get_ticket_list', 'send_reply', 'ticket-detail'],
        'dashboard'       => ['dashboard'],
        'user_management' => [
            'user_mgt', 'get_user_list',
            'add_user', 'edit_user', 'delete_user','get_user' 
        ],
        'system_settings' => [
            'system_settings',
            'faq_list', 'get_faq_list',
            'user_role_list', 'get_user_role_list',
            'add_user_role', 'edit_user_role', 'delete_user_role', 
            'request_type', 'get_request_type_list',
            'add_request_type', 'edit_request_type', 'delete_request_type', 
            'sla_settings', 'get_sla_list',
            'add_sla', 'edit_sla', 'delete_sla', 
            'add_permission', 'get_permission', 'edit_permission', 'delete_permission', 
        ],
         'reports' => [
            'report_user', 'export_ticket_excel', 'export_sla_excel','submit_report_job', 'download_report' ,'delete_report_job'
        ],
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $userPermissions = session('user_permissions') ?? [];
        $role = session('role') ?? '';
        $username = session('username') ?? '';

        $superadminConfig = new Superadmin();
        if (
            strtolower($role) === 'superadmin' ||
            strtolower($username) === strtolower($superadminConfig->username)
        ) {
            return;
        }

        $uriObj = $request->getUri();
        $segment2 = $uriObj->getTotalSegments() >= 2 ? strtolower($uriObj->getSegment(2)) : '';
        if ($segment2 === '') return;

        // Exception: allow access to attachment view
        if ($segment2 === 'view') {
            return;
        }

        $allowed = false;
        foreach ($this->permissionFileMap as $permCode => $files) {
            if (in_array($segment2, $files) && in_array($permCode, $userPermissions)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return redirect()->to('/admin/forbidden');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}