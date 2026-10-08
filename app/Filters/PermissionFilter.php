<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Superadmin;

class PermissionFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    $role = session('role') ?? '';
    $username = session('username') ?? '';
    $userPermissions = session('user_permissions') ?? session('permissions') ?? [];

    // Superadmin bypass
    $superadminConfig = new \Config\Superadmin();
    $cleanRole = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]/', '', (string)$role)));
    if (
      $cleanRole === 'superadmin' ||
      (!empty($superadminConfig->username) && strtolower($username) === strtolower($superConfig->username ?? $superadminConfig->username))
    ) {
      return;
    }

    // Ambil parameter dari route (misal: 'settings.read')
    $requiredParam = $arguments[0] ?? null;
    if (!$requiredParam) {
      return;
    }

    // Ubah titik ke titik dua agar cocok dengan database (settings.read -> settings:read)
    $requiredCode = str_replace('.', ':', $requiredParam);

    $hasPermission = in_array($requiredCode, $userPermissions, true)
      || in_array($requiredParam, $userPermissions, true);

    if (!$hasPermission) {
      $isAjax = ($request instanceof \CodeIgniter\HTTP\IncomingRequest && $request->isAJAX())
        || service('request')->isAJAX();

      if ($isAjax) {
        return service('response')->setStatusCode(403)->setJSON([
          'status' => 'error',
          'message' => "Forbidden: Anda tidak memiliki akses [{$requiredCode}]."
        ]);
      }

      return redirect()->to('/admin/forbidden');
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // No action needed
  }
}