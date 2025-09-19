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
    
    function access($page){
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'],$page);
        }
public function listdataMaterialJoin()
{
    $serverside_model = new \App\Models\MdlDatatableJoin();
    $request = \Config\Services::request();
    
    // Define the columns to select with COALESCE to handle NULL stock values
    $select_columns = 'stock.*, materials.*, materials_detail.kite as kite, 
                      materials_detail.type_id as type_id, type.nama as nama_type, 
                      satuan.kode as kode_satuan, satuan.nama as satuan, 
                      materials_detail.hscode as hscode,
                      COALESCE(stock.stock_awal, 0) as stock_awal,
                      COALESCE(stock.stock_masuk, 0) as stock_masuk,
                      COALESCE(stock.stock_keluar, 0) as stock_keluar,
                      COALESCE(stock.selisih_stock_opname, 0) as selisih_stock_opname';  
    
    // Define the joins
    $joins = [
        ['stock', 'stock.id_material = materials.id', 'right'],
        ['materials_detail', 'materials_detail.material_id = materials.id', 'left'],
        ['type', 'type.id = materials_detail.type_id', 'left'],
        ['satuan', 'satuan.id = materials_detail.satuan_id', 'left'],
    ];

    $where = ['materials.id !=' => 0, 'materials.deleted_at' => NULL];

    // Column Order Must Match Header Columns in View
    $column_order = array(
        NULL, 
        'materials.kode', 
        'materials.name', 
        'type.id',
        'materials_detail.kite',
        'materials.id',
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
        'materials_detail.hscode', 
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
        $row[] = $lists->kite;
        $row[] = $lists->nama_type;
        $row[] = $lists->kode_satuan;
        $row[] = $lists->satuan;
        $row[] = $lists->hscode;
        $row[] = $lists->stock_awal;
        $row[] = $lists->stock_masuk;
        $row[] = $lists->stock_keluar;
        $row[] = $lists->selisih_stock_opname;
        
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
        
        $mdl = new \App\Models\MdlType();
        $data = $mdl->get()
                ->getResultArray();
        return json_encode($data);
       }
       function tambah_satuan(){
        
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
        
        $mdl = new \App\Models\MdlSatuan();
        $data = $mdl->get()
                ->getResultArray();
        return json_encode($data);
       }



       function tambah_material() {
    
    $userInfo = $_SESSION['auth'];
    $MdlMaterial = new \App\Models\MdlMaterial();
    $MdlMaterialDet = new \App\Models\MdlMaterialDet();

    // Cek jika kode sudah ada
    $existingMaterial = $MdlMaterial->where('kode', $_POST["kode"])->first();
    if ($existingMaterial) {
        $riwayat = "User " . $userInfo['nama_depan'] . " gagal menambahkan material: Kode " . $_POST['kode'] . " sudah ada.";
        header('HTTP/1.1 409 Conflict');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Kode sudah ada, gagal menambahkan data.', 'code' => 4)));
    }

    $dataMaterial = [
        "kode" => $_POST["kode"],
        "name" => $_POST["nama"]
    ];

    if ($MdlMaterial->insert($dataMaterial)) {
        $query = $MdlMaterial->orderBy('id', 'DESC')->first();
        $materialDet = [
            "material_id" => $query['id'],
            "type_id" => $_POST["type"],
            "satuan_id" => $_POST["satuanUkuran"],
            "kite" => $_POST["kite"]
        ];
        if ($MdlMaterialDet->insert($materialDet)) {
            $riwayat = "User " . $userInfo['nama_depan'] . " " . $userInfo['nama_belakang'] . " menambahkan material: " . $_POST['nama'] . "";
            header('HTTP/1.1 200 OK');
        }

    } else {
        $riwayat = "User " . $userInfo['nama_depan'] . " gagal menambahkan material: " . $_POST['nama'];
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Gagal menambahkan data.', 'code' => 3)));
    }
    $this->changelog->riwayat($riwayat);
}

    public function get_material($id)
    {
        $model = new \App\Models\MdlMaterial();
        $material = $model->getMaterialWithDetails($id);

        if ($material) {
            return $this->response->setJSON($material);
        } else {
            return $this->response->setJSON(['message' => 'Material not found'], 404);
        }
    }

    // Function to update material details


    // Function to delete a material
function delete(){
        
        $param = $_POST['param'];

        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlMaterial();
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus material $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => $param)));
        }
       } 
    function satuanDelete(){
        
        $param = $_POST['param'];
        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlSatuan();
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus satuan $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
       function typeDelete(){
        
        $param = $_POST['param'];
        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlType();
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus tipe $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
           function materialUpdate(){
      
        $param = $_POST['param'];
        $id = $param['id'];
        $data['name'] = $param['nama'];
        $data['kode'] = $param['kode'];
        $dataDet['type_id'] = $param['type'];
        $dataDet['kite'] = $param['kite'];
        $dataDet['satuan_id'] = $param['satuanUkuran'];
        $dataDet['hscode'] = $param['hscode'];

        $mdl = new \App\Models\MdlMaterial();
        $mdlDet = new \App\Models\MdlMaterialDet();
        $mdl->set($data);
        $mdl->where('id',$id);
        $mdl->update();


         $mdlDet->set($dataDet);
            $mdlDet->where('material_id',$id);
            $mdlDet->update();
        if ($mdl->affectedRows()!=0) {
           
          $riwayat = "update material {$data['name']}";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
    }
        function satuanUpdate(){
      
        $param = $_POST['params'];
        $id = $param['id'];
        $data['nama'] = $param['name'];
        $data['kode'] = $param['code'];
        $mdl = new \App\Models\MdlSatuan();
        
        $mdl->set($data);
        $mdl->where('id',$id);
        $mdl->update();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "update satuan {$data['nama']}";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
    }
        function typeUpdate(){
      
        $param = $_POST['params'];
        $id = $param['id'];
        $data['nama'] = $param['name'];
        $data['kode'] = $param['code'];
        $mdl = new \App\Models\MdlType();
        
        $mdl->set($data);
        $mdl->where('id',$id);
        $mdl->update();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "update type {$data['nama']}";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
    }
}
 