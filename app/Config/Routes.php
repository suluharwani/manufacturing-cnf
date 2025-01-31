<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/forbidden', 'Home::forbidden');
$routes->get('/col/(:any)', 'Home::col/$1');
$routes->get('/', 'Homepage::index');

$routes->get('/dashboard', 'Home::index',['filter' => 'accessControl:2']);
$routes->get('/dashboard/fetchAndSaveRates', 'DashboardController::fetchAndSaveRates',['filter' => 'accessControl:2']);
$routes->get('/dashboard/getCurrencyData', 'DashboardController::getCurrencyData',['filter' => 'accessControl:2']);
$routes->get('/dashboard/getCountryData', 'DashboardController::getCountryData',['filter' => 'accessControl:2']);
$routes->get('productionArea', 'Home::productionArea',['filter' => 'accessControl:1']);
$routes->get('department', 'Home::department',['filter' => 'accessControl:1']);
$routes->get('material_request', 'Home::materialRequest',['filter' => 'accessControl:2']);
$routes->get('hscode/check_hs_code', 'Home::check_hs_code',['filter' => 'accessControl:2']);


$routes->get('/supplier', 'Home::supplier',['filter' => 'accessControl:1']);  
$routes->get('/customer', 'Home::customer',['filter' => 'accessControl:1']);  
$routes->get('/material', 'Home::material',['filter' => 'accessControl:1']);
$routes->get('/warehouse', 'Home::warehouse',['filter' => 'accessControl:1']);
$routes->get('/employee', 'Home::employee',['filter' => 'accessControl:2']);
$routes->get('/salary', 'Home::salarySetting',['filter' => 'accessControl:2']);
$routes->get('/proformainvoice', 'Home::proformainvoice',['filter' => 'accessControl:2']);
$routes->get('/finishing', 'Home::finishing',['filter' => 'accessControl:1']);
$routes->post('/employeeData', 'User::employeeData',['filter' => 'accessControl:2']);
$routes->get('/employeeData', 'User::employeeData',['filter' => 'accessControl:2']);


$routes->get('/login', 'Login');
$routes->get('/signup', 'Login');
$routes->post('/login', 'Login');
$routes->get('/logout', 'Login::logout');
$routes->get('/material', 'Home::material',['filter' => 'accessControl:2']);
$routes->get('/stock', 'Home::stock',['filter' => 'accessControl:2']);
$routes->get('/production', 'Home::production',['filter' => 'accessControl:2']);
$routes->get('/product', 'Home::product',['filter' => 'accessControl:1']);
$routes->get('/design', 'Home::design',['filter' => 'accessControl:2']);
$routes->get('/work_order', 'Home::work_order',['filter' => 'accessControl:2']);
$routes->get('/track_work_order', 'Home::track_work_order',['filter' => 'accessControl:2']);
$routes->get('/purchase_order', 'Home::purchase_order',['filter' => 'accessControl:2']);
$routes->get('/track_purchase_delivery', 'Home::track_purchase_delivery',['filter' => 'accessControl:2']);
$routes->get('/record_scrap', 'Home::scrap',['filter' => 'accessControl:2']);
$routes->get('/review_scrap_report', 'Home::review_scrap_report',['filter' => 'accessControl:2']);
$routes->get('/user', 'Home::user',['filter' => 'accessControl:2']);
$routes->get('/role', 'Home::role',['filter' => 'accessControl:2']);
$routes->get('/activitylog', 'Home::activitylog',['filter' => 'accessControl:2']);
$routes->get('/scrap', 'Home::scrap',['filter' => 'accessControl:2']);
$routes->get('/track_material', 'Home::track_material',['filter' => 'accessControl:2']);
$routes->get('/scrap_management', 'Home::scrap_management',['filter' => 'accessControl:2']);
$routes->get('/warehouse_report', 'Home::warehouse_report',['filter' => 'accessControl:2']);
$routes->post('/material/tambah_tipe', 'MaterialController::tambah_tipe',['filter' => 'accessControl:2']);
$routes->post('/material/tambah_satuan', 'MaterialController::tambah_satuan',['filter' => 'accessControl:2']);
$routes->get('/material_requisition_progress',  'Home::material_requisition_progress',['filter' => 'accessControl:2']);

//product


