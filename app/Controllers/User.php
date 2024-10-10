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
  $column_order = array(NULL,'pegawai.pegawai_nip','pegawai.pegawai_nama','pegawai.pegawai_pin','pegawai.pegawai_id');
  $column_search = array('pegawai.pegawai_nip','pegawai.pegawai_nama','pegawai.pegawai_pin','pegawai.pegawai_id');
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
    $row[] = $lists->pegawai_pin;
    $row[] = $lists->tgl_masuk_pertama;

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
public function getPresensi()
{
  $pin = $this->request->getPost('pin');
  $id = $this->request->getPost('id');
  $startDate = $this->request->getPost('startDate');
  $endDate = $this->request->getPost('endDate');

    // Load model and fetch data
  $model = new \App\Models\AttendanceModel();
    $data = $model->getAttendanceData($pin, $id, $startDate, $endDate); // Implement this method in your model

    return $this->response->setJSON(['data' => $data]);
  }
  public function updateAttendance(){


    $this->access('operator');
    // $pin = $this->request->getPost('pin');
    
    $params = $this->request->getPost('params');
    $date = $params['date'];
    $time = $params['time'];
    $pin = $params['pin'];

    $mergeTime = new \DateTime("$date $time");
    $dateTime = $mergeTime->format('Y-m-d H:i:s');
    $userInfo = $_SESSION['auth'];
    $Mdl = new \App\Models\AttendanceModel();
    $data = [
      "sn"=>"Admin manual add",
      "scan_date"=>$dateTime,
      "pin"=>$pin,
      "verifymode"=>1,
      "inoutmode"=>1,
      "reserved"=>0,
      "work_code"=>0,
      "att_id"=>0,

    ];
    if ($Mdl->insert($data)==0) {
      $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan presensi untuk pin: ".$pin."untuk waktu $dateTime";
      header('HTTP/1.1 200 OK');
    }else{

      $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan jam kerja";
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      die(json_encode(array('message' => 'Gagal menambahkan data.', 'code' => 3)));
    }
  }
        function addEffectiveHours(){
        $this->access('operator');
        $userInfo = $_SESSION['auth'];
        $Mdl = new \App\Models\MdlEffectiveHours();
        $params = $this->request->getPost();

        if ($Mdl->insert($params)) {
          $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan hari kerja ". $params['day']."";
          header('HTTP/1.1 200 OK');
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan hari kerja";
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
      
    }
public function getDataWorkDay()
    {
        $model = new \App\Models\MdlEffectiveHours();
        $data = $model->findAll();

        return $this->response->setJSON($data);
    }

    
  public function deleteWorkDay(){
     $id = $this->request->getPost('id');
    $model =new \App\Models\MdlEffectiveHours();

    if ($model->delete($id)) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data.']);
    }
  }
   public function addSalaryCat()
    {
        $data = [
            'Kode' => $this->request->getPost('Kode'),
            'Nama' => $this->request->getPost('Nama'),
            'Kategori' => $this->request->getPost('Kategori'),
            'Gaji_Pokok' => $this->request->getPost('Gaji_Pokok'),
            'Gaji_Per_Jam' => $this->request->getPost('Gaji_Per_Jam'),
            'Gaji_Per_Jam_Hari_Minggu' => $this->request->getPost('Gaji_Per_Jam_Hari_Minggu')
        ];

        $model =new \App\Models\MdlSalaryCat();

        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the record.'], 400);
        }
    }
  public function getSalaryCat()
    {
        $model = new \App\Models\MdlSalaryCat();
        $data = $model->findAll();

        return $this->response->setJSON($data);
    }

      public function deleteSalaryCat(){
     $id = $this->request->getPost('id');
    $model =new \App\Models\MdlSalaryCat();

    if ($model->delete($id)) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data.']);
    }
  }
  public function getAllowanceData()
    {
        $model = new \App\Models\AllowanceModel();
        $data = $model->findAll(); // Fetch all allowance data from the table
        return $this->response->setJSON($data); // Return as JSON
    }

    // Load all deduction data
    public function getDeductionData()
    {
        $model = new \App\Models\DeductionModel();
        $data = $model->findAll(); // Fetch all deduction data from the table
        return $this->response->setJSON($data); // Return as JSON
    }

    // Delete allowance by Kode
    public function deleteAllowance()
    {
        $id = $this->request->getPost('id');
        $model = new \App\Models\AllowanceModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete the allowance.'], 400);
        }
    }

    // Delete deduction by Kode
    public function deleteDeduction()
    {
        $id = $this->request->getPost('id');
        $model = new \App\Models\DeductionModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete the deduction.'], 400);
        }
    }

    // Add new allowance
    public function addAllowance()
    {
        $data = [
            'Kode' => $this->request->getPost('Kode'),
            'Nama' => $this->request->getPost('Nama'),
            'Status' => $this->request->getPost('Status')
        ];

        $model = new \App\Models\AllowanceModel();
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the allowance.'], 400);
        }
    }

    // Add new deduction
    public function addDeduction()
    {
        $data = [
            'Kode' => $this->request->getPost('Kode'),
            'Nama' => $this->request->getPost('Nama'),
            'Status' => $this->request->getPost('Status')
        ];

        $model = new \App\Models\DeductionModel();
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the deduction.'], 400);
        }
    }
    public function getSalarySetting()
{
    $pin = $this->request->getPost('pin');
    $id = $this->request->getPost('id');

    // Fetch salary settings based on pin or id (replace with your actual logic)
    $salarySettingsModel = new \App\Models\SalarySettingsModel();
    $salarySettings = $salarySettingsModel->where('pin', $pin)->findAll();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $salarySettings,
    ]);
}


}
