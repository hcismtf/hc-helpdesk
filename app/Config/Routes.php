<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================================================
// 1. PUBLIC ROUTES (Tanpa Autentikasi)
// =============================================================================

$routes->get('/', 'Home::index');
$routes->get('beranda', 'Home::index');
$routes->post('track-ticket', 'Home::trackTicket');
$routes->get('faq', 'PusatBantuan::pusat_bantuan');
$routes->get('pusat-bantuan', 'PusatBantuan::pusat_bantuan');
$routes->get('dashboard', '\App\Controllers\User\DashboardController::index');

$routes->group('ticket', static function ($routes) {
	$routes->get('/', '\App\Controllers\Public\TicketController::create');
	$routes->get('create', '\App\Controllers\Public\TicketController::create');
	$routes->post('store', '\App\Controllers\Public\TicketController::store');
	$routes->get('track', '\App\Controllers\Public\TicketController::track');
	$routes->get('faq', 'PusatBantuan::pusat_bantuan');
	$routes->get('detail/(:segment)', '\App\Controllers\User\TicketController::show/$1', ['filter' => 'userauth']);
});

// Attachment Preview & Download (Transparent in-flight gzip decompress)
$routes->get('attachment/view/(:any)', '\App\Controllers\AttachmentController::view/$1');
$routes->get('attachment/download/(:any)', '\App\Controllers\AttachmentController::download/$1');

// User / Employee Tickets (Wajib Login Karyawan / UserAuth)
$routes->group('my', ['namespace' => 'App\Controllers\User', 'filter' => 'userauth'], static function ($routes) {
	$routes->get('tickets', 'TicketController::index');
	$routes->get('ticket/(:segment)', 'TicketController::show/$1');
	$routes->post('ticket/(:segment)/reply', 'TicketController::reply/$1');
	$routes->post('ticket/(:segment)/close', 'TicketController::close/$1');
});

// General Authentication & User Login (Popup /)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::ajaxLogin');
$routes->post('auth/refresh-token', 'Auth::refreshToken');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('logout', 'Auth::logout');

// Assets / Static JS Helper
$routes->get('assets/ticket_js', 'Assets::ticket_js');