// material
$routes->post('/material/type_list', 'MaterialController::type_list',['filter' => 'accessControl:2']);
$routes->post('/material/satuan_list', 'MaterialController::satuan_list',['filter' => 'accessControl:2']);
$routes->post('/material/listdataMaterial', 'MaterialController::listdataMaterial',['filter' => 'accessControl:2']);
$routes->get('/material/listdataMaterial', 'MaterialController::listdataMaterial',['filter' => 'accessControl:2']);
$routes->post('/material/listdataMaterialJoin', 'MaterialController::listdataMaterialJoin',['filter' => 'accessControl:2']);
$routes->get('/material/listdataMaterialJoin', 'MaterialController::listdataMaterialJoin',['filter' => 'accessControl:2']);
$routes->post('/material/tambah_material', 'MaterialController::tambah_material',['filter' => 'accessControl:2']);
$routes->get('/material/get_material/(:any)', 'MaterialController::get_material/$1',['filter' => 'accessControl:2']);
$routes->post('/material/update_material', 'MaterialController::update_material',['filter' => 'accessControl:2']);
$routes->post('/material/satuanDelete', 'MaterialController::satuanDelete',['filter' => 'accessControl:2']);
$routes->post('/material/typeDelete', 'MaterialController::typeDelete',['filter' => 'accessControl:2']);
$routes->post('/material/delete', 'MaterialController::delete',['filter' => 'accessControl:2']);
$routes->post('/material/materialUpdate', 'MaterialController::materialUpdate',['filter' => 'accessControl:2']);
$routes->post('/material/satuanUpdate', 'MaterialController::satuanUpdate',['filter' => 'accessControl:2']);
$routes->post('/material/typeUpdate', 'MaterialController::typeUpdate',['filter' => 'accessControl:2']);
// $routes->post('/material/get_types', 'MaterialController::get_types',['filter' => 'accessControl:2']);
// $routes->post('/material/get_satuan_ukuran', 'MaterialController::get_satuan_ukuran',['filter' => 'accessControl:2']);




$routes->post('ProductionController/gudang_list', 'ProductionController::gudang_list',['filter' => 'accessControl:2']);
$routes->post('ProductionController/delete', 'ProductionController::delete',['filter' => 'accessControl:2']);
$routes->post('ProductionController/deletedData', 'ProductionController::deletedData',['filter' => 'accessControl:2']);
$routes->post('ProductionController/restoreData', 'ProductionController::restoreData',['filter' => 'accessControl:2']);
$routes->post('ProductionController/purgeData', 'ProductionController::purgeData',['filter' => 'accessControl:2']);
$routes->post('ProductionController/update', 'ProductionController::update',['filter' => 'accessControl:2']);
$routes->post('ProductionController/create', 'ProductionController::create',['filter' => 'accessControl:2']);

$routes->post('production/warehouseList', 'ProductionController::warehouseList',['filter' => 'accessControl:2']);
$routes->post('production/productionList', 'ProductionController::productionList',['filter' => 'accessControl:2']);
$routes->post('production/getWOList', 'ProductionController::getWOList',['filter' => 'accessControl:2']);
$routes->post('production/addWo', 'ProductionController::addWo',['filter' => 'accessControl:2']);
$routes->post('production/addWo', 'ProductionController::addWo',['filter' => 'accessControl:2']);
$routes->get('production/getWOProduction/(:any)', 'ProductionController::getWOProduction/$1',['filter' => 'accessControl:2']);
$routes->get('production/getProductByWO/(:any)', 'ProductionController::getProductByWO/$1',['filter' => 'accessControl:2']);
$routes->post('production/addProgress', 'ProductionController::addProgress',['filter' => 'accessControl:2']);
$routes->get('production/getProductionProduct/(:any)', 'ProductionController::getProductionProduct/$1',['filter' => 'accessControl:2']);
$routes->get('production/getWarehouseProduct/(:any)', 'ProductionController::getWarehouseProduct/$1',['filter' => 'accessControl:2']);
$routes->post('production/moveProduction', 'ProductionController::moveProduction',['filter' => 'accessControl:2']);
$routes->post('production/moveWarehouse', 'ProductionController::moveWarehouse',['filter' => 'accessControl:2']);
$routes->post('production/moveWarehouseFromProd', 'ProductionController::moveWarehouseFromProd',['filter' => 'accessControl:2']);

 

