<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Services\Auth\KeycloackAuthService;

class AuthController extends BaseController
{
  protected KeycloackAuthService $keycloakService;

  public function __construct()
  {
    $this->keycloakService = new KeycloackAuthService();
  }

  public function login()
  {
    if (session('isLoggedIn')) {
      $roleId = session('role_id');
      $role = strtolower(session('role') ?? '');
      if (!empty($roleId) || $role === 'superadmin') {
        return redirect()->to('/admin/dashboard');
      }
      return redirect()->to('/')->with('error', 'Akun Anda tidak memiliki hak akses administrator.');
    }
    return view('admin/login');
  }

  public function authenticate()
  {
    $username = trim($this->request->getPost('username') ?? '');
    $password = $this->request->getPost('password') ?? '';

    if (empty($username) || empty($password)) {
      return redirect()->back()->with('error', 'Username dan password tidak boleh kosong');
    }

    $tokenData = $this->keycloakService->authenticate($username, $password);

    if (!$tokenData || !isset($tokenData['access_token'])) {
      return redirect()->back()->with('error', 'Username atau password salah / Akun tidak terdaftar di Keycloak');
    }

    $accessToken = $tokenData['access_token'];
    $tokenPayload = $this->keycloakService->parseTokenPayload($accessToken);

    $employeeNo = $username;

    $helpdeskUser = $this->keycloakService->getHelpdeskUserData($employeeNo);

    if ($helpdeskUser === null) {
      return redirect()->back()->with('error', 'Gagal terhubung ke service Helpdesk atau data employee tidak ditemukan.');
    }

    $userModel = new UserModel();

    $fullName = $helpdeskUser['name']
      ?? $helpdeskUser['fullName']
      ?? ($tokenPayload['name'] ?? $employeeNo);

    $email = $helpdeskUser['officeMail']
      ?? ($tokenPayload['email'] ?? null);

    $localUser = $userModel
      ->where('employee_no', $employeeNo)
      ->first();

    if (!$localUser && !empty($email)) {
      $localUser = $userModel->where('email', $email)->first();
    }

    if (!$localUser) {
      $newUserData = [
        'employee_no'     => $employeeNo,
        'name'            => $fullName,
        'email'           => $email,
        'position'        => $helpdeskUser['position'] ?? null,
        'position_level'  => $helpdeskUser['positionLevel'] ?? ($helpdeskUser['position_level'] ?? null),
        'job_title'       => $helpdeskUser['jobTitle'] ?? ($helpdeskUser['job_title'] ?? null),
        'status'          => 'ACTIVE',
        'last_login_time' => date('Y-m-d H:i:s'),
        'is_deleted'      => 0,
        'role_id'         => $helpdeskUser['role_id'] ?? null,
      ];

      $userId = $userModel->insert($newUserData, true);
      $localUser = $userModel->find($userId);
    } else {
      $userModel->update($localUser['id'], [
        'employee_no'     => $employeeNo,
        'name'            => $fullName,
        'email'           => $email,
        'last_login_time' => date('Y-m-d H:i:s'),
        'is_deleted'      => 0,
      ]);
      $localUser = $userModel->find($localUser['id']);
    }

    $roleModel = new RoleModel();
    $userWithRole = $userModel->getUserWithRole($localUser['id']);
    $permissionCodes = $userModel->getUserPermissionCodes($localUser['id']);

    if (empty($localUser['role_id']) || empty($userWithRole['role_name']) || empty($permissionCodes)) {
      session()->destroy();

      return redirect()->to('/')->with('error', 'Akses ditolak: Akun Anda belum memiliki role atau permission untuk mengakses sistem.');
    }

    session()->set([
      'isLoggedIn' => true,
      'access_token' => $accessToken,
      'refresh_token' => $tokenData['refresh_token'] ?? null,
      'user_id' => $localUser['id'],
      'employee_no' => $employeeNo,
      'username' => $employeeNo,
      'name' => $localUser['name'],
      'email' => $localUser['email'],
      'role_id' => $localUser['role_id'],
      'role' => $userWithRole['role_name'],
      'role_name' => $userWithRole['role_name'],
      'helpdesk_data' => $helpdeskUser,
      'user_permissions' => $permissionCodes,
    ]);

    return redirect()->to('/admin/dashboard');
  }

  public function logout()
  {
    session()->destroy();
    return redirect()->to('/admin/login');
  }
}