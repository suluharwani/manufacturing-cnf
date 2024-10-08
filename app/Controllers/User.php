<?php
namespace App\Controllers;
use AllowDynamicProperties; 
use CodeIgniter\Controller;
use Bcrypt\Bcrypt;

class User extends BaseController
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
      //check access
      $check = new \App\Controllers\CheckAccess();
      $check->logged();
      //check access
     
}
public function index()
{


}
function access($page){
$check = new \App\Controllers\CheckAccess();
$check->access($_SESSION['auth']['id'],$page);
}
public function user($jenis=null){
    if ($jenis == "administrator") {
       $this->access('administrator');

        $data['content']=view('admin/content/administrator');

    }else if($jenis == "client"){
       $this->access('client');
        $data['content']=view('admin/content/user');

    }else{
        $data['content']="";

    }
    return view('admin/index', $data);
}
public function listdata_user(){
  $this->access('administrator');
  $serverside_model = new \App\Models\Mdl_datatables();
  $request = \Config\Services::request();
  $list_data = $serverside_model;
          // $level = $_POST['level'];
          // if ($level == "all") {
  $where = ['id !=' => 0, 'deleted_at'=>NULL];
          // }else{
          //   $where = ['level' => $level,];
          // } 
          //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
          //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
  $column_order = array(NULL,'users.nama_depan','users.profile_picture','users.level','users.status','users.id');
  $column_search = array('users.nama_depan','users.nama_belakang','users.email','users.id');
  $order = array('users.id' => 'desc');
  $list = $list_data->get_datatables('users', $column_order, $column_search, $order, $where);
  $data = array();
  $no = $request->getPost("start");
  foreach ($list as $lists) {
    $no++;
    $row    = array();
    $row[] = $no;
    $row[] = $lists->nama_depan;
    $row[] = $lists->nama_belakang;
    $row[] = $lists->email;
    $row[] = $lists->id;
    $row[] = $lists->level;
    $row[] = $lists->status;

    $data[] = $row;
}
$output = array(
    "draw" => $request->getPost("draw"),
    "recordsTotal" => $list_data->count_all('users', $where),
    "recordsFiltered" => $list_data->count_filtered('users', $column_order, $column_search, $order, $where),
    "data" => $data,
);

return json_encode($output);
}

  function tambah_admin(){
      $this->access('administrator');
      $userInfo = $_SESSION['auth'];
      $userModel = new \App\Models\MdlUser();
      $userdata = [
        "nama_depan"=>  $_POST["namaDepan"],
        "nama_belakang"=> $_POST["namaBelakang"],
        "email" =>  $_POST["email"],
        "password" =>  $this->bcrypt->encrypt($_POST["password"],$this->bcrypt_version),
        "level" => 2,
        "status" => 1
      ];
      if ($userModel->createNewUser($userdata)) {
        $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_depan']." menambahkan user: ".$_POST['email']."sebagai Admin";
        header('HTTP/1.1 200 OK');
      }else{
        $riwayat = "User ".$userInfo['name']." gagal menambahkan user: ".$_POST['email'];
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
      }
      $this->changelog->riwayat($riwayat);
    
  }
  function hapus_user(){
      $this->access('administrator');
      $id = $_POST['id'];
      $nama = $_POST['nama'];
      $mdl = new \App\Models\MdlUser();
      $mdl->where('id',$id);
      $mdl->delete();
      if ($mdl->affectedRows()!=0) {
        $riwayat = "Menghapus user $nama";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
      }else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
      }
     } 
  function reset_password(){
      $this->access('administrator');
      $db      = \Config\Database::connect();
      $builder = $db->table('user');

      $this->form_validation->setRules([
        'password' => 'required|min_length[4]|max_length[39]'
      ]);
      if ($this->form_validation->withRequest($this->request)->run()) {
        $id = $_POST["id"];
        $password = $_POST["password"];
        $builder->set('password', $this->bcrypt->encrypt($password, $this->bcrypt_version));
        $builder->where('id', $id);
        if ($builder->update()) {
          $riwayat = "Mengubah password Admin id: $id menjadi $password";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'ERROR, gagal mengubah password', 'code' => 4)));
        }
      }else{
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR, Password harus lebih dari 4 karakter, max 39', 'code' => 5)));
      }
    
  }
    function ubah_status_user(){
      $this->access('administrator');
      // code...

      $id = $_POST['id'];
      $status = $_POST['status'];
      $data_status = array('status' => $status );
      $mdl = new \App\Models\MdlUser();
      $mdl->set($data_status);
      $mdl->where('id',$id);
      $mdl->update();
      if ($mdl->affectedRows()>0) {
        $riwayat = "Mengubah status user id: $id dengan status $status ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
      }else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
      }

  }
    function ubah_level_user(){
      $this->access('administrator');
      // code...

      $id = $_POST['id'];
      $level = $_POST['level'];
      $data_level = array('level' => $level );
      $mdl = new \App\Models\MdlUser();
      $mdl->set($data_level);
      $mdl->where('id',$id);
      $mdl->update();
      if ($mdl->affectedRows()>0) {
        $riwayat = "Mengubah status user id: $id dengan level $level ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
      }else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
      }
   
  }