$routes->post('/warehouseController/gudang_list', 'WarehouseController::gudang_list',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/delete', 'WarehouseController::delete',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/deletedData', 'WarehouseController::deletedData',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/restoreData', 'WarehouseController::restoreData',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/purgeData', 'WarehouseController::purgeData',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/update', 'WarehouseController::update',['filter' => 'accessControl:2']);
$routes->post('/warehouseController/create', 'WarehouseController::create',['filter' => 'accessControl:2']);
//datatables post user admin
$routes->post('/user/listdata_user', 'User::listdata_user',['filter' => 'accessControl:2']);
$routes->post('/user/tambah_admin', 'User::tambah_admin',['filter' => 'accessControl:2']);
$routes->post('/user/hapus_user', 'User::hapus_user',['filter' => 'accessControl:2']);
$routes->post('/user/reset_password', 'User::reset_password',['filter' => 'accessControl:2']);
$routes->post('/user/ubah_status_user', 'User::ubah_status_user',['filter' => 'accessControl:2']);
$routes->post('/user/ubah_level_user', 'User::ubah_level_user',['filter' => 'accessControl:2']);
//datatables post client
$routes->post('/listdata_client', 'User::listdata_client',['filter' => 'accessControl:2']);
$routes->post('/user/tambah_client', 'User::tambah_client',['filter' => 'accessControl:2']);
$routes->post('/user/hapus_client', 'User::hapus_client',['filter' => 'accessControl:2']);
$routes->post('/user/reset_password_client', 'User::reset_password_client',['filter' => 'accessControl:2']);
$routes->post('/user/ubah_status_client', 'User::ubah_status_client',['filter' => 'accessControl:2']);
$routes->post('/user/getPresensi', 'User::getPresensi',['filter' => 'accessControl:2']);
$routes->post('/user/updateAttendance', 'User::updateAttendance',['filter' => 'accessControl:2']);
$routes->post('/user/addEffectiveHours', 'User::addEffectiveHours',['filter' => 'accessControl:2']);
$routes->post('/user/getDataWorkDay', 'User::getDataWorkDay',['filter' => 'accessControl:2']);
$routes->post('/user/deleteWorkDay', 'User::deleteWorkDay',['filter' => 'accessControl:2']);
$routes->post('/user/getSalaryCat', 'User::getSalaryCat',['filter' => 'accessControl:2']);
$routes->post('/user/deleteSalaryCat', 'User::deleteSalaryCat',['filter' => 'accessControl:2']);
$routes->post('/user/addSalaryCat', 'User::addSalaryCat',['filter' => 'accessControl:2']);

$routes->get('/user/getAllowanceData', 'User::getAllowanceData',['filter' => 'accessControl:2']); 
$routes->post('/user/deleteAllowance', 'User::deleteAllowance',['filter' => 'accessControl:2']);  
$routes->post('/user/addAllowance', 'User::addAllowance',['filter' => 'accessControl:2']);  
$routes->get('/user/getDeductionData', 'User::getDeductionData',['filter' => 'accessControl:2']); 
$routes->post('/user/deleteDeduction', 'User::deleteDeduction',['filter' => 'accessControl:2']);  
$routes->post('/user/addDeduction', 'User::addDeduction',['filter' => 'accessControl:2']); 
$routes->post('/user/getSalarySetting', 'User::getSalarySetting',['filter' => 'accessControl:2']); 
// $routes->post('/user/getSalarySetting', 'User::getSalarySetting',['filter' => 'accessControl:2']);
$routes->post('/user/saveSalarySettings', 'User::saveSalarySettings',['filter' => 'accessControl:2']);
$routes->post('/user/saveSalaryCategory', 'User::saveSalaryCategory',['filter' => 'accessControl:2']);
$routes->post('/user/getEmployeeNameByPin', 'User::getEmployeeNameByPin',['filter' => 'accessControl:2']);
$routes->post('/user/getAvailableItems', 'User::getAvailableItems',['filter' => 'accessControl:2']);

//datatables post orders
$routes->post('/order/listdataOrder', 'OrderController::listdataOrder',['filter' => 'accessControl:2']);

$routes->get('user/fetchAllowances', 'User::fetchAllowances',['filter' => 'accessControl:2']);
$routes->post('user/saveAllowance', 'User::saveAllowance',['filter' => 'accessControl:2']);
$routes->post('user/deleteAllowance/(:num)', 'User::deleteAllowance/$1',['filter' => 'accessControl:2']);
$routes->get('user/getAllowanceOptions', 'User::getAllowanceOptions',['filter' => 'accessControl:2']);
$routes->get('user/getEmployeeAllowances/(:any)', 'User::getEmployeeAllowances/$1',['filter' => 'accessControl:2']);
$routes->post('user/deleteAllowanceList/(:any)', 'User::deleteAllowanceList/$1',['filter' => 'accessControl:2']);

 
$routes->get('user/fetchDeductions', 'User::fetchDeductions',['filter' => 'accessControl:2']);
$routes->post('user/saveDeduction', 'User::saveDeduction',['filter' => 'accessControl:2']);
$routes->post('user/deleteDeduction/(:num)', 'User::deleteDeduction/$1',['filter' => 'accessControl:2']);
$routes->get('user/getDeductionOptions', 'User::getDeductionOptions',['filter' => 'accessControl:2']);
$routes->get('user/getEmployeeDeductions/(:any)', 'User::getEmployeeDeductions/$1',['filter' => 'accessControl:2']);
$routes->post('user/deleteDeductionList/(:any)', 'User::deleteDeductionList/$1',['filter' => 'accessControl:2']);
$routes->post('user/addDeductionList', 'User::addDeductionList',['filter' => 'accessControl:2']);

//master salary
$routes->get('/master_salary', 'Home::masterSalary',['filter' => 'accessControl:2']);
$routes->get('/detail_salary/(:any)', 'Home::detailSalary/$1',['filter' => 'accessControl:2']);
$routes->group('master_penggajian', function ($routes) {
    $routes->get('/', 'MasterPenggajianController::index',['filter' => 'accessControl:2']); 
    $routes->post('get_list', 'MasterPenggajianController::get_list',['filter' => 'accessControl:2']); 
    $routes->post('add', 'MasterPenggajianController::add',['filter' => 'accessControl:2']); 
    $routes->get('get/(:num)', 'MasterPenggajianController::get/$1',['filter' => 'accessControl:2']);
    $routes->post('update', 'MasterPenggajianController::update',['filter' => 'accessControl:2']);
    $routes->post('delete/(:num)', 'MasterPenggajianController::delete/$1',['filter' => 'accessControl:2']); 

});


$routes->group('MasterPenggajianDetailController', function ($routes) {
    $routes->post('addEmployeeToPayroll', 'MasterPenggajianDetailController::addEmployeeToPayroll',['filter' => 'accessControl:2']); 
    $routes->get('dataEmployeeMaster/(:any)', 'MasterPenggajianDetailController::dataEmployeeMaster/$1',['filter' => 'accessControl:2']); 
    $routes->post('deleteEmployeeFromPayroll','MasterPenggajianDetailController::deleteEmployeeFromPayroll',['filter' => 'accessControl:2']);
    $routes->get('getEmployeeSalarySlip/(:num)/(:num)', 'MasterPenggajianDetailController::getEmployeeSalarySlip/$1/$2',['filter' => 'accessControl:2']);
    $routes->get('getSalaryRate/(:num)', 'MasterPenggajianDetailController::getSalaryRate/$1',['filter' => 'accessControl:2']);
    $routes->get('exportToExcel/(:num)', 'MasterPenggajianDetailController::exportToExcel/$1',['filter' => 'accessControl:2']);
    $routes->get('exportAllToExcel', 'MasterPenggajianDetailController::exportAllToExcel',['filter' => 'accessControl:2']);
    $routes->get('getTunjanganPrint', 'MasterPenggajianDetailController::getTunjanganPrint',['filter' => 'accessControl:2']);
    $routes->get('getPotonganPrint', 'MasterPenggajianDetailController::getPotonganPrint',['filter' => 'accessControl:2']);
});

$routes->group('product', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 
    $routes->post('addcat', 'ProductController::addcat',['filter' => 'accessControl:2']); 
    // $routes->post('add', 'ProductController::add',['filter' => 'accessControl:2']); 
    $routes->get('getcat/(:num)', 'ProductController::getcat/$1',['filter' => 'accessControl:2']);
    $routes->post('cat_list', 'ProductController::cat_list',['filter' => 'accessControl:2']);
    $routes->post('upload', 'ProductController::upload',['filter' => 'accessControl:2']);
    $routes->post('create', 'ProductController::create',['filter' => 'accessControl:2']);
    // $routes->post('delete/(:num)', 'ProductController::delete/$1',['filter' => 'accessControl:2']); 
    $routes->post('listdataProdukJoin', 'ProductController::listdataProdukJoin',['filter' => 'accessControl:2']);
    $routes->get('getMaterial', 'ProductController::getMaterial',['filter' => 'accessControl:2']);
    $routes->get('searchMaterial', 'ProductController::searchMaterial',['filter' => 'accessControl:2']);
    $routes->post('saveBom', 'ProductController::saveBom',['filter' => 'accessControl:2']);
    $routes->post('getBom', 'ProductController::getBom',['filter' => 'accessControl:2']);
    $routes->get('getProduct', 'ProductController::getProduct',['filter' => 'accessControl:2']);
});
$routes->group('supplier', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('list', 'SupplierController::listdataSupplierJoin',['filter' => 'accessControl:2']); // Route untuk menampilkan data supplier
    $routes->post('listdataSupplierJoin', 'SupplierController::listdataSupplierJoin',['filter' => 'accessControl:2']); // Route untuk data server-side processing
    $routes->post('create', 'SupplierController::create',['filter' => 'accessControl:2']); // Route untuk menambahkan supplier baru
    $routes->post('update/(:num)', 'SupplierController::update/$1',['filter' => 'accessControl:2']); // Route untuk mengupdate data supplier
    $routes->post('delete/(:num)', 'SupplierController::delete/$1',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
    $routes->get('get/(:num)', 'SupplierController::get/$1',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
    $routes->post('upload', 'SupplierController::upload',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
});
$routes->group('customer', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('list', 'CustomerController::listdataCustomerJoin',['filter' => 'accessControl:2']); // Route untuk menampilkan data supplier
    $routes->post('listdataCustomerJoin', 'CustomerController::listdataCustomerJoin',['filter' => 'accessControl:2']); // Route untuk data server-side processing
    $routes->post('create', 'CustomerController::create',['filter' => 'accessControl:2']); // Route untuk menambahkan supplier baru
    $routes->post('update/(:num)', 'CustomerController::update/$1',['filter' => 'accessControl:2']); // Route untuk mengupdate data supplier
    $routes->post('delete/(:num)', 'CustomerController::delete/$1',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
    $routes->get('get/(:num)', 'CustomerController::get/$1',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
    $routes->post('upload', 'CustomerController::upload',['filter' => 'accessControl:2']); // Route untuk menghapus supplier
});

$routes->group('stock', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 
    $routes->get('addStock/(:any)', 'Stock::addStock/(:any)',['filter' => 'accessControl:2']); 
    $routes->post('stockdata', 'Stock::stockdata',['filter' => 'accessControl:2']); 
    $routes->get('get_stock_in_out/(:any)', 'Stock::get_stock/$1',['filter' => 'accessControl:2']); 
    
});
 
    $routes->get('breakdownBoM/(:any)', 'ProductController::breakdownBom/$1',['filter' => 'accessControl:2']);
    $routes->get('product/getProductData/(:any)', 'ProductController::getProductData/$1',['filter' => 'accessControl:2']);
$routes->group('finishing', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'FinishingController::index',['filter' => 'accessControl:2']); // Halaman utama finishing
    $routes->post('getAll', 'FinishingController::getAll',['filter' => 'accessControl:2']); // Mendapatkan semua data finishing
    $routes->post('create', 'FinishingController::create',['filter' => 'accessControl:2']); // Menambahkan data finishing baru
    $routes->post('update/(:num)', 'FinishingController::update/$1',['filter' => 'accessControl:2']); // Memperbarui data finishing berdasarkan ID
    $routes->delete('delete/(:num)', 'FinishingController::delete/$1',['filter' => 'accessControl:2']); // Menghapus data finishing berdasarkan ID
    $routes->post('get', 'FinishingController::get',['filter' => 'accessControl:2']); // Menghapus data finishing berdasarkan ID
    $routes->post('updatePicture', 'FinishingController::updatePicture',['filter' => 'accessControl:2']); // Menghapus data finishing berdasarkan ID
    $routes->post('updateData', 'FinishingController::updateData',['filter' => 'accessControl:2']); // Menghapus data finishing berdasarkan ID
    
    
});
$routes->group('pembelian', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('', 'Home::pembelian',['filter' => 'accessControl:2']);
    $routes->post('listdataPembelian', 'ControllerPembelian::listdataPembelian',['filter' => 'accessControl:2']); 
    $routes->get('form/(:any)', 'ControllerPembelian::pembelianForm/$1',['filter' => 'accessControl:2']);
    $routes->post('listdataPembelianDetail/(:any)', 'ControllerPembelian::listdataPembelianDetail/$1',['filter' => 'accessControl:2']);
    $routes->get('listdataPembelianDetail/(:any)', 'ControllerPembelian::listdataPembelianDetail/$1',['filter' => 'accessControl:2']);
    $routes->get('getSupplierData/(:num)', 'ControllerPembelian::getSupplierData/$1',['filter' => 'accessControl:2']); // Route untuk mengambil data supplier berdasarkan ID
    $routes->get('getSupplierList', 'ControllerPembelian::getSupplierList',['filter' => 'accessControl:2']); 
    $routes->post('updateSupplier/(:any)', 'ControllerPembelian::updateSupplier/$1',['filter' => 'accessControl:2']); 

    $routes->get('getCountryData', 'ControllerPembelian::getCountryData',['filter' => 'accessControl:2']); // Route untuk mengambil data negara
    $routes->get('getCurrencyData', 'ControllerPembelian::getCurrencyData',['filter' => 'accessControl:2']); // Route untuk mengambil data mata uang  
    $routes->get('getSupplierDataByPurchase/(:num)', 'ControllerPembelian::getSupplierDataByPurchase/$1',['filter' => 'accessControl:2']); // Route untuk mengambil data mata uang  
    $routes->post('addInvoice', 'ControllerPembelian::addInvoice',['filter' => 'accessControl:2']); // Route untuk mengambil data mata uang  
    $routes->post('deleteinvoice', 'ControllerPembelian::deleteinvoice',['filter' => 'accessControl:2']); // Route untuk mengambil data mata uang  
    $routes->post('addMaterial', 'ControllerPembelian::addMaterial',['filter' => 'accessControl:2']); // Route untuk mengambil data mata uang  
    $routes->post('update/(:segment)','ControllerPembelian::update/$1',['filter' => 'accessControl:2']);  // Mengupdate data material
    $routes->post('delete/(:segment)', 'ControllerPembelian::delete/$1',['filter' => 'accessControl:2']);  // Menghapus material
    $routes->get('get/(:segment)', 'ControllerPembelian::get/$1',['filter' => 'accessControl:2']);  // Menghapus material
    $routes->post('posting', 'ControllerPembelian::posting',['filter' => 'accessControl:2']);  
    $routes->post('unposting', 'ControllerPembelian::unposting',['filter' => 'accessControl:2']); 


    
});

