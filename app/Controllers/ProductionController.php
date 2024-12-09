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

       function warehouseList(){
        
        $mdl = new \App\Models\Warehouse();
        $data = $mdl->findAll();
        return json_encode($data);
       }
       function productionList(){
        
        $mdl = new \App\Models\MdlProductionArea();
        $data = $mdl->findAll();
        return json_encode($data);
       }
        function getWOList(){
        
        $mdl = new \App\Models\MdlWorkOrder();
        $data = $mdl->get()->getResultArray();
        return json_encode($data);
       }

       function addWo(){
        $userInfo = $_SESSION['auth'];

        $Mdl = new \App\Models\MdlProductionWO();

              if ($Mdl->insert($_POST)) {
        $riwayat = "User ".$userInfo['nama_depan']." menambahkan wo: ke produksi ".$_POST['wo_id']."ke ".$_POST['nama'];
        $this->changelog->riwayat($riwayat);  
            header('HTTP/1.1 200 OK');
        
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan wo: ".$_POST['kode'];
          $this->changelog->riwayat($riwayat);

          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
       }
function getWOProduction($id){
        
        $mdl = new \App\Models\MdlProductionWO();
        $data = $mdl->select('proforma_invoice.invoice_number as pi, production_wo.id as id, work_order.kode as kode')
        ->join('work_order', 'work_order.id = production_wo.wo_id ')
        ->join('proforma_invoice', 'work_order.invoice_id = proforma_invoice.id ')
        ->where('production_wo.production_id',$id)->get()->getResultArray();
        return json_encode($data);
       }
function getProductByWO($id_production)
{
    $mdl = new \App\Models\MdlProductionWO();
    
    // Selecting required fields and calculating the remaining quantity
    $data = $mdl->select('
                            work_order.kode as wo,
                            proforma_invoice.invoice_number,
                            product.kode,
                            product.nama,
                            work_order_detail.quantity as qty_wo,
                            sum(production_progress.quantity) as qty_prod,
                            work_order_detail.quantity - COALESCE(SUM(production_progress.quantity), 0) as quantity, 
                            product.id as id_product,
                            production_wo.production_id as production_id,
                            production_wo.wo_id as wo_id,
                            proforma_invoice.id as pi_id
                        ')
                ->join('work_order', 'work_order.id = production_wo.wo_id')
                ->join('proforma_invoice', 'work_order.invoice_id = proforma_invoice.id')
                ->join('work_order_detail', 'work_order_detail.wo_id = work_order.id')
                ->join('product', 'work_order_detail.product_id = product.id')
                ->join('production_progress', 'work_order_detail.wo_id = production_progress.wo_id', 'left') // Join with production_progress to get quantity data
                ->where('production_wo.production_id', $id_production)
                ->groupBy([
                    'work_order.kode',
                    'proforma_invoice.invoice_number',
                    'product.kode',
                    'product.nama',
                    'product.id',
                    'production_wo.production_id',
                    'production_wo.wo_id',
                    'proforma_invoice.id'
                ])  // Grouping to avoid duplicates
                ->get()
                ->getResultArray();

    return json_encode($data);
}


  function addProgress(){
        $userInfo = $_SESSION['auth'];

     $mdl = new \App\Models\MdlProductionProgress();
    if ($mdl->insert($_POST)) {
        $riwayat = "User ".$userInfo['nama_depan']." menambahkan  produksi ke produksi ".$_POST['production_id']."dari WO ".$_POST['wo_id']." produk ".$_POST['product_id'];
        $this->changelog->riwayat($riwayat);  
            header('HTTP/1.1 200 OK');
        
        }else{
          // $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan wo: ".$_POST['kode'];
          // $this->changelog->riwayat($riwayat);

          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }

      }
      function getProductionProduct($id){
        $mdl = new \App\Models\MdlProductionProgress();
        $data = $mdl->select('product.kode, product.nama, production_progress.id as id, production_progress.quantity as quantity')->join('product', 'product.id = production_progress.product_id')->where('production_id',$id)->get()->getResultArray();
        return json_encode($data);
      }
  }

 