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
        $column_order = array(NULL,'product.nama','product.status','product.id');
        $column_search = array('product.nama','product.judul');
        $order = array('product.id' => 'desc');
        $list = $list_data->get_datatables('product', $column_order, $column_search, $order, $where);
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
          $no++;
          $row    = array();
          $row[] = $no;
          $row[] = $lists->id;
          $row[] = $lists->nama;
          $row[] = $lists->status;
          $data[] = $row;
      }
      $output = array(
          "draw" => $request->getPost("draw"),
          "recordsTotal" => $list_data->count_all('product', $where),
          "recordsFiltered" => $list_data->count_filtered('product', $column_order, $column_search, $order, $where),
          "data" => $data,
      );
      
      return json_encode($output);
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
}