$routes->group('proformainvoice', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 
    $routes->post('getCustomerList', 'ProformaInvoiceController::getCustomerList',['filter' => 'accessControl:2']); 
    $routes->post('upload', 'ProformaInvoiceController::upload',['filter' => 'accessControl:2']); 
    $routes->post('add', 'ProformaInvoiceController::add',['filter' => 'accessControl:2']); 
    $routes->post('listdata', 'ProformaInvoiceController::listdata',['filter' => 'accessControl:2']); 
    $routes->post('get_list', 'ProformaInvoiceController::get_list',['filter' => 'accessControl:2']); 
    $routes->post('get_list_json', 'ProformaInvoiceController::get_list_json',['filter' => 'accessControl:2']); 
    $routes->get('pi/(:any)', 'ProformaInvoiceController::pi/$1',['filter' => 'accessControl:2']); 
    $routes->get('piDoc/(:any)', 'ProformaInvoiceController::piDoc/$1',['filter' => 'accessControl:2']); 
    $routes->post('listdataPi/(:any)', 'ProformaInvoiceController::listdataPi/$1',['filter' => 'accessControl:2']); 
    $routes->post('addProduct', 'ProformaInvoiceController::addProduct',['filter' => 'accessControl:2']); 
    $routes->get('getProduct/(:num)', 'ProformaInvoiceController::getProduct/$1',['filter' => 'accessControl:2']);
    $routes->post('updateProduct/(:num)', 'ProformaInvoiceController::updateProduct/$1',['filter' => 'accessControl:2']);
    $routes->post('deleteProduct/(:num)', 'ProformaInvoiceController::deleteProduct/$1',['filter' => 'accessControl:2']);
    $routes->post('update/(:num)', 'ProformaInvoiceController::update/$1',['filter' => 'accessControl:2']);
    $routes->post('file/(:num)', 'ProformaInvoiceController::file/$1',['filter' => 'accessControl:2']);
    $routes->get('getDocumentDetails', 'ProformaInvoiceController::getDocumentDetails',['filter' => 'accessControl:2']);
    $routes->post('updateDocument', 'ProformaInvoiceController::updateDocument',['filter' => 'accessControl:2']);
    $routes->delete('delete/(:num)', 'ProformaInvoiceController::delete/$1',['filter' => 'accessControl:2']);
    $routes->post('deleteinvoice/(:num)', 'ProformaInvoiceController::deleteinvoice/$1',['filter' => 'accessControl:2']);
    $routes->post('finish/(:num)', 'ProformaInvoiceController::finish/$1',['filter' => 'accessControl:2']);
    $routes->post('batalFinish/(:num)', 'ProformaInvoiceController::batalFinish/$1',['filter' => 'accessControl:2']);
    $routes->get('print/(:num)', 'ProformaInvoiceController::print/$1',['filter' => 'accessControl:2']);
    $routes->get('delivery_note/(:num)', 'ProformaInvoiceController::printDeliveryNote/$1',['filter' => 'accessControl:2']);


});
$routes->group('wo', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 

    $routes->post('woList', 'WorkOrderController::woList',['filter' => 'accessControl:2']); 
    $routes->post('listdata', 'WorkOrderController::listdataWorkOrder',['filter' => 'accessControl:2']); 
    $routes->post('add', 'WorkOrderController::add',['filter' => 'accessControl:2']); 
    $routes->get('(:any)', 'WorkOrderController::wo/$1',['filter' => 'accessControl:2']); 

});
    $routes->get('WogetPi/(:any)', 'WorkOrderController::getPi/$1',['filter' => 'accessControl:2']); 
    $routes->post('workOrder/addDetail', 'WorkOrderController::addDetail',['filter' => 'accessControl:2']); 
    $routes->get('getWo/(:any)', 'WorkOrderController::getWo/$1',['filter' => 'accessControl:2']); 
    $routes->post('workOrder/delete/(:any)', 'WorkOrderController::delete/$1',['filter' => 'accessControl:2']); 


