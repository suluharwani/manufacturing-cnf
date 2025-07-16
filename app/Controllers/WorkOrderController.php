<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MdlWorkOrder;
use App\Models\MdlWorkOrderDetail;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 
use Dompdf\Dompdf;
use Dompdf\Options;
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
  public function listdataWorkOrder(){
    
           $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'work_order.*, proforma_invoice.invoice_number, proforma_invoice.status';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
                 ['proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left'],
           ];
   
           $where = ['work_order.id !=' => 0, 'work_order.deleted_at' => NULL,];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'proforma_invoice.invoice_number', 
               'work_order.kode', 
               'work_order.id',
               'work_order.start',
               'work_order.end',
               'work_order.id',
           );
           $column_search = array(
               'work_order.nama', 
               'work_order.kode', 
          
           );
           $order = array('work_order.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('work_order', $select_columns, $joins, $column_order, $column_search, $order, $where);
           
           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
            if ($lists->status == null) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->id;
               $row[] = $lists->invoice_id;
               $row[] = $lists->invoice_number;
               $row[] = $lists->kode;
               $row[] = $lists->start;
               $row[] = $lists->end;
               $row[] = $lists->status;

 // From joined suppliers table
               $data[] = $row;
            }
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('work_order', $where),
               "recordsFiltered" => $serverside_model->count_filtered('work_order', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
          

           return $this->response->setJSON($output);
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
    public function add(){
        $userInfo = $_SESSION['auth'];

        $Mdl = new \App\Models\MdlWorkOrder();

              if ($Mdl->insert($_POST)) {
        $riwayat = "User ".$userInfo['nama_depan']." menambahkan wo: ".$_POST['kode'];
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


        public function wo($id)
    {
         $Mdl = new \App\Models\MdlWorkOrder();
         $MdlDetail = new \App\Models\MdlWorkOrderDetail();
$dataPembelian = $Mdl
                ->select('proforma_invoice.*, work_order.id as id_wo, customer.id_currency as curr_id, currency.kode as curr_code, currency.nama as curr_name, customer.customer_name, work_order.kode, work_order.start, work_order.end, work_order.release_date, work_order.manufacture_finishes, work_order.loading_date')
                ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id','left')    
                ->join('customer', 'customer.id = proforma_invoice.customer_id','left')    
                ->join('currency', 'currency.id = customer.id_currency','left')    
                ->where('work_order.id', $id)->get()->getResultArray();
// $dataPembelianDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materials","materials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataPembelianDetail);
// die();
        $data['wo'] = $dataPembelian;

        // var_dump($data['pi']);
        // die();
        $data['content'] = view('admin/content/form_wo',$data);
        return view('admin/index', $data);
    }
    public function listdataWo($id){
         $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'work_order_details.*,product.nama as nama, product.kode as kode,product.id as id_product';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
                 ['product', 'product.id = work_order_details.id_product', 'left'],
           ];
   
           $where = ['work_order_details.invoice_id ' => $id, 'work_order_details.deleted_at' => NULL];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'product.nama', 
               'product.kode', 
               'id_product',
               'id_product',
               'id_product',
               'id_product',
               'id_product',
           );
           $column_search = array(
               'product.nama', 
               'product.kode', 
          
           );
           $order = array('work_order_details.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('work_order_details', $select_columns, $joins, $column_order, $column_search, $order, $where);

           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->id_product;
               $row[] = $lists->nama;
               $row[] = $lists->kode;
               $row[] = $lists->quantity;

 // From joined suppliers table
               $data[] = $row;
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('work_order_details', $where),
               "recordsFiltered" => $serverside_model->count_filtered('work_order_details', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
          

           return $this->response->setJSON($output);
    }
function getPi($id_wo)
{
    // Load models
    $MdlWo = new \App\Models\MdlWorkOrder();
    $MdlWoDetail = new \App\Models\MdlWorkOrderDetail();
    $MdlPiDetail = new \App\Models\ProformaInvoiceDetail();

    // Get invoice_id based on work order id
    $id_pi = $MdlWo->where('id', $id_wo)->get()->getResultArray()[0]['invoice_id'];

    // Query to get the proforma invoice details and calculate total quantity in work_order_detail
    $data = $MdlPiDetail->select('proforma_invoice_details.*, 
                                   COALESCE(SUM(work_order_detail.quantity), 0) as qty_wo, 
                                   (proforma_invoice_details.quantity - COALESCE(SUM(work_order_detail.quantity), 0)) as qty_tersedia, 
                                   product.*, product.id as id_product')
                        ->join('work_order_detail', 'work_order_detail.product_id = proforma_invoice_details.id_product', 'left')
                        ->join('product', 'product.id = proforma_invoice_details.id_product', 'left')
                        ->where('proforma_invoice_details.invoice_id', $id_pi) // Filter by invoice_id from proforma_invoice_details
                        // ->where('work_order_detail.wo_id', $id_wo) // Filter by invoice_id from proforma_invoice_details
                        ->groupBy('proforma_invoice_details.id_product') // Group by product_id from proforma_invoice_details
                        ->get()
                        ->getResultArray();

    return json_encode($data);
}

public function addDetail()
{
    // Ambil data dari POST request
    $data = $this->request->getPost();

    // Model untuk work order detail
    $MdlWoDetail = new \App\Models\MdlWorkOrderDetail();

    // Cek apakah sudah ada data dengan id_wo dan id_product yang sama
    $existingData = $MdlWoDetail->where('wo_id', $data['wo_id'])
                                ->where('product_id', $data['product_id'])
                                ->first();

    if ($existingData) {
        // Jika data sudah ada, lakukan update quantity
        $newQuantity = $existingData['quantity'] + $data['quantity'];  // Tambah quantity yang baru
        $MdlWoDetail->update($existingData['id'], ['quantity' => $newQuantity]);
        
        // Mengirimkan response sukses
        return $this->response->setJSON(['status' => 'success', 'message' => 'Quantity updated successfully.']);
    } else {
        // Jika data belum ada, lakukan insert
        if ($MdlWoDetail->insert($data)) {
            // Mengirimkan response sukses setelah insert
            return $this->response->setJSON(['status' => 'success', 'message' => 'Product added successfully.']);
        } else {
            // Mengirimkan response error jika insert gagal
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add product.']);
        }
    }
}
function getWo($id_wo){
           $MdlWo = new \App\Models\MdlWorkOrder();

         $MdlWoDetail = new \App\Models\MdlWorkOrderDetail();
         $MdlPiDetail = new \App\Models\ProformaInvoiceDetail();

    $data = $MdlWoDetail->select('* ,work_order_detail.id as id_det')
                        ->join('product', 'product.id = work_order_detail.product_id', 'left')
                        ->where('work_order_detail.wo_id', $id_wo)
                        ->get()
                        ->getResultArray();
         return json_encode($data);
}

    public function delete($id)
    {
        $model = new \App\Models\MdlWorkOrderDetail();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }
    function woList(){
        $mdl = new \App\Models\MdlWorkOrder();
         $data =  $mdl->orderBy('id','desc')->get()->getResultArray();
         return $this->response->setJSON($data);

    }
    public function print($id)
{
    $mdl = new MdlWorkOrder();

    $data = $mdl->getPrintData($id);

    // Load the view and pass the data
    $html = view('admin/content/printWO', $data);

    // Initialize Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Load HTML content
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream("WO_{$data['invoice']['kode']}.pdf", ["Attachment" => false]);
}
public function updateDate($id){
    $mdl = new MdlWorkOrder();
    $mdl->set($_POST);
    $mdl->where('id',$id);
    $mdl->update();
    if ($mdl->affectedRows()!=0) {
      $riwayat = "update wo date";
      $this->changelog->riwayat($riwayat);
      header('HTTP/1.1 200 OK');
    }else {
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
    }
}
   public function deleteWO($id)
{
    $mdlWorkOrder = new MdlWorkOrder();
    $mdlWorkOrderDetail = new MdlWorkOrderDetail();
    
    // Start transaction to ensure both operations succeed or fail together
    $this->db->transStart();
    
    // Delete the Work Order (soft delete)
    $resultWO = $mdlWorkOrder->delete($id);
    
    // Delete related Work Order Details (soft delete)
    $resultDetails = $mdlWorkOrderDetail->where('wo_id', $id)->delete();
    
    $this->db->transComplete();
    
    if ($this->db->transStatus() === false) {
        $response = [
            'success' => false,
            'message' => 'Gagal menghapus Work Order dan detailnya'
        ];
    } else {
        $response = [
            'success' => true,
            'message' => 'Work Order dan detailnya berhasil dihapus'
        ];
    }
    
    return $this->response->setJSON($response);
}
}
