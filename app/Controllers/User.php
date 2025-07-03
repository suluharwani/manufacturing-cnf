<?php
namespace App\Controllers;
use AllowDynamicProperties; 
use CodeIgniter\Controller;
use Bcrypt\Bcrypt;
use App\Models\AttendanceModel;
use CodeIgniter\API\ResponseTrait;
class User extends BaseController
{
  use ResponseTrait;
  protected $bcrypt;
  protected $userValidation;
  protected $bcrypt_version;
  protected $session;
  protected $db;
  protected $uri;
  protected $form_validation;
  protected $changelog;
  protected $attendanceModel;
  public function __construct()
  {
    $this->attendanceModel = new AttendanceModel();
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
    $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan user: ".$_POST['email']."sebagai Admin";
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

  
  $serverside_model = new \App\Models\Mdl_datatables_2();
  $request = \Config\Services::request();
  $list_data = $serverside_model;
  $where = ['pegawai_status !=' => 0];
                //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
                //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
  $column_order = array(NULL,'pegawai.pegawai_nip','pegawai.pegawai_nama','pegawai.pegawai_pin','pegawai.pegawai_id');
  $column_search = array('pegawai.pegawai_pin','pegawai.pegawai_nama','pegawai.pegawai_id');
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
    if ($Mdl->insert($data)) {
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
    $salaryModel = new \App\Models\MdlSalaryCat();
    $salaryCategories = $salaryModel->findAll();

    if ($salaryCategories) {
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $salaryCategories,
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No salary categories found.',
        ]);
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
    public function deleteSalaryCat()
    {
        $id = $this->request->getPost('id');
        $model = new \App\Models\MdlSalaryCat();
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete salary category.'], 400);
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
            'employee_id' => $this->request->getPost('employeeId'),
            'allowance_id' => $this->request->getPost('allowanceId'),
            'amount' => $this->request->getPost('amount')
        ];

        $model = new \App\Models\MdlFemployeeAllowanceList();
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the allowance.'], 400);
        }
    }

    // Add new deduction
    public function addDeductionList()
    {
       $data = [
            'employee_id' => $this->request->getPost('employeeId'),
            'deduction_id' => $this->request->getPost('deductionId'),
            'amount' => $this->request->getPost('amount')
        ];

        $model = new \App\Models\MdlFemployeeDeductionList();
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the allowance.'], 400);
        }
    }
//     public function getSalarySetting()
// { 
//     $pin = $this->request->getPost('pin');
//     $id = $this->request->getPost('id');

//     // Fetch salary settings based on pin or id (replace with your actual logic)
//     $salarySettingsModel = new \App\Models\SalarySettingsModel();
//     $salarySettings = $salarySettingsModel->where('pin', $pin)->findAll();

//     return $this->response->setJSON([
//         'status' => 'success',
//         'data' => $salarySettings,
//     ]);
// }
public function saveSalarySettings()
{
    $items = $this->request->getPost('items');

    foreach ($items as $item) {
        // Save each selected item with its nominal value (insert/update to database)
        $data = [
            'kode' => $item['kode'],
            'nominal' => $item['nominal'],
            // Add other necessary fields here
        ];

        // Save logic (update or insert into your table)
        // Example: $this->salaryModel->save($data);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Data berhasil disimpan'
    ]);
}
public function saveSalaryCategory()
{
    $pin = $this->request->getPost('pin');
    $id = $this->request->getPost('id');  // Employee ID
    $salaryCategoryId = $this->request->getPost('salaryCategoryId');  // Salary Category ID

    // Load the salary model
    $salaryModel = new \App\Models\MdlFsalaryPatternEmployee();

    // Check if there is an existing record for this employee
    $existingRecord = $salaryModel->where('id_employee', $id)
                                  ->first();

    // Prepare the data
    $data = [
        'id_employee' => $id,
        'id_salary_pattern' => $salaryCategoryId,
    ];

    // If record exists, update it
    if ($existingRecord) {
        $data['id'] = $existingRecord['id'];  // Set the primary key to update the record

        if ($salaryModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Salary category updated successfully.',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update salary category.',
            ]);
        }
    } else {
        // Insert new record if no existing record found
        if ($salaryModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Salary category inserted successfully.',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to insert salary category.',
            ]);
        }
    }
}

