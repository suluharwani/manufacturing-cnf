<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/test', 'Home::test');
$routes->get('/material', 'Home::material');
$routes->get('/warehouse', 'Home::warehouse');
$routes->post('/WarehouseController/buat_gudang_baru', 'WarehouseController::buat_gudang_baru');
$routes->get('/login', 'Login');
$routes->get('/signup', 'Login');
$routes->post('/login', 'Login');
$routes->get('/logout', 'Login::logout');
$routes->get('/material', 'Home::material');
$routes->post('/material/tambah_tipe', 'MaterialController::tambah_tipe');
$routes->post('/material/tambah_satuan', 'MaterialController::tambah_satuan');
// $routes->post('/material/hapustype', 'MaterialController::material');
$routes->post('/material/type_list', 'MaterialController::type_list');
$routes->post('/material/satuan_list', 'MaterialController::satuan_list');
$routes->post('/material/listdataMaterial', 'MaterialController::listdataMaterial');
$routes->get('/material/listdataMaterial', 'MaterialController::listdataMaterial');
$routes->post('/material/listdataMaterialJoin', 'MaterialController::listdataMaterialJoin');
$routes->post('/warehousecontroller/gudang_list', 'warehousecontroller::gudang_list');

