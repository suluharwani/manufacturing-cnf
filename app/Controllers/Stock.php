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
    $this->db = \Config\Database::connect();
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
  function access($page)
  {
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'], $page);
  }
  public function addStock()
  {
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
      $row[] = $this->get_stock_in_out($lists->id_material)['total_in']; //in
      $row[] = $this->get_stock_in_out($lists->id_material)['total_out']; //out
      $row[] = $lists->satuan;
      $row[] = $lists->kode_satuan;
      $row[] = $lists->id_material;
      $row[] = $lists->price;
      $row[] = $lists->rate;
      $row[] = $lists->kode_currency;
      $row[] = $this->get_stock_in_out($lists->id_material)['so']; //stock opname
      $row[] = $lists->stock_awal + $this->get_stock_in_out($lists->id_material)['total']; //stock
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

  function get_stock_in_out($id)
  {
    $data['total_in'] = $this->materialPurchase($id) + $this->materialReturn($id);
    $data['total_out'] = $this->materialDestruction($id) + $this->materialRequisition($id);
    $data['so'] = $this->materialStockOpname($id);
    $data['total'] = $data['total_in'] + $data['total_out'] + $data['so'];
    // var_dump($data);
    return $data;

  }
  public function materialReturn($id)
  {
    $mdl = new \App\Models\MdlMaterialReturnList();

    // Initialize the query
    $query = $mdl->select(' 

        sum(material_return_list.jumlah) as jumlah, 
 ') // Select fields from both tables
      ->join('materials', 'materials.id = material_return_list.id_material') // Join with materials table
      ->join('material_return', 'material_return.id = material_return_list.id_material_return')
      ->where('material_return.status', 1)
      ->where('material_return_list.id_material', $id);


    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialRequisition($id)
  {
    $mdl = new \App\Models\MdlMaterialRequisitionProgress();


    $query = $mdl->select(' 
        sum(-(material_requisition_progress.jumlah)) as jumlah, 
       ')
      ->join('material_requisition_list', 'material_requisition_list.id = material_requisition_progress.id_material_requisition_list') // Join with materials table

      ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition') // Join with materials table
      ->where('material_requisition.status', 1)
      ->where('material_requisition_progress.id_material', $id);
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialPurchase($id)
  {
    $mdl = new \App\Models\MdlPembelianDetail();


    // Initialize the query
    $query = $mdl->select(' 
        sum(pembelian_detail.jumlah) as jumlah, 
') // Select fields from both tables
      ->join('pembelian', 'pembelian.id = pembelian_detail.id_pembelian') // Join with materials table
      ->where('pembelian.posting', 1)
      ->where('pembelian_detail.id_material', $id);

    // Add date range conditions if provided


    // Fetch the purchase details
    $data = $query->findAll();

    // Return the data as JSON or load a view as needed

    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialDestruction($id)
  {
    $mdl = new \App\Models\MdlMaterialDestructionList();


    // Initialize the query
    $query = $mdl->select(' 
       
        sum(-(material_destruction_list.jumlah)) as jumlah, 
         ') // Select fields from both tables
      ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction') // Join with materials table
      ->where('material_destruction.status', 1)
      ->where('material_destruction_list.id_material', $id);

    // Add date range conditions if provided


    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialStockOpname($id)
  {
    $mdl = new \App\Models\MdlStockOpnameList();

    // Get the material ID and date range from the POST request


    // Initialize the query
    $query = $mdl->select(' 
       
        sum((stock_opname_list.jumlah_akhir - stock_opname_list.jumlah_awal)) as jumlah, 
         ') // Select fields from both tables
         ->join('stock_opname', 'stock_opname.id = stock_opname_list.id_stock_opname')
            ->where('stock_opname.status', 1)
      ->where('stock_opname_list.id_material', $id);

    // Add date range conditions if provided

    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }

}