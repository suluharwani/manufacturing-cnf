<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProductionController extends BaseController
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
       function update(){
      
        $param = $_POST['params'];
        $id = $param['id'];
        $data['name'] = $param['name'];
        $data['location'] = $param['location'];
        $mdl = new \App\Models\MdlProductionArea();
        
        $mdl->set($data);
        $mdl->where('id',$id);
        $mdl->update();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "update gudang {$data['name']}";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
    }
    function access($page){
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'],$page);
        }


      function create(){
        
        $userInfo = $_SESSION['auth'];
        $Mdl = new \App\Models\MdlProductionArea();
        $data = [
          "location" =>  $_POST["location"],
          "name" =>  $_POST["name"]
        ];
        if ($Mdl->insert($data)) {
          $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan client: ".$_POST['name']."sebagai type baru";
          header('HTTP/1.1 200 OK');
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan type: ".$_POST['name'];
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
      
    }
    function delete(){
        
        $param = $_POST['param'];
        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlProductionArea();
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus gudang $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
       function purgeData(){
        
        $param = $_POST['param'];
        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlProductionArea();
        $mdl->where('id',$id);
        $mdl->purgeDeleted();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus permanen gudang $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
       function restoreData(){
        
        $param = $_POST['param'];
        $id = $param['id'];
        $name = $param['name'];
        $mdl = new \App\Models\MdlProductionArea();
        $mdl->set('deleted_at',null);
        $mdl->where('id',$id);
        $mdl->update();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "Menghapus gudang $name";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
       } 
       function deletedData(){
        
        $mdl = new \App\Models\MdlProductionArea();
        $data = $mdl->onlyDeleted()->findAll();
        return json_encode($data);
       }
       function gudang_list(){
        
        $mdl = new \App\Models\MdlProductionArea();
        $data = $mdl->findAll();
        return json_encode($data);
       }
}
 