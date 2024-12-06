<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceDetail;
use App\Models\MdlCustomer;

class ProformaInvoiceController extends BaseController
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
    public function pi($id)
    {
         $Mdl = new ProformaInvoice();
    $MdlDetail = new ProformaInvoiceDetail();
$dataPembelian = $Mdl
                ->select('proforma_invoice.*, customer.id_currency as curr_id, currency.kode as curr_code, currency.nama as curr_name, customer.customer_name')
                ->join('customer', 'customer.id = proforma_invoice.customer_id','left')    
                ->join('currency', 'currency.id = customer.id_currency','left')    
                ->where('proforma_invoice.id', $id)->get()->getResultArray();
// $dataPembelianDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materials","materials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataPembelianDetail);
// die();
        $data['pi'] = $dataPembelian;

        // var_dump($data['pi']);
        // die();
        $data['content'] = view('admin/content/form_pi',$data);
        return view('admin/index', $data);
    }
    public function listdataPi($id){
         $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'proforma_invoice_details.*,product.nama as nama, product.kode as kode,product.id as id_product';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
                 ['product', 'product.id = proforma_invoice_details.id_product', 'left'],
           ];
   
           $where = ['proforma_invoice_details.invoice_id ' => $id, 'proforma_invoice_details.deleted_at' => NULL];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'product.nama', 
               'product.kode', 
               'id_product',
               'id_product',
               'id_product',
               'id_product',
               'id_product',
           );
           $column_search = array(
               'product.nama', 
               'product.kode', 
          
           );
           $order = array('proforma_invoice_details.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('proforma_invoice_details', $select_columns, $joins, $column_order, $column_search, $order, $where);

           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->id_product;
               $row[] = $lists->nama;
               $row[] = $lists->kode;
               $row[] = $lists->quantity;

 // From joined suppliers table
               $data[] = $row;
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('proforma_invoice_details', $where),
               "recordsFiltered" => $serverside_model->count_filtered('proforma_invoice_details', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
          

           return $this->response->setJSON($output);
    }
    public function listdata(){
$serverside_model = new \App\Models\MdlDatatableJoin();
          $request = \Config\Services::request();
          
          // Define the columns to select
          $select_columns = 'proforma_invoice.*, customer.customer_name, customer.code as cus_code, customer.address as customer_address ';
          
          // Define the joins (you can add more joins as needed)
          $joins = [
              ['customer', 'customer.id = proforma_invoice.customer_id', 'left'],

          ];
  
          $where = ['proforma_invoice.deleted_at' => NULL];
  
          // Column Order Must Match Header Columns in View
          $column_order = array(
              NULL, 
              'proforma_invoice.invoice_number', 
              'proforma_invoice.invoice_date', 
              'customer.customer_name',
              'proforma_invoice.id', 

          );
          $column_search = array(
              'customer.customer_name', 
              'proforma_invoice.invoice_number', 
       
          );
          $order = array('proforma_invoice.id' => 'desc');
  
          // Call the method to get data with dynamic joins and select fields
          $list = $serverside_model->get_datatables('proforma_invoice', $select_columns, $joins, $column_order, $column_search, $order, $where);
          
          $data = array();
          $no = $request->getPost("start");
          foreach ($list as $lists) {
              $no++;
              $row = array();
              $row[] = $no;
              $row[] = $lists->id;
              $row[] = $lists->invoice_number;
              $row[] = $lists->invoice_date;
              $row[] = $lists->customer_name;
              $data[] = $row;
          }
  
          $output = array(
              "draw" => $request->getPost("draw"),
              "recordsTotal" => $serverside_model->count_all('proforma_invoice', $where),
              "recordsFiltered" => $serverside_model->count_filtered('proforma_invoice', $select_columns, $joins, $column_order, $column_search, $order, $where),
              "data" => $data,
          );
  
        //   return $this->response->setJSON($output);
        
          return json_encode($output);
    }
    function getCustomerList(){
        $model = new MdlCustomer();
        $customer = $model->findAll();

        return $this->response->setJSON($customer);
    
    }
    public function add(){
         $mdl = new ProformaInvoice();
         $mdl->insert($_POST);

           if ($mdl->affectedRows() !== 0) {
        $riwayat = "Menambahkan Proforma Invoice ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
    }
public function addProduct(){
             $mdl = new ProformaInvoiceDetail();
         $mdl->insert($_POST);

           if ($mdl->affectedRows() !== 0) {
        $riwayat = "Menambahkan Proforma Invoice ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}
public function get_list(){
         $mdl = new ProformaInvoice();
         $data =  $mdl->orderBy('id','desc')->get()->getResultArray();
        return json_encode($data);
}

}
