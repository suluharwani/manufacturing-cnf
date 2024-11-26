<?php
namespace App\Controllers;
use AllowDynamicProperties; 
use CodeIgniter\Controller;
use Bcrypt\Bcrypt;
use google\apiclient;
class Stock extends BaseController
{
  protected $bcrypt;
  protected $userValidation;
  protected $bcrypt_version;
  protected $session;
  protected $db;
  protected $uri;
  protected $form_validation;
  protected $changelog;

  public function __construct()
  {
  //   parent::__construct();
    $this->db      = \Config\Database::connect();
    $this->session = session();
    $this->bcrypt = new Bcrypt();
    $this->bcrypt_version = '2a';
    $this->uri = service('uri');
    helper('form');
    $this->form_validation = \Config\Services::validation();
    $this->userValidation = new \App\Controllers\LoginValidation();
    $this->changelog = new \App\Controllers\Changelog();

    //if sesion habis

    $check = new \App\Controllers\CheckAccess();
    $check->logged();  
  }
  public function index()
  {

  }

  // function logoutAdmin(){
  //     return "adad";
  // }
  function access($page){
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'],$page);
  }
  public function addStock(){
    $this->access('operator');
    return view('admin/content/inputStock');
  }
      public function stockdata()
      {
          
          $serverside_model = new \App\Models\MdlDatatableJoin();
          $request = \Config\Services::request();
          
          // Define the columns to select
          $select_columns = 'stock.*, materials.name as name, materials.kode as kode, satuan.nama as satuan,  satuan.kode as kode_satuan, currency.rate as rate, currency.kode as kode_currency';
          
          // Define the joins (you can add more joins as needed)
          $joins = [
              ['materials', 'stock.id_material = materials.id', 'left'],
              ['materials_detail', 'materials_detail.material_id = materials.id', 'left'],
              ['type', 'type.id = materials_detail.type_id', 'left'],
              ['satuan', 'satuan.id = materials_detail.satuan_id', 'left'],
              ['currency', 'currency.id = stock.id_currency', 'left'],

          ];
  
          $where = ['stock.deleted_at' => NULL];
  
          // Column Order Must Match Header Columns in View
          $column_order = array(
              NULL, 
              'materials.kode', 
              'materials.name', 
              'type.id',
              'materials.id',
              'materials.id',
              'materials.id',
              'materials.id',
              'materials.id',
              'materials.id',
              'materials.id'
          );
          $column_search = array(
              'materials.name', 
              'materials.kode', 
       
          );
          $order = array('stock.id' => 'desc');
  
          // Call the method to get data with dynamic joins and select fields
          $list = $serverside_model->get_datatables('stock', $select_columns, $joins, $column_order, $column_search, $order, $where);
          
          $data = array();
          $no = $request->getPost("start");
          foreach ($list as $lists) {
              $no++;
              $row = array();
              $row[] = $no;
              $row[] = $lists->id;
              $row[] = $lists->name;
              $row[] = $lists->kode;
              $row[] = $lists->stock_awal;
              $row[] = $lists->stock_masuk;
              $row[] = $lists->stock_keluar;
              $row[] = $lists->satuan;
              $row[] = $lists->kode_satuan;
              $row[] = $lists->id_material;
              $row[] = $lists->price;
              $row[] = $lists->rate;
              $row[] = $lists->kode_currency;
              $data[] = $row;
          }
  
          $output = array(
              "draw" => $request->getPost("draw"),
              "recordsTotal" => $serverside_model->count_all('stock', $where),
              "recordsFiltered" => $serverside_model->count_filtered('stock', $select_columns, $joins, $column_order, $column_search, $order, $where),
              "data" => $data,
          );
  
        //   return $this->response->setJSON($output);
        
          return json_encode($output);
      }

}