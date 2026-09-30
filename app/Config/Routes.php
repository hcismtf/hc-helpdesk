<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================================================
// 1. PUBLIC ROUTES (Tanpa Autentikasi)
// =============================================================================

$routes->get('/', 'Ticket::create');
$routes->get('faq', 'Ticket::faq');
$routes->get('pusat-bantuan', 'PusatBantuan::pusat_bantuan');

$routes->group('ticket', static function ($routes) {
    $routes->get('/', 'Ticket::index');
    $routes->get('create', 'Ticket::create');
    $routes->post('store', 'Ticket::store');
    $routes->get('faq', 'Ticket::faq');
    $routes->get('detail/(:segment)', 'admin\TicketAdminController::Ticket_detail/$1');
});

// General Authentication
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');

// Assets / Static JS Helper
$routes->get('assets/ticket_js', 'Assets::ticket_js');


// =============================================================================
// 2. ADMIN AUTH ROUTES (Guest/Tanpa Login)
// =============================================================================
$routes->group('admin', ['namespace' => 'App\Controllers\admin'], static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('authenticate', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('forbidden', 'AuthController::forbidden');
});


// =============================================================================
// 3. ADMIN PROTECTED ROUTES (Wajib Login & Cek Hak Akses Role/Permission)
// =============================================================================
$routes->group('admin', [
    'namespace' => 'App\Controllers\admin',
    'filter'    => ['auth', 'permission']
], static function ($routes) {

    // --- Dashboard ---
    $routes->get('dashboard', 'DashboardController::dashboard');

    // --- Tickets & Kanban Swimlane ---
    $routes->get('Ticket_dashboard', 'TicketAdminController::Ticket_dashboard');
    $routes->get('Ticket_detail/(:segment)', 'TicketAdminController::Ticket_detail/$1');
    $routes->post('send_reply/(:segment)', 'TicketAdminController::send_reply/$1');
    $routes->post('update_ticket_status', 'TicketAdminController::update_ticket_status');
    $routes->get('view/(:any)', 'TicketAdminController::view/$1');

    // --- System Settings & Master Data ---
    $routes->get('system_settings', 'SystemSettingsController::system_settings');

    // FAQ Management
    $routes->post('add_faq', 'SystemSettingsController::add_faq');
    $routes->get('get_faq_list', 'SystemSettingsController::get_faq_list');
    $routes->post('edit_faq', 'SystemSettingsController::edit_faq');
    $routes->post('delete_faq', 'SystemSettingsController::delete_faq');

    // User Role Management
    $routes->get('get_user_role_list', 'SystemSettingsController::get_user_role_list');
    $routes->post('add_user_role', 'SystemSettingsController::add_user_role');
    $routes->post('edit_user_role', 'SystemSettingsController::edit_user_role');
    $routes->post('delete_user_role', 'SystemSettingsController::delete_user_role');

    // Request Type Management
    $routes->get('get_request_type_list', 'SystemSettingsController::get_request_type_list');
    $routes->post('add_request_type', 'SystemSettingsController::add_request_type');
    $routes->post('edit_request_type', 'SystemSettingsController::edit_request_type');
    $routes->post('delete_request_type', 'SystemSettingsController::delete_request_type');

    // SLA Settings Management
    $routes->post('add_sla', 'SystemSettingsController::add_sla');
    $routes->get('get_sla_list', 'SystemSettingsController::get_sla_list');
    $routes->post('edit_sla', 'SystemSettingsController::edit_sla');
    $routes->post('delete_sla', 'SystemSettingsController::delete_sla');
    $routes->get('get_used_request_types', 'SystemSettingsController::get_used_request_types');

    // Permissions Management
    $routes->post('add_permission', 'SystemSettingsController::add_permission');
    $routes->get('get_permission', 'SystemSettingsController::get_permission');
    $routes->post('edit_permission', 'SystemSettingsController::edit_permission');
    $routes->post('delete_permission', 'SystemSettingsController::delete_permission');

    // --- User Management ---
    $routes->get('user_mgt', 'UserManagementController::user_mgt');
    $routes->post('add_user', 'UserManagementController::add_user');
    $routes->post('edit_user', 'UserManagementController::edit_user');
    $routes->post('delete_user', 'UserManagementController::delete_user');
    $routes->get('get_user', 'UserManagementController::get_user');

    // --- Reports & Export Jobs ---
    $routes->get('report', '\App\Controllers\ReportController::index');
    $routes->get('report/ticket-detail', '\App\Controllers\ReportController::ticketDetail');
    $routes->get('report/sla-detail', '\App\Controllers\ReportController::slaDetail');
    $routes->get('report/sla-response', '\App\Controllers\ReportController::slaResponseComparison');
    $routes->get('report/sla-resolution', '\App\Controllers\ReportController::slaResolutionComparison');

    $routes->get('report_user', 'ReportUserController::report_user');
    $routes->post('submit_report_job', 'ReportUserController::submit_report_job');
    $routes->get('download_report/(:num)', 'ReportUserController::download_report/$1');
    $routes->post('delete_report_job/(:num)', 'ReportUserController::delete_report_job/$1');

    // --- Developer Options ---
    $routes->get('developer-options', 'DeveloperOptions::index');
});