public function getEmployeeNameByPin()
{
    // Get the pin from the AJAX request
    $pin = $this->request->getPost('pin');
    $mdl = new \App\Models\MdlEmployee();
    // Find the employee by the pin
    $employee = $mdl->where('pegawai_pin', $pin)->first();

    if ($employee) {
        // Return the employee's name as a JSON response
        return $this->response->setJSON([
            'success' => true,
            'name' => $employee['pegawai_nama'] // assuming 'name' is the column for the employee's name
        ]);
    } else {
        // If no employee is found
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Employee not found'
        ]);
    }
}

public function getSalarySetting()
{
    $id_employee = $this->request->getPost('id');

    // Load your models
    $salaryPatternModel = new \App\Models\MdlFsalaryPatternEmployee();
    $salarySettingsModel = new \App\Models\SalarySettingsModel();

    // Get all salary patterns for the specific employee
    $existingPatterns = $salaryPatternModel
        ->select('id_salary_pattern')
        ->where('id_employee', $id_employee)
        ->findAll();

    // Convert the results to an array of `id_salary_pattern`
    $existingPatternIds = array_column($existingPatterns, 'id_salary_pattern');

    // Fetch all salary patterns with a join to employeesallarycat
    $allPatterns =$salarySettingsModel
    ->select('employeesallarycat.id, employeesallarycat.Nama as nama, employeesallarycat.Kode as kode, employeesallarycat.Kategori, employeesallarycat.Gaji_Pokok, employeesallarycat.Gaji_Per_Jam as perjam,  employeesallarycat.Gaji_Per_Jam_Hari_Minggu')
    ->from('salary_pattern_employee')  // Base table is salary_pattern_employee
    ->join('employeesallarycat', 'salary_pattern_employee.id_salary_pattern = employeesallarycat.id')  // Join employeesalarycat
    ->where('salary_pattern_employee.id_employee', $id_employee)
    ->findAll();

    // Send the data back as JSON
    return $this->response->setJSON([
        'existingPatterns' => $existingPatternIds,
        'allPatterns' => $allPatterns,
    ]);
}
public function getAvailableItems()
{
    // Get the POST data (pin and id)
    $pin = $this->request->getPost('pin');
    $id = $this->request->getPost('id');

    // Load your model that handles fetching the available items
    $availableItemsModel = new \App\Models\AvailableItemsModel();

    // Fetch the available items for the given employee (pin or id)
    // You can modify the query logic based on your actual schema
    $availableItems = $availableItemsModel
        ->select('employeesallarycat.Nama, employeesallarycat.Kode, employeesallarycat.Gaji_Per_Jam')
        ->join('salary_pattern_employee', 'salary_pattern_employee.id_salary_pattern = employeesallarycat.id')
        ->where('salary_pattern_employee.id_employee', $id)
        ->findAll();

    // Check if any items were found
    if (!empty($availableItems)) {
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $availableItems
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No items found for the provided employee.'
        ]);
    }
}

  public function fetchAllowances()
    {

        $allowanceModel = new \App\Models\MdlFsalaryAllowance();
        $data = $allowanceModel->findAll();
        return $this->response->setJSON($data);
    }
public function getAllowanceOptions()
{
    $allowanceModel = new \App\Models\MdlFsalaryAllowance();

    // Fetch all allowances from the database
    $allowances = $allowanceModel->findAll();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $allowances
    ]);
}
    // Save a new allowance entry
    public function addDeduction()
    {
        $data = [
            'employee_id' => $this->request->getPost('employeeId'),
            'allowance_id' => $this->request->getPost('allowanceId'),
            'amount' => $this->request->getPost('amount')
        ];

        $model = new \App\Models\MdlFemployeeAllowanceList();
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add the allowance.'], 400);
        }
    }

    public function saveAllowance()
    {

       $allowanceModel = new \App\Models\MdlFemployeeAllowanceList();
        $allowanceData = $this->request->getPost();
        $allowanceModel->save($allowanceData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Allowance saved successfully!']);
    }

    public function getEmployeeAllowances($employeeId = null)
    {
       $Mdl = new \App\Models\MdlFemployeeAllowanceList();
         $Mdl->select('employee_allowance_list.*, salary_allowance.Nama as allowance_name, salary_allowance.Kode as allowance_code')
            ->join('salary_allowance', 'employee_allowance_list.allowance_id = salary_allowance.id', 'left');

        if ($employeeId) {
             $Mdl->where('employee_allowance_list.employee_id', $employeeId);
        }

        return  json_encode($Mdl->findAll());
    }
    public function deleteAllowanceList($id=null)
    {
        // $id = $this->request->getPost('id');
        $model = new \App\Models\MdlFemployeeAllowanceList();
        if ($model->where('id',$id)->delete()) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete the allowance.'], 400);
        }
    }




