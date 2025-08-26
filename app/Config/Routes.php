<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Ticket::create');   // langsung ke form submit tiket
$routes->post('/ticket/store', 'Ticket::store'); // untuk proses simpan ke DB

// kalau mau lihat list tiket (opsional)
$routes->get('/ticket', 'Ticket::index');

$routes->get('/login', 'Auth::login'); // tampilkan halaman login
$routes->post('/login', 'Auth::attemptLogin'); // proses submit form login

$routes->get('admin/login', 'admin\Admin::login');
$routes->post('admin/authenticate', 'admin\Admin::authenticate');
$routes->get('admin/dashboard', 'admin\Admin::dashboard');
$routes->get('admin/logout', 'admin\Admin::logout');

$routes->get('admin/dashboard', 'admin\Admin::dashboard');

// contoh untuk users kalau masih dipakai
$routes->get('/users', 'User::index');