//client
  public function listdata_client(){
  $this->access('operator');
  $serverside_model = new \App\Models\Mdl_datatables();
  $request = \Config\Services::request();
  $list_data = $serverside_model;
  $where = ['id !=' => 0, 'deleted_at'=>NULL];
          //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
          //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
  $column_order = array(NULL,'client.nama_depan','client.profile_picture','client.status','client.id');
  $column_search = array('client.nama_depan','client.nama_belakang','client.email','client.id');
  $order = array('client.id' => 'desc');
  $list = $list_data->get_datatables('client', $column_order, $column_search, $order, $where);
  $data = array();
  $no = $request->getPost("start");
  foreach ($list as $lists) {
    $no++;
    $row    = array();
    $row[] = $no;
    $row[] = $lists->nama_depan;
    $row[] = $lists->nama_belakang;
    $row[] = $lists->email;
    $row[] = $lists->id;
    $row[] = $lists->nama_depan.' '.$lists->nama_belakang;
    $row[] = $lists->status;
    $row[] = $lists->profile_picture;
    $data[] = $row;
}
$output = array(
    "draw" => $request->getPost("draw"),
    "recordsTotal" => $list_data->count_all('client', $where),
    "recordsFiltered" => $list_data->count_filtered('client', $column_order, $column_search, $order, $where),
    "data" => $data,
);

return json_encode($output);
}

  function tambah_client(){
      $this->access('operator');
      $userInfo = $_SESSION['auth'];
      $userModel = new \App\Models\MdlClient();
      $userdata = [
        "email" =>  $_POST["email"],
        "password" =>  $this->bcrypt->encrypt($_POST["password"],$this->bcrypt_version),
        "status" => 1
      ];
      if ($userModel->createNewUser($userdata)) {
        $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_depan']." menambahkan client: ".$_POST['email']."sebagai client baru";
        header('HTTP/1.1 200 OK');
      }else{
        $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan client: ".$_POST['email'];
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
      }
      $this->changelog->riwayat($riwayat);
    
  }
  function hapus_client(){
      $this->access('operator');
      $id = $_POST['id'];
      $nama = $_POST['nama'];
      $mdl = new \App\Models\MdlClient();
      $mdl->where('id',$id);
      $mdl->delete();
      if ($mdl->affectedRows()!=0) {
        $riwayat = "Menghapus user $nama";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
      }else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
      }
     } 
  function reset_password_client(){
      $this->access('operator');
      $db      = \Config\Database::connect();
      $builder = $db->table('client');

      $this->form_validation->setRules([
        'password' => 'required|min_length[4]|max_length[39]'
      ]);
      if ($this->form_validation->withRequest($this->request)->run()) {
        $id = $_POST["id"];
        $password = $_POST["password"];
        $builder->set('password', $this->bcrypt->encrypt($password, $this->bcrypt_version));
        $builder->where('id', $id);
        if ($builder->update()) {
          $riwayat = "Mengubah password Admin id: $id menjadi $password";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'ERROR, gagal mengubah password', 'code' => 4)));
        }
      }else{
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR, Password harus lebih dari 4 karakter, max 39', 'code' => 5)));
      }
    
  }
    function ubah_status_client(){
      $this->access('operator');
      // code...

      $id = $_POST['id'];
      $status = $_POST['status'];
      $data_status = array('status' => $status );
      $mdl = new \App\Models\MdlClient();
      $mdl->set($data_status);
      $mdl->where('id',$id);
      $mdl->update();
      if ($mdl->affectedRows()>0) {
        $riwayat = "Mengubah status user id: $id dengan status $status ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
      }else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
      }

  }

  function employeeData(){

        $this->access('operator');
        $serverside_model = new \App\Models\Mdl_datatables_2();
        $request = \Config\Services::request();
        $list_data = $serverside_model;
        $where = ['pegawai_status !=' => 0];
                //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
                //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
        $column_order = array(NULL,'pegawai.pegawai_nama','pegawai.pegawai_nip','pegawai.pegawai_id');
        $column_search = array('pegawai.pegawai_nama','pegawai.pegawai_nip','pegawai.pegawai_id');
        $order = array('pegawai.pegawai_id' => 'desc');
        $list = $list_data->get_datatables('pegawai', $column_order, $column_search, $order, $where);
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
          $no++;
          $row    = array();
          $row[] = $no;
          $row[] = $lists->pegawai_id;
          $row[] = $lists->pegawai_nama;
          $row[] = $lists->pegawai_nip;
        
          $data[] = $row;
      }
      $output = array(
          "draw" => $request->getPost("draw"),
          "recordsTotal" => $list_data->count_all('pegawai', $where),
          "recordsFiltered" => $list_data->count_filtered('pegawai', $column_order, $column_search, $order, $where),
          "data" => $data,
      );
      return json_encode($output);

}
}
