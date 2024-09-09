<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 

class MaterialController extends BaseController
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
    function access($page){
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'],$page);
        }
    public function listdataMaterial(){
        $this->access('operator');
        $serverside_model = new \App\Models\Mdl_datatables();
        $request = \Config\Services::request();
        $list_data = $serverside_model;
        $where = ['id !=' => 0, 'deleted_at'=>NULL];
                //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
                //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
        $column_order = array(NULL,'materials.nama','materials.kode','materials.id');
        $column_search = array('materials.name','materials.kode');
        $order = array('materials.id' => 'desc');
        $list = $list_data->get_datatables('materials', $column_order, $column_search, $order, $where);
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
          $no++;
          $row    = array();
          $row[] = $no;
          $row[] = $lists->id;
          $row[] = $lists->name;
          $row[] = $lists->kode;
        
          $data[] = $row;
      }
      $output = array(
          "draw" => $request->getPost("draw"),
          "recordsTotal" => $list_data->count_all('materials', $where),
          "recordsFiltered" => $list_data->count_filtered('materials', $column_order, $column_search, $order, $where),
          "data" => $data,
      );
      
      return json_encode($output);
      }
      public function listdataMaterialJoin()
      {
          $this->access('operator');
          $serverside_model = new \App\Models\MdlDatatableJoin();
          $request = \Config\Services::request();
          
          // Define the columns to select
          $select_columns = 'materials.*, materials_detail.type_id as type_id, type.nama as nama_type, satuan.kode as kode_satuan, satuan.nama as satuan';
          
          // Define the joins (you can add more joins as needed)
          $joins = [
              ['materials_detail', 'materials_detail.material_id = materials.id', 'left'],
              ['type', 'type.id = materials_detail.type_id', 'left'],
              ['satuan', 'satuan.id = materials_detail.satuan_id', 'left'],

          ];
  
          $where = ['materials.id !=' => 0, 'materials.deleted_at' => NULL];
  
          // Column Order Must Match Header Columns in View
          $column_order = array(
              NULL, 
              'materials.name', 
              'materials.kode', 
              'materials.id',
              'type.nama'
          );
          $column_search = array(
              'materials.name', 
              'materials.kode', 
              'materials_detail.id', 
          );
          $order = array('materials.id' => 'desc');
  
          // Call the method to get data with dynamic joins and select fields
          $list = $serverside_model->get_datatables('materials', $select_columns, $joins, $column_order, $column_search, $order, $where);
          
          $data = array();
          $no = $request->getPost("start");
          foreach ($list as $lists) {
              $no++;
              $row = array();
              $row[] = $no;
              $row[] = $lists->id;
              $row[] = $lists->name;
              $row[] = $lists->kode;
              $row[] = $lists->type_id;
              $row[] = $lists->nama_type;
              $row[] = $lists->kode_satuan;
              $row[] = $lists->satuan;
// From joined suppliers table
              $data[] = $row;
          }
  
          $output = array(
              "draw" => $request->getPost("draw"),
              "recordsTotal" => $serverside_model->count_all('materials', $where),
              "recordsFiltered" => $serverside_model->count_filtered('materials', $select_columns, $joins, $column_order, $column_search, $order, $where),
              "data" => $data,
          );
  
          return $this->response->setJSON($output);
      }
      function tambah_tipe(){
        $this->access('operator');
        $userInfo = $_SESSION['auth'];
        $MdlType = new \App\Models\MdlType();
        $dataType = [
          "kode" =>  $_POST["kode"],
          "nama" =>  $_POST["nama"]
        ];
        if ($MdlType->insert($dataType)) {
          $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan client: ".$_POST['nama']."sebagai type baru";
          header('HTTP/1.1 200 OK');
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan type: ".$_POST['nama'];
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
      
    }
    function hapus_tipe(){
        $this->access('operator');
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $mdl = new \App\Models\MdlType();
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus type $nama";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
       function type_list(){
        $this->access('operator');
        $mdl = new \App\Models\MdlType();
        $data = $mdl->get()
                ->getResultArray();
        return json_encode($data);
       }
       function tambah_satuan(){
        $this->access('operator');
        $userInfo = $_SESSION['auth'];
        $MdlType = new \App\Models\MdlSatuan();
        $dataType = [
          "kode" =>  $_POST["kode"],
          "nama" =>  $_POST["nama"]
        ];
        if ($MdlType->insert($dataType)) {
          $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan client: ".$_POST['nama']."sebagai type baru";
          header('HTTP/1.1 200 OK');
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan satuan: ".$_POST['nama'];
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
      
    }
       function satuan_list(){
        $this->access('operator');
        $mdl = new \App\Models\MdlSatuan();
        $data = $mdl->get()
                ->getResultArray();
        return json_encode($data);
       }

       public function listdataProdukJoin()
       {
           $this->access('operator');
           $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'product.*,';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
 
           ];
   
           $where = ['product.id !=' => 0, 'product.deleted_at' => NULL];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'product.name', 
               'product.kode', 
               'product.id',
           );
           $column_search = array(
               'product.name', 
               'product.kode', 
          
           );
           $order = array('product.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('product', $select_columns, $joins, $column_order, $column_search, $order, $where);
           
           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->id;
               $row[] = $lists->nama;
               $row[] = $lists->kode;

 // From joined suppliers table
               $data[] = $row;
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('product', $where),
               "recordsFiltered" => $serverside_model->count_filtered('product', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
   
           return $this->response->setJSON($output);
       }

       function tambah_material(){
        $this->access('operator');
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
                         "satuan_id"=>$_POST["type"],
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
