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
      return redirect()->to('/admin/dashboard');
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

    // 1. Validasi kredensial ke Keycloak (menggunakan employee_no sebagai username login)
    $tokenData = $this->keycloakService->authenticate($username, $password);

    if (!$tokenData || !isset($tokenData['access_token'])) {
      return redirect()->back()->with('error', 'Username atau password salah / Akun tidak terdaftar di Keycloak');
    }

    $accessToken = $tokenData['access_token'];
    $tokenPayload = $this->keycloakService->parseTokenPayload($accessToken);

    // Employee number adalah username login itu sendiri
    $employeeNo = $username;

    // 2. Hit Spring Boot Helpdesk User dengan parameter employeeNo
    $helpdeskUser = $this->keycloakService->getHelpdeskUserData($employeeNo);

    if ($helpdeskUser === null) {
      return redirect()->back()->with('error', 'Gagal terhubung ke service Helpdesk atau data employee tidak ditemukan.');
    }

    // 3. Persiapkan mapping data user
    $userModel = new UserModel();

    // Nama asli diambil dari return response Spring Boot (bukan employee_no)
    $fullName = $helpdeskUser['name']
      ?? $helpdeskUser['fullName']
      ?? ($tokenPayload['name'] ?? $employeeNo);

    $email = $helpdeskUser['email']
      ?? ($tokenPayload['email'] ?? $employeeNo . '@mtf.co.id');

    // Cari user lokal berdasarkan employee_no terlebih dahulu
    $localUser = $userModel
      ->where('employee_no', $employeeNo)
      ->first();

    // Fallback pencarian via email jika belum ketemu
    if (!$localUser && !empty($email)) {
      $localUser = $userModel->where('email', $email)->first();
    }

    if (!$localUser) {
      // Buat record baru jika belum ada
      $newUserData = [
        'employee_no' => $employeeNo,
        'name' => $fullName,
        'email' => $email,
        'position' => $helpdeskUser['position'] ?? null,
        'position_level' => $helpdeskUser['positionLevel'] ?? ($helpdeskUser['position_level'] ?? null),
        'job_title' => $helpdeskUser['jobTitle'] ?? ($helpdeskUser['job_title'] ?? null),
        'status' => 'ACTIVE',
        'created_by' => 'SYSTEM_KEYCLOAK',
        'created_date' => date('Y-m-d H:i:s'),
        'last_login_time' => date('Y-m-d H:i:s'),
        'is_deleted' => 0,
        // role_id default null atau role viewer sesuai kebijakan sistem
        'role_id' => $helpdeskUser['role_id'] ?? null,
      ];

      $userId = $userModel->insert($newUserData, true);
      $localUser = $userModel->find($userId);
    } else {
      // Update data profil terbaru dari Spring Boot dan waktu login
      $userModel->update($localUser['id'], [
        'employee_no' => $employeeNo,
        'name' => $fullName,
        'email' => $email,
        'last_login_time' => date('Y-m-d H:i:s'),
        'is_deleted' => 0,
      ]);
      $localUser = $userModel->find($localUser['id']);
    }

    // 4. Validasi Kepemilikan Role & Permission
    $roleModel = new RoleModel();
    $userWithRole = $userModel->getUserWithRole($localUser['id']);
    $permissionCodes = $userModel->getUserPermissionCodes($localUser['id']);

    // Jika user tidak memiliki role atau tidak memiliki permission apapun
    if (empty($localUser['role_id']) || empty($userWithRole['role_name']) || empty($permissionCodes)) {
      // Hapus session/token auth sepenuhnya
      session()->destroy();

      // Redirect ke root "/"
      return redirect()->to('/')->with('error', 'Akses ditolak: Akun Anda belum memiliki role atau permission untuk mengakses sistem.');
    }

    // 5. Set Session jika lolos validasi role & permission
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