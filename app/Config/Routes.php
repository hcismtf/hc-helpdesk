<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Ticket::create');   // langsung ke form submit tiket
$routes->post('/ticket/store', 'Ticket::store'); // untuk proses simpan ke DB

// kalau mau lihat list tiket (opsional)
$routes->get('/ticket', 'Ticket::index');

// contoh untuk users kalau masih dipakai
$routes->get('/users', 'User::index');


