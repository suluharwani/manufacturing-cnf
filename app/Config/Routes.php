<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Homepage::index');
$routes->get('/dashboard', 'Home::index');
$routes->get('/dashboard/fetchAndSaveRates', 'DashboardController::fetchAndSaveRates');
$routes->get('/dashboard/getCurrencyData', 'DashboardController::getCurrencyData');

$routes->get('/test', 'Home::test');
$routes->get('/material', 'Home::material');
$routes->get('/warehouse', 'Home::warehouse');
$routes->get('/employee', 'Home::employee');
$routes->get('/salary', 'Home::salarySetting');
$routes->post('/employeeData', 'User::employeeData');
$routes->get('/employeeData', 'User::employeeData');

$routes->get('/login', 'Login');
$routes->get('/signup', 'Login');
$routes->post('/login', 'Login');
$routes->get('/logout', 'Login::logout');
$routes->get('/material', 'Home::material');
$routes->get('/stock', 'Home::stock');
$routes->get('/production', 'Home::production');
$routes->get('/product', 'Home::product');
$routes->get('/design', 'Home::design');
$routes->get('/work_order', 'Home::work_order');
$routes->get('/track_work_order', 'Home::track_work_order');
$routes->get('/purchase_order', 'Home::purchase_order');
$routes->get('/track_purchase_delivery', 'Home::track_purchase_delivery');
$routes->get('/record_scrap', 'Home::scrap');
$routes->get('/review_scrap_report', 'Home::review_scrap_report');
$routes->get('/user', 'Home::user');
$routes->get('/role', 'Home::role');
$routes->get('/activitylog', 'Home::activitylog');
$routes->get('/scrap', 'Home::scrap');
$routes->get('/track_material', 'Home::track_material');
$routes->get('/scrap_management', 'Home::scrap_management');
$routes->get('/warehouse_report', 'Home::warehouse_report');
$routes->post('/material/tambah_tipe', 'MaterialController::tambah_tipe');
$routes->post('/material/tambah_satuan', 'MaterialController::tambah_satuan');
//product
$routes->post('/material/listdataProdukJoin', 'MaterialController::listdataProdukJoin');

// material
$routes->post('/material/type_list', 'MaterialController::type_list');
$routes->post('/material/satuan_list', 'MaterialController::satuan_list');
$routes->post('/material/listdataMaterial', 'MaterialController::listdataMaterial');
$routes->get('/material/listdataMaterial', 'MaterialController::listdataMaterial');
$routes->post('/material/listdataMaterialJoin', 'MaterialController::listdataMaterialJoin');
$routes->get('/material/listdataMaterialJoin', 'MaterialController::listdataMaterialJoin');
$routes->post('/material/tambah_material', 'MaterialController::tambah_material');
$routes->get('/material/get_material/(:any)', 'MaterialController::get_material/$1');
$routes->post('/material/update_material', 'MaterialController::update_material');
$routes->post('/material/satuanDelete', 'MaterialController::satuanDelete');
$routes->post('/material/typeDelete', 'MaterialController::typeDelete');
$routes->post('/material/delete', 'MaterialController::delete');
$routes->post('/material/materialUpdate', 'MaterialController::materialUpdate');
$routes->post('/material/satuanUpdate', 'MaterialController::satuanUpdate');
$routes->post('/material/typeUpdate', 'MaterialController::typeUpdate');
// $routes->post('/material/get_types', 'MaterialController::get_types');
// $routes->post('/material/get_satuan_ukuran', 'MaterialController::get_satuan_ukuran');







$routes->post('/warehouseController/gudang_list', 'WarehouseController::gudang_list');
$routes->post('/warehouseController/delete', 'WarehouseController::delete');
$routes->post('/warehouseController/deletedData', 'WarehouseController::deletedData');
$routes->post('/warehouseController/restoreData', 'WarehouseController::restoreData');
$routes->post('/warehouseController/purgeData', 'WarehouseController::purgeData');
$routes->post('/warehouseController/update', 'WarehouseController::update');
$routes->post('/warehouseController/create', 'WarehouseController::create');
//datatables post user admin
$routes->post('/user/listdata_user', 'User::listdata_user');
$routes->post('/user/tambah_admin', 'User::tambah_admin');
$routes->post('/user/hapus_user', 'User::hapus_user');
$routes->post('/user/reset_password', 'User::reset_password');
$routes->post('/user/ubah_status_user', 'User::ubah_status_user');
$routes->post('/user/ubah_level_user', 'User::ubah_level_user');
//datatables post client
$routes->post('/listdata_client', 'User::listdata_client');
$routes->post('/user/tambah_client', 'User::tambah_client');
$routes->post('/user/hapus_client', 'User::hapus_client');
$routes->post('/user/reset_password_client', 'User::reset_password_client');
$routes->post('/user/ubah_status_client', 'User::ubah_status_client');
$routes->post('/user/getPresensi', 'User::getPresensi');
$routes->post('/user/updateAttendance', 'User::updateAttendance');
$routes->post('/user/addEffectiveHours', 'User::addEffectiveHours');
$routes->post('/user/getDataWorkDay', 'User::getDataWorkDay');
$routes->post('/user/deleteWorkDay', 'User::deleteWorkDay');
$routes->post('/user/getSalaryCat', 'User::getSalaryCat');
$routes->post('/user/deleteSalaryCat', 'User::deleteSalaryCat');
$routes->post('/user/addSalaryCat', 'User::addSalaryCat');

$routes->get('/user/getAllowanceData', 'User::getAllowanceData'); 
$routes->post('/user/deleteAllowance', 'User::deleteAllowance');  
$routes->post('/user/addAllowance', 'User::addAllowance');  
$routes->get('/user/getDeductionData', 'User::getDeductionData'); 
$routes->post('/user/deleteDeduction', 'User::deleteDeduction');  
$routes->post('/user/addDeduction', 'User::addDeduction'); 
$routes->post('/user/getSalarySetting', 'User::getSalarySetting'); 
// $routes->post('/user/getSalarySetting', 'User::getSalarySetting');
$routes->post('/user/saveSalarySettings', 'User::saveSalarySettings');
$routes->post('/user/saveSalaryCategory', 'User::saveSalaryCategory');
$routes->post('/user/getEmployeeNameByPin', 'User::getEmployeeNameByPin');
$routes->post('/user/getAvailableItems', 'User::getAvailableItems');

//datatables post orders
$routes->post('/order/listdataOrder', 'OrderController::listdataOrder');

$routes->get('user/fetchAllowances', 'User::fetchAllowances');
$routes->post('user/saveAllowance', 'User::saveAllowance');
$routes->post('user/deleteAllowance/(:num)', 'User::deleteAllowance/$1');
$routes->get('user/getAllowanceOptions', 'User::getAllowanceOptions');
$routes->get('user/getEmployeeAllowances/(:any)', 'User::getEmployeeAllowances/$1');
$routes->post('user/deleteAllowanceList/(:any)', 'User::deleteAllowanceList/$1');

 
$routes->get('user/fetchDeductions', 'User::fetchDeductions');
$routes->post('user/saveDeduction', 'User::saveDeduction');
$routes->post('user/deleteDeduction/(:num)', 'User::deleteDeduction/$1');
$routes->get('user/getDeductionOptions', 'User::getDeductionOptions');
$routes->get('user/getEmployeeDeductions/(:any)', 'User::getEmployeeDeductions/$1');
$routes->post('user/deleteDeductionList/(:any)', 'User::deleteDeductionList/$1');
$routes->post('user/addDeductionList', 'User::addDeductionList');