// Deduction
 public function fetchDeductions()
    {

        $allowanceModel = new \App\Models\MdlFsalaryAllowance();
        $data = $allowanceModel->findAll();
        return $this->response->setJSON($data);
    }
public function getDeductionOptions()
{
    $allowanceModel = new \App\Models\MdlFsalaryDeduction();

    // Fetch all allowances from the database
    $allowances = $allowanceModel->findAll();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $allowances
    ]);
}
    // Save a new allowance entry
    public function saveDeduction()
    {

       $deductionModel = new \App\Models\MdlFemployeeDeductionList();
        $deductionData = $this->request->getPost();
        $deductionModel->save($deductionData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Allowance saved successfully!']);
    }

    public function getEmployeeDeductions($employeeId = null)
    {
       $Mdl = new \App\Models\MdlFemployeeDeductionList();
         $Mdl->select('employee_deduction_list.*, salary_deduction.Nama as deduction_name, salary_deduction.Kode as deduction_code')
            ->join('salary_deduction', 'employee_deduction_list.deduction_id = salary_deduction.id', 'left');

        if ($employeeId) {
             $Mdl->where('employee_deduction_list.employee_id', $employeeId);
        }

        return  json_encode($Mdl->findAll());
    }
    public function deleteDeductionList($id=null)
    {
        // $id = $this->request->getPost('id');
        $model = new \App\Models\MdlFemployeeDeductionList();
        if ($model->where('id',$id)->delete()) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete the allowance.'], 400);
        }
    }
 public function addAttendance()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'pin' => 'required|max_length[32]',
            'date' => 'required|valid_date',
            'clockIn' => 'required',
            'clockOut' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $pin = $this->request->getPost('pin');
        $date = $this->request->getPost('date');
        $clockIn = $this->request->getPost('clockIn');
        $clockOut = $this->request->getPost('clockOut');

        // Prepare data for clock in
        $clockInData = [
            'sn' => 'manual',
            'scan_date' => "$date $clockIn",
            'pin' => $pin,
            'verifymode' => 0,
            'inoutmode' => 0,
            'att_id' => 0
        ];

        // Prepare data for clock out
        $clockOutData = [
            'sn' => 'manual',
            'scan_date' => "$date $clockOut",
            'pin' => $pin,
            'verifymode' => 0,
            'inoutmode' => 1,
            'att_id' => 0
        ];

        try {
            $this->attendanceModel->insert($clockInData);
            $this->attendanceModel->insert($clockOutData);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Attendance records added successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding attendance: ' . $e->getMessage());
            return $this->failServerError('Failed to add attendance records');
        }
    }

    /**
     * Delete attendance record from att_log table
     */
    public function deleteAttendance()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'pin' => 'required|max_length[32]',
            'date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $pin = $this->request->getPost('pin');
        $date = $this->request->getPost('date');

        try {
            $result = $this->attendanceModel
                ->where('pin', $pin)
                ->where('DATE(scan_date)', $date)
                ->delete();

            if ($result) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Attendance records deleted successfully',
                    'deleted_rows' => $result
                ]);
            }

            return $this->failNotFound('No manual attendance records found for this date');
            
        } catch (\Exception $e) {
            log_message('error', 'Error deleting attendance: ' . $e->getMessage());
            return $this->failServerError('Failed to delete attendance records');
        }
    }

}