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
                            SUM(DISTINCT production_progress.quantity) as qty_prod,
                            work_order_detail.quantity - COALESCE(SUM(DISTINCT production_progress.quantity), 0) as quantity, 
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
        // wo_id:wo_id, production_id:production_id,product_id:id_product, quantity: result.value.quantity
     $mdl = new \App\Models\MdlProductionProgress();
    if ($mdl->insert($_POST)) {
      $mdlStockMove = new \App\Models\MdlStockMove();
      $data['wo_id'] = $_POST['wo_id'];
      $data['product_id'] = $_POST['product_id'];
      $data['prod_id_tujuan'] = $_POST['production_id'];
      $data['stock_change'] = $_POST['quantity'];
      $mdlStockMove->insert($data);

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
        $data = $mdl->select('product.id as product_id, product.kode, product.nama, production_progress.id as id, production_progress.quantity as quantity, production_progress.wo_id as wo_id, work_order.kode as wo_code')->
                      join('product', 'product.id = production_progress.product_id')->
                      join('work_order', 'work_order.id = production_progress.wo_id')
                      ->where('production_progress.deleted_at', null)
                      ->where('production_progress.quantity >', 0)
                      ->where('production_id', $id)->get()->getResultArray();
        return json_encode($data);
      }
      function getWarehouseProduct($id) {
        $mdl = new \App\Models\MdlProductionProgress();
        $data = $mdl->select('product.id as product_id, product.kode, product.nama, production_progress.id as id, production_progress.quantity as quantity, production_progress.wo_id as wo_id, work_order.kode as wo_code')
               ->join('product', 'product.id = production_progress.product_id')
                     ->join('work_order', 'work_order.id = production_progress.wo_id')
                    ->where('warehouse_id', $id)
                    ->where('production_progress.deleted_at', null)
                    ->where('production_progress.quantity >', 0)
                    ->get()
                    ->getResultArray();
        return json_encode($data);
    }
    
      
       public function moveProduction()
    {
        $prodIdAwal = $this->request->getPost('prod_id_awal');  // ID produksi awal
        $prodIdTujuan = $this->request->getPost('prod_id');      // ID produksi tujuan
        $quantity = $this->request->getPost('quantity');         // Quantity yang dipindahkan
        $wo_id = $this->request->getPost('wo_id');         // Quantity yang dipindahkan
        $product_id = $this->request->getPost('product_id');         // Quantity yang dipindahkan

        // Validasi input
        if (empty($prodIdAwal) || empty($prodIdTujuan) || empty($quantity)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua kolom harus diisi.'
            ]);
        }

        // Cek apakah quantity adalah angka positif
        if (!is_numeric($quantity) || $quantity <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Quantity harus berupa angka positif.'
            ]);
        }

        // Load model
        $model =  new \App\Models\MdlProductionProgress();

        // Pindahkan quantity
        $result = $model->transferQuantity($prodIdAwal, $prodIdTujuan, $quantity);

        if ($result === true) {
          $mdlStockMove = new \App\Models\MdlStockMove();
      $dataMovement['wo_id'] = $wo_id;
      $dataMovement['product_id'] = $product_id;
      $dataMovement['prod_id_asal'] = $prodIdAwal;
      $dataMovement['prod_id_tujuan'] = $prodIdTujuan;
      $dataMovement['stock_change'] = $quantity;
      $mdlStockMove->insert($dataMovement);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Produk berhasil dipindahkan.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memindahkan produk.'
            ]);
        }
    }
           public function moveWarehouse()
    {
        $prodIdAwal = $this->request->getPost('prod_id_awal');  // ID produksi awal
        $prodIdTujuan = $this->request->getPost('prod_id');      // ID produksi tujuan
        $quantity = $this->request->getPost('quantity');         // Quantity yang dipindahkan
        $wo_id = $this->request->getPost('wo_id');         // Quantity yang dipindahkan
        $wh_id = $this->request->getPost('wh_id');         // Quantity yang dipindahkan
        $product_id = $this->request->getPost('product_id');         // Quantity yang dipindahkan
        
        // Validasi input
        if (empty($prodIdAwal) || empty($prodIdTujuan) || empty($quantity)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua kolom harus diisi.'
            ]);
        }

        // Cek apakah quantity adalah angka positif
        if (!is_numeric($quantity) || $quantity <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Quantity harus berupa angka positif.'
            ]);
        }

        // Load model
        $model =  new \App\Models\MdlProductionProgress();

        // Pindahkan quantity
        $result = $model->transferFinish($prodIdAwal, $prodIdTujuan, $quantity);

        if ($result === true) {
          $mdlStockMove = new \App\Models\MdlStockMove();
      $dataMovement['wo_id'] = $wo_id;
      $dataMovement['product_id'] = $product_id;
      $dataMovement['wh_id_asal'] = $wh_id;
      $dataMovement['wh_id_tujuan'] = $prodIdTujuan;
      $dataMovement['stock_change'] = $quantity;
      $mdlStockMove->insert($dataMovement);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Produk berhasil dipindahkan.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memindahkan produk.'
            ]);
        }
    }
    public function moveWarehouseFromProd()
    {
        $prodIdAwal = $this->request->getPost('prod_id_awal');  // ID produksi awal
        $prodIdTujuan = $this->request->getPost('prod_id');      // ID produksi tujuan
        $quantity = $this->request->getPost('quantity');         // Quantity yang dipindahkan
        $wo_id = $this->request->getPost('wo_id');         // Quantity yang dipindahkan
        $prod_awal = $this->request->getPost('prod_awal');         // Quantity yang dipindahkan
        $product_id = $this->request->getPost('product_id'); 
        // Validasi input
        if (empty($prodIdAwal) || empty($prodIdTujuan) || empty($quantity)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua kolom harus diisi.'
            ]);
        }

        // Cek apakah quantity adalah angka positif
        if (!is_numeric($quantity) || $quantity <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Quantity harus berupa angka positif.'
            ]);
        }

        // Load model
        $model =  new \App\Models\MdlProductionProgress();

        // Pindahkan quantity
        $result = $model->transferFinish($prodIdAwal, $prodIdTujuan, $quantity);

        if ($result === true) {
          $mdlStockMove = new \App\Models\MdlStockMove();
      $dataMovement['wo_id'] = $wo_id;
      $dataMovement['product_id'] = $product_id;
      $dataMovement['prod_id_asal'] = $prod_awal;
      $dataMovement['wh_id_tujuan'] = $prodIdTujuan;
      $dataMovement['stock_change'] = $quantity;
      $mdlStockMove->insert($dataMovement);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Produk berhasil dipindahkan.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memindahkan produk.'
            ]);
        }
    }
  }

 