$routes->group('purchase', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 

    $routes->post('listdataPurchaseOrder', 'PurchaseController::listdataPurchaseOrder',['filter' => 'accessControl:2']); 
    $routes->post('listdataPo/(:any)', 'PurchaseController::listdataPo/$1',['filter' => 'accessControl:2']); 
    $routes->post('add_po', 'PurchaseController::add_po',['filter' => 'accessControl:2']); 
    $routes->get('po/(:any)', 'PurchaseController::po/$1',['filter' => 'accessControl:2']); 
    $routes->post('update/(:any)', 'PurchaseController::update/$1',['filter' => 'accessControl:2']); 
    $routes->post('addPOList', 'PurchaseController::addPOList',['filter' => 'accessControl:2']); 

});

$routes->group('report', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 

    $routes->get('activity', 'ReportController::activity',['filter' => 'accessControl:2']); 
    $routes->get('customer_order', 'ReportController::customer_order',['filter' => 'accessControl:2']); 
    $routes->get('finished_good', 'ReportController::finished_good',['filter' => 'accessControl:2']); 
    $routes->get('material', 'ReportController::material',['filter' => 'accessControl:2']); 
    $routes->get('purchase', 'ReportController::purchase',['filter' => 'accessControl:2']); 
    $routes->post('materialStockCard', 'ReportController::materialStockCard',['filter' => 'accessControl:2']); 
    $routes->post('productionReport', 'ReportController::productionReport',['filter' => 'accessControl:2']); 
    $routes->post('productionReportPIByProduct', 'ReportController::productionReportPIByProduct',['filter' => 'accessControl:2']); 
    $routes->get('getHeader', 'ReportController::getHeader',['filter' => 'accessControl:2']); 
    $routes->get('getHeaderScrap', 'ReportController::getHeaderScrap',['filter' => 'accessControl:2']); 
    $routes->post('materialScrap', 'ReportController::materialScrap',['filter' => 'accessControl:2']); 
    $routes->post('stockMovementReport', 'ReportController::stockMovementReport',['filter' => 'accessControl:2']); 
    

});
$routes->get('mr/(:any)', 'MaterialRequestController::mr/$1',['filter' => 'accessControl:2']); 
$routes->group('materialrequest', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 

    $routes->post('materialRequest', 'MaterialRequestController::materialRequest',['filter' => 'accessControl:2']); 
    $routes->post('materialRequestList', 'MaterialRequestController::materialRequestList',['filter' => 'accessControl:2']); 
    $routes->get('material_request', 'MaterialRequestController::index',['filter' => 'accessControl:2']); 
    $routes->post('add', 'MaterialRequestController::add',['filter' => 'accessControl:2']); 
    $routes->post('addMR', 'MaterialRequestController::addMR',['filter' => 'accessControl:2']); 
    $routes->post('datamr/(:any)', 'MaterialRequestController::datamr/$1',['filter' => 'accessControl:2']); 
    $routes->post('deleteList/(:any)', 'MaterialRequestController::deleteList/$1',['filter' => 'accessControl:2']);
    $routes->get('getMR/(:any)', 'MaterialRequestController::getMR/$1',['filter' => 'accessControl:2']);

});

