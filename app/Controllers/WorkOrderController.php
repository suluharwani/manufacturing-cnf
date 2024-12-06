<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 
class WorkOrderController extends BaseController
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
  function access($page){
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'],$page);
  }
  public function index()
  {
        //
  }
  public function listdataOrder(){
    
    $serverside_model = new \App\Models\Mdl_datatables();
    $request = \Config\Services::request();
    $list_data = $serverside_model;
    $where = ['id !=' => 0, 'deleted_at'=>NULL];
                //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
                //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
    $column_order = array(NULL,'orders.order_number','orders.customer_name','order.due','orders.status','orders.due_date','orders.delivery_date');
    $column_search = array('orders.order_number','orders.customer_name','order.due','orders.status','orders.due_date','orders.delivery_date');
    $order = array('orders.id' => 'desc');
    $list = $list_data->get_datatables('orders', $column_order, $column_search, $order, $where);
    $data = array();
    $no = $request->getPost("start");
    foreach ($list as $lists) {
      $no++;
      $row    = array();
      $row[] = $no;
      $row[] = $lists->id;
      $row[] = $lists->order_name;
      $row[] = $lists->status;

      $data[] = $row;
    }
    $output = array(
      "draw" => $request->getPost("draw"),
      "recordsTotal" => $list_data->count_all('orders', $where),
      "recordsFiltered" => $list_data->count_filtered('orders', $column_order, $column_search, $order, $where),
      "data" => $data,
    );

    return json_encode($output);
  }
  function addOrder(){
        
        $userInfo = $_SESSION['auth'];
        $MdlMaterial = new \App\Models\MdlMaterial();
        $MdlMaterialDet = new \App\Models\MdlMaterialDet();
        $dataMaterial = [
          "kode" =>  $_POST["kode"],
          "name" =>  $_POST["nama"]
          
        ];

        if ($MdlMaterial->insert($dataMaterial)) {
          $query = $MdlMaterial->orderBy('id', 'DESC')->first();
          $materialDet =["material_id" => $query['id'],
                         "type_id"=>$_POST["type"],
                         "satuan_id"=>$_POST["satuanUkuran"],
                        ];
          if ($MdlMaterialDet->insert($materialDet)) {
            $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan material: ".$_POST['nama']."";
            header('HTTP/1.1 200 OK');
          }
        
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan material: ".$_POST['nama'];
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
      
    }
}
