<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Ticket::create'); 
$routes->post('/ticket/store', 'Ticket::store'); 
$routes->get('faq', 'Ticket::faq');

$routes->get('/ticket', 'Ticket::index');
$routes->get('ticket/faq', 'Ticket::faq');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin'); 

$routes->get('admin/login', 'admin\Admin::login');
$routes->post('admin/authenticate', 'admin\Admin::authenticate');
$routes->get('admin/dashboard', 'admin\Admin::dashboard');
$routes->get('admin/logout', 'admin\Admin::logout');

// dboard
$routes->get('admin/dashboard', 'admin\Admin::dashboard');
$routes->get('admin/Ticket_dashboard', 'admin\Admin::Ticket_dashboard');

// tickets_db
$routes->get('admin/Ticket_detail/(:num)', 'admin\Admin::Ticket_detail/$1');
$routes->post('admin/send_reply/(:num)', 'admin\Admin::send_reply/$1');

//system settings
$routes->get('admin/system_settings', 'admin\Admin::system_settings');

//faq
$routes->post('admin/add_faq', 'admin\Admin::add_faq');
$routes->get('admin/get_faq_list', 'admin\Admin::get_faq_list');
$routes->post('admin/delete_faq', 'admin\Admin::delete_faq');
$routes->post('admin/edit_faq', 'admin\Admin::edit_faq');

// User Roles
$routes->get('admin/get_user_role_list', 'admin\Admin::get_user_role_list');
$routes->post('admin/add_user_role', 'admin\Admin::add_user_role');
$routes->post('admin/edit_user_role', 'admin\Admin::edit_user_role');
$routes->post('admin/delete_user_role', 'admin\Admin::delete_user_role');

// Request Types
$routes->get('admin/get_request_type_list', 'admin\Admin::get_request_type_list');
$routes->post('admin/add_request_type', 'admin\Admin::add_request_type');
$routes->post('admin/edit_request_type', 'admin\Admin::edit_request_type');
$routes->post('admin/delete_request_type', 'admin\Admin::delete_request_type');

// SLA Settings
$routes->post('admin/add_sla', 'admin\Admin::add_sla');
$routes->get('admin/get_sla_list', 'admin\Admin::get_sla_list');
$routes->post('admin/edit_sla', 'admin\Admin::edit_sla');
$routes->post('admin/delete_sla', 'admin\Admin::delete_sla');
$routes->get('admin/get_used_request_types', 'admin\Admin::get_used_request_types');

// User Management
$routes->get('admin/user_mgt', 'admin\Admin::user_mgt');
$routes->post('admin/add_user', 'admin\Admin::add_user');
$routes->post('admin/delete_user', 'admin\Admin::delete_user');
$routes->post('admin/edit_user', 'admin\Admin::edit_user');
$routes->get('admin/get_user', 'admin\Admin::get_user');

// user permission
$routes->post('admin/add_permission', 'admin\Admin::add_permission');
$routes->get('admin/get_permission', 'admin\Admin::get_permission');
$routes->post('admin/edit_permission', 'admin\Admin::edit_permission');
$routes->post('admin/delete_permission', 'admin\Admin::delete_permission');

// report user
$routes->get('admin/report_user', 'admin\Admin::report_user');

//assets js
$routes->get('assets/ticket_js', 'Assets::ticket_js');