$routes->post('department/department_list', 'DepartmentController::department_list',['filter' => 'accessControl:1']); 

$routes->group('materialrequisition', function ($routes) {
    // $routes->post('get_list', 'ProductController::get_list',['filter' => 'accessControl:2']); 

    $routes->get('', 'Home::material_requisition',['filter' => 'accessControl:2']); 
    $routes->get('list', 'Home::material_requisition_list',['filter' => 'accessControl:2']); 
    $routes->get('process', 'Home::material_requisition_process',['filter' => 'accessControl:2']); 
    $routes->post('listData', `MaterialRequisitionController::material_requisition`,['filter' => 'accessControl:2']); 
    

});
$routes->group('materialreturn', function ($routes) {
    $routes->get('', 'Home::material_return',['filter' => 'accessControl:2']); 
    $routes->get('form/(:any)', 'MaterialReturnController::material_return_form/$1',['filter' => 'accessControl:2']); 
    $routes->post('listdataMaterialReturn', 'MaterialReturnController::listdataMaterialReturn',['filter' => 'accessControl:2']); 
    $routes->post('listdataMaterialReturnList/(:any)', 'MaterialReturnController::listdataMaterialReturnList/$1',['filter' => 'accessControl:2']); 
    
});
$routes->group('pemusnahan', function ($routes) {
    $routes->get('', 'Home::inventory_destruction',['filter' => 'accessControl:2']); 
    $routes->get('form/(:any)', 'MaterialDestructionController::destruction_form/$1',['filter' => 'accessControl:2']); 
    $routes->post('addDocument', 'MaterialDestructionController::addDocument',['filter' => 'accessControl:2']); 
    $routes->post('addMD', 'MaterialDestructionController::addMD',['filter' => 'accessControl:2']); 
    $routes->post('listdataPemusnahan', 'MaterialDestructionController::listdataPemusnahan',['filter' => 'accessControl:2']); 
    $routes->post('delete', 'MaterialDestructionController::delete',['filter' => 'accessControl:2']); 
    $routes->post('deleteList/(:any)', 'MaterialDestructionController::deleteList/$1',['filter' => 'accessControl:2']); 
    $routes->post('datamd/(:any)', 'MaterialDestructionController::datamd/$1',['filter' => 'accessControl:2']); 
    $routes->post('posting/(:any)', 'MaterialDestructionController::posting/$1',['filter' => 'accessControl:2']); 
    
});

