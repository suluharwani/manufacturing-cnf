<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/material', 'Home::material');
$routes->get('/login', 'Login');
$routes->get('/signup', 'Login');
$routes->post('/login', 'Login');
$routes->get('/logout', 'Login::logout');
$routes->get('/material', 'Home::material');
$routes->post('/material/tambah_tipe', 'MaterialController::tambah_tipe');
$routes->post('/material/hapustype', 'MaterialController::material');

