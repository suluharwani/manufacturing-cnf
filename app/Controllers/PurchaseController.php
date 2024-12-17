<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MdlPurchaseOrder;
use App\Models\MdlPurchaseOrderList;

class PurchaseController extends BaseController
{
       protected $changelog;
  public function __construct()
  {
    //   parent::__construct();
    $this->db      = \Config\Database::connect();
    $this->session = session();
    $this->uri = service('uri');
    helper('form');
    $this->form_validation = \Config\Services::validation();
    $this->userValidation = new \App\Controllers\LoginValidation();
    $this->changelog = new \App\Controllers\Changelog();

      //if sesion habis
      //check access
    $check = new \App\Controllers\CheckAccess();
    $check->logged();
      //check access

  }
    public function index()
    {
        //
    }
      public function listdataPurchaseOrder(){
    
         $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'purchase_order.*, supplier.supplier_name';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
                 ['supplier', 'supplier.id = purchase_order.supplier_id', 'left'],
           ];
   
           $where = [ 'purchase_order.deleted_at' => NULL];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'purchase_order.code', 
               'purchase_order.date', 
               'purchase_order.status',
               'purchase_order.id',
   
           );
           $column_search = array(
               'purchase_order.kode', 
               'supplier.supplier_name', 
          
           );
           $order = array('purchase_order.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('purchase_order', $select_columns, $joins, $column_order, $column_search, $order, $where);

           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->code;
               $row[] = $lists->date;
               $row[] = $lists->status;
               $row[] = $lists->id;
               $row[] = $lists->supplier_name;
    


 // From joined suppliers table
               $data[] = $row;
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('purchase_order', $where),
               "recordsFiltered" => $serverside_model->count_filtered('purchase_order', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
          

           return $this->response->setJSON($output);
    }
        public function add_po(){
         $mdl = new MdlPurchaseOrder();
         $mdl->insert($_POST);

           if ($mdl->affectedRows() !== 0) {
        $riwayat = "Menambahkan Purchase order ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
    }
}