$routes->group('requisition', function ($routes) {
    $routes->get('form/(:any)', 'MaterialRequisition::mr/$1',['filter' => 'accessControl:2']); 
    $routes->post('addDocument', 'MaterialRequisition::addDocument',['filter' => 'accessControl:2']); 
    $routes->post('addMD', 'MaterialRequisition::addMD',['filter' => 'accessControl:2']); 
    $routes->post('listdata', 'MaterialRequisition::listdata',['filter' => 'accessControl:2']); 
    $routes->post('delete', 'MaterialRequisition::delete',['filter' => 'accessControl:2']); 
    $routes->post('deleteList/(:any)', 'MaterialRequisition::deleteList/$1',['filter' => 'accessControl:2']); 
    $routes->post('datamd/(:any)', 'MaterialRequisition::datamd/$1',['filter' => 'accessControl:2']); 
    $routes->post('posting/(:any)', 'MaterialRequisition::posting/$1',['filter' => 'accessControl:2']); 
    $routes->get('WoAvailablelistdata/(:any)', 'MaterialRequisition::WoAvailablelistdata/$1',['filter' => 'accessControl:2']); 
    $routes->get('dataRequestList/(:any)', 'MaterialRequisition::dataRequestList/$1',['filter' => 'accessControl:2']); 
    $routes->post('submitRequest', 'MaterialRequisition::submitRequest',['filter' => 'accessControl:2']); 
    $routes->post('deleteList/(:any)', 'MaterialRequisition::deleteList/$1',['filter' => 'accessControl:2']); 
    
});