// =============================================================================
// 3. ADMIN PROTECTED ROUTES (Wajib Login & Cek Hak Akses Role/Permission)
// =============================================================================
$routes->group('admin', ['namespace' => 'App\Controllers\admin', 'filter' => 'auth'], static function ($routes) {

	// --- Dashboard & Main Settings View ---
	$routes->get('', 'DashboardController::dashboard');
	$routes->get('/', 'DashboardController::dashboard');
	$routes->get('dashboard', 'DashboardController::dashboard');
	$routes->get('system_settings', 'SystemSettingsController::system_settings', ['filter' => 'perm:settings.read']);
	$routes->get('system_settings/roles', 'SystemSettingsController::roles_category', ['filter' => 'perm:role.read']);
	$routes->get('system_settings/sla', 'SystemSettingsController::sla_policies', ['filter' => 'perm:sla.read']);
	$routes->get('system_settings/request_types', 'SystemSettingsController::request_types', ['filter' => 'perm:request_type.read']);
	$routes->get('system_settings/faqs', 'SystemSettingsController::faqs', ['filter' => 'perm:faq.read']);

	// --- Tickets Management ---
	$routes->group('', static function ($routes) {
		$routes->get('ticket_dashboard', 'TicketAdminController::ticket_dashboard', ['filter' => 'perm:ticket.read']);
		$routes->get('ticket_detail/(:segment)', 'TicketAdminController::ticket_detail/$1', ['filter' => 'perm:ticket.read']);
		$routes->get('view/(:any)', 'TicketAdminController::view/$1', ['filter' => 'perm:ticket.read']);
		$routes->post('send_reply/(:segment)', 'TicketAdminController::send_reply/$1', ['filter' => 'perm:ticket.reply']);
		$routes->post('update_ticket_status', 'TicketAdminController::update_ticket_status', ['filter' => 'perm:ticket.status']);
		$routes->post('update_ticket_sla', 'TicketAdminController::update_ticket_sla', ['filter' => 'perm:ticket.update']);
	});

	// --- FAQ Management ---
	$routes->group('', static function ($routes) {
		$routes->get('get_faq_list', 'SystemSettingsController::get_faq_list', ['filter' => 'perm:faq.read']);
		$routes->post('add_faq', 'SystemSettingsController::add_faq', ['filter' => 'perm:faq.create']);
		$routes->post('edit_faq', 'SystemSettingsController::edit_faq', ['filter' => 'perm:faq.update']);
		$routes->post('delete_faq', 'SystemSettingsController::delete_faq', ['filter' => 'perm:faq.delete']);
	});

	// --- SLA Management ---
	$routes->group('', static function ($routes) {
		$routes->get('get_sla_list', 'SystemSettingsController::get_sla_list', ['filter' => 'perm:sla.read']);
		$routes->get('get_used_request_types', 'SystemSettingsController::get_used_request_types', ['filter' => 'perm:sla.read']);
		$routes->post('add_sla', 'SystemSettingsController::add_sla', ['filter' => 'perm:sla.create']);
		$routes->post('edit_sla', 'SystemSettingsController::edit_sla', ['filter' => 'perm:sla.update']);
		$routes->post('delete_sla', 'SystemSettingsController::delete_sla', ['filter' => 'perm:sla.delete']);
	});

	// --- User Role Management ---
	$routes->group('', static function ($routes) {
		$routes->get('get_user_role_list', 'SystemSettingsController::get_user_role_list', ['filter' => 'perm:role.read']);
		$routes->post('add_user_role', 'SystemSettingsController::add_user_role', ['filter' => 'perm:role.create']);
		$routes->post('edit_user_role', 'SystemSettingsController::edit_user_role', ['filter' => 'perm:role.update']);
		$routes->post('delete_user_role', 'SystemSettingsController::delete_user_role', ['filter' => 'perm:role.delete']);
	});

	// --- Request Type & Category Management ---
	$routes->group('', static function ($routes) {
		$routes->get('get_request_type_list', 'SystemSettingsController::get_request_type_list', ['filter' => 'perm:request_type.read']);
		$routes->post('add_request_type', 'SystemSettingsController::add_request_type', ['filter' => 'perm:request_type.create']);
		$routes->post('edit_request_type', 'SystemSettingsController::edit_request_type', ['filter' => 'perm:request_type.update']);
		$routes->post('delete_request_type', 'SystemSettingsController::delete_request_type', ['filter' => 'perm:request_type.delete']);

		// Main Category (Topik HC)
		$routes->get('get_category_list', 'SystemSettingsController::get_category_list', ['filter' => 'perm:request_type.read']);
		$routes->get('get_category_detail/(:segment)', 'SystemSettingsController::get_category_detail/$1', ['filter' => 'perm:request_type.read']);
		$routes->post('add_category', 'SystemSettingsController::add_category', ['filter' => 'perm:request_type.create']);
		$routes->post('edit_category', 'SystemSettingsController::edit_category', ['filter' => 'perm:request_type.update']);
		$routes->post('delete_category', 'SystemSettingsController::delete_category', ['filter' => 'perm:request_type.delete']);
	});

	// --- Permission Management ---
	$routes->group('', static function ($routes) {
		$routes->get('get_permission', 'SystemSettingsController::get_permission', ['filter' => 'perm:permission.read']);
		$routes->post('add_permission', 'SystemSettingsController::add_permission', ['filter' => 'perm:permission.create']);
		$routes->post('edit_permission', 'SystemSettingsController::edit_permission', ['filter' => 'perm:permission.update']);
		$routes->post('delete_permission', 'SystemSettingsController::delete_permission', ['filter' => 'perm:permission.delete']);
	});

	// --- User Management ---
	$routes->group('', static function ($routes) {
		$routes->get('system_settings/users', 'UserManagementController::user_mgt', ['filter' => 'perm:user.read']);
		$routes->get('user_mgt', static function() {
			return redirect()->to(base_url('admin/system_settings/users'));
		});
		$routes->get('get_user', 'UserManagementController::get_user', ['filter' => 'perm:user.read']);
		$routes->get('get_all_users_list', 'UserManagementController::get_all_users_list', ['filter' => 'perm:user.read']);
		$routes->get('search_candidate_users', 'UserManagementController::search_candidate_users', ['filter' => 'perm:user.read']);
		$routes->post('add_user', 'UserManagementController::add_user', ['filter' => 'perm:user.create']);
		$routes->post('edit_user', 'UserManagementController::edit_user', ['filter' => 'perm:user.update']);
		$routes->post('delete_user', 'UserManagementController::delete_user', ['filter' => 'perm:user.delete']);
	});

	// --- Reports & Export Jobs ---
	$routes->group('', static function ($routes) {
		// Analytics & Comparison Views
		$routes->get('report', '\App\Controllers\ReportController::index', ['filter' => 'perm:report.read']);
		$routes->get('report/ticket-detail', '\App\Controllers\ReportController::ticketDetail', ['filter' => 'perm:report.read']);
		$routes->get('report/sla-detail', '\App\Controllers\ReportController::slaDetail', ['filter' => 'perm:report.read']);
		$routes->get('report/sla-response', '\App\Controllers\ReportController::slaResponseComparison', ['filter' => 'perm:report.read']);
		$routes->get('report/sla-resolution', '\App\Controllers\ReportController::slaResolutionComparison', ['filter' => 'perm:report.read']);

		// User Report Jobs & Downloader
		$routes->get('report_user', 'ReportUserController::report_user', ['filter' => 'perm:report.read']);
		$routes->post('submit_report_job', 'ReportUserController::submit_report_job', ['filter' => 'perm:report.export']);
		$routes->get('download_report/(:num)', 'ReportUserController::download_report/$1', ['filter' => 'perm:report.export']);
		$routes->post('delete_report_job/(:num)', 'ReportUserController::delete_report_job/$1', ['filter' => 'perm:report.delete']);
	});

	// --- Developer Tools ---
	$routes->get('developer-options', 'DeveloperOptions::index', ['filter' => 'perm:dev.access']);
});