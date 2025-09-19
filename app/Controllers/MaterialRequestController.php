<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties;
use Dompdf\Dompdf;
use Dompdf\Options;
class MaterialRequestController extends BaseController
{
    protected $changelog;
        public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = session();
        helper(['form', 'url']);
        $this->form_validation = \Config\Services::validation();
        $this->changelog = new \App\Controllers\Changelog();

        // Check if session is active
        $check = new \App\Controllers\CheckAccess();
        $check->logged();
    }
  
    public function materialRequest()
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();
        
        // Define the columns to select
        $select_columns = 'material_request.id,material_request.status, material_request.kode,material_request.created_at, material_request.remarks, material_request.created_at, proforma_invoice.invoice_number as pi';
        
        // Define the joins (you can add more joins as needed)
        $joins = [
            ['proforma_invoice', 'proforma_invoice.id = material_request.id_pi', 'left'],


        ];

        $where = ['material_request.id !=' => 0, 'material_request.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL, 
            'material_request.created_at',
            'material_request.kode',
            'proforma_invoice.invoice_number',
            'material_request.remarks',
            'material_request.status',
            'material_request.id',

        );
        $column_search = array(
            'material_request.kode', 
            'material_request.pi', 
        );
        $order = array('material_request.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('material_request', $select_columns, $joins, $column_order, $column_search, $order, $where);
        
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id;
            $row[] = $lists->created_at;
            $row[] = $lists->kode;
            $row[] = $lists->pi;
            $row[] = $lists->remarks;
            $row[] = $lists->status;
// From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('material_request', $where),
            "recordsFiltered" => $serverside_model->count_filtered('material_request', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );

      //   return $this->response->setJSON($output);
      
        return json_encode($output);
    }
    public function mr($id){
        $mdl = new \App\Models\MdlMaterialRequest();
        
        $data['mr'] = $mdl->select('material_request.*,proforma_invoice.invoice_number as pi')
                    ->join('proforma_invoice', 'proforma_invoice.id = material_request.id_pi', 'left')
                        ->where('material_request.id', $id)->first(); 
        $data['content'] = view('admin/content/form_mr', $data);
        return view('admin/index', data: $data);
    }
    public function add(){
        $mdl = new \App\Models\MdlMaterialRequest();
        $mdl->insert($_POST);
        if ($mdl->affectedRows() !== 0) {
     $riwayat = "Menambahkan Material Request ";
     $this->changelog->riwayat($riwayat);
     header('HTTP/1.1 200 OK');
 } else {
     header('HTTP/1.1 500 Internal Server Error');
     header('Content-Type: application/json; charset=UTF-8');
     die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
 }
    }
    function datamr($id_mr){
        $mdl = new \App\Models\MdlMaterialRequestList();
        $data = $mdl->select('stock.*,material_request.status, material_request_list.*, materials.name as material,materials.kode as code, proforma_invoice.invoice_number as pi, department.name as dep, satuan.kode as satuan, satuan.nama as nama_satuan, materials_detail.kite as kite,    materials_detail.hscode as hs_code')
                    ->join('materials', 'materials.id = material_request_list.id_material', 'left')
                    ->join('materials_detail', 'materials.id = materials_detail.material_id', 'left')
                    ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
                    ->join('proforma_invoice', 'proforma_invoice.id = material_request_list.id_pi', 'left')
                    ->join('department', 'department.id = material_request_list.id_dept', 'left')
                    ->join('material_request', 'material_request_list.id_mr = material_request.id', 'left')
                    ->join('stock', 'materials.id = stock.id_material', 'left')
                    ->where('id_mr', $id_mr)->findAll();
        return json_encode($data);

    }
    public function addMR(){
            $mdl = new \App\Models\MdlMaterialRequestList();
            $mdl->insert($_POST);
            if ($mdl->affectedRows() !== 0) {
         $riwayat = "Menambahkan Material Request list";
         $this->changelog->riwayat($riwayat);
         header('HTTP/1.1 200 OK');
     } else {
         header('HTTP/1.1 500 Internal Server Error');
         header('Content-Type: application/json; charset=UTF-8');
         die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
     }

    }
    public function deleteList($id){
        $mdl = new \App\Models\MdlMaterialRequestList();
        $mdl->delete($id);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Menghapus Material Request List';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
            }
    }
    public function getMR($id) {
        $mdl = new \App\Models\MdlMaterialRequestList();
        
        // Fetch the material request data along with the proforma invoice
        $data = $mdl->select('material_request_list.*,supplier.supplier_name as supplier, materials.name as material,materials.kode as code, proforma_invoice.invoice_number as pi, department.name as dep')
        ->join('materials', 'materials.id = material_request_list.id_material', 'left')
        ->join('proforma_invoice', 'proforma_invoice.id = material_request_list.id_pi', 'left')
        ->join('department', 'department.id = material_request_list.id_dept', 'left')
        ->join('supplier', 'supplier.id = material_request_list.id_sup', 'left')
        ->where('material_request_list.id', $id)->first();
        
        // Check if data is found
        if ($data) {
            return json_encode($data);
        } else {
            // Return an error message if no data found
            return json_encode(['message' => 'Material Request not found', 'code' => 404]);
        }
    }
    public function importPi(){
        $mdl = new \App\Models\MdlMaterialRequestList();
        $mdl->importMaterialUsage($_POST['idMR'], $_POST['idPI'], $_POST['idDept']);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Import Material Request List MR: {$_POST['idMR']},PI: {$_POST['idPI']}";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function deleteAll(){
        $mdl = new \App\Models\MdlMaterialRequestList();
        $mdl->where('id_mr', $_POST['idMR'])->delete();
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Menghapus Semua Material Request List';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function posting(){
        $mdl = new \App\Models\MdlMaterialRequest();
        $mdl->update($_POST['idMR'], ['status' => 1]);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Mengubah status Material Request ke posting";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function batalPosting(){
        $mdl = new \App\Models\MdlMaterialRequest();
        $mdl->update($_POST['idMR'], ['status' => 0]);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Mengubah status Material Request ke batal posting";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function updateQty(){
        $mdl = new \App\Models\MdlMaterialRequestList();
        $params =$_POST['params'];
        $id = $params['id'];
        $quantity = $params['quantity'];
        $mdl->update($id,  ['quantity' => $quantity]);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Mengubah quantity Material Request List id: {$id} kuantitas: {$quantity}";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message'=> 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function printPR($id){
        // Konfigurasi opsi Dompdf
        $mdl = new \App\Models\MdlMaterialRequest();
        $data['pr'] = $mdl->select('department.name as department, material_request.*,proforma_invoice.invoice_number as pi, work_order.kode as wo')
                    ->join('proforma_invoice', 'proforma_invoice.id = material_request.id_pi', 'left')
                    ->join('work_order', 'work_order.id = material_request.id_wo', 'left')
                    ->join('department', 'material_request.dept_id = department.id', 'left')
                        ->where('material_request.id', $id)->first();
        $data['prDet'] = $this->datamr($id);
        $options = new Options();
        $options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);


    
        // Data untuk tampilan
        $data['title'] = 'Proforma Invoice';
        $html = view('admin/content/printPRForm', $data);
    
        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);
    
        // Atur ukuran kertas A4 dan orientasi landscape
        $dompdf->setPaper('A4', 'landscape');
    
        // Render PDF
        $dompdf->render();
    
        // Output PDF ke browser tanpa mengunduh otomatis
        $dompdf->stream("PR_{$id}.pdf", ["Attachment" => false]);
    }
// In MaterialRequest controller
public function generateCode()
{
    $model = new \App\Models\MdlMaterialRequest();
    $code = $model->generateCode();
    
    return $this->response->setJSON(['code' => $code]);
}
}