$routes->group('requisitionprogress', function ($routes) {
  
    $routes->post('listdata', 'MaterialRequisitionProgress::listdata',['filter' => 'accessControl:2']); 
    $routes->get('form/(:any)', 'MaterialRequisitionProgress::mr/$1',['filter' => 'accessControl:2']); 
    $routes->post('posting/(:any)', 'MaterialRequisitionProgress::posting/$1',['filter' => 'accessControl:2']); 
    
    
});

$routes->group('scrap', function ($routes) {
  
    $routes->post('add', 'ScrapController::add',['filter' => 'accessControl:2']); 
    $routes->post('listdataScrap', 'ScrapController::listdataScrap',['filter' => 'accessControl:2']); 
    $routes->post('addScrap', 'ScrapController::addScrap',['filter' => 'accessControl:2']); 
    $routes->get('form/(:any)', 'ScrapController::scrap_form/$1',['filter' => 'accessControl:2']); 
    $routes->post('listdataWO/(:any)', 'ScrapController::listdataWO/$1',['filter' => 'accessControl:2']); 
    $routes->get('WoAvailablelistdata/(:any)', 'ScrapController::WoAvailablelistdata/$1',['filter' => 'accessControl:2']); 
    $routes->get('materialScrapList/(:any)', 'ScrapController::materialScrapList/$1',['filter' => 'accessControl:2']); 
    $routes->post('deleteList/(:any)', 'ScrapController::deleteList/$1',['filter' => 'accessControl:2']); 
    $routes->post('posting/(:any)', 'ScrapController::posting/$1',['filter' => 'accessControl:2']); 
    
    
});
