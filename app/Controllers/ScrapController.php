<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
class ScrapController extends BaseController
{
    protected $changelog;
    public function __construct()
    {
        //   parent::__construct();
        $this->db = \Config\Database::connect();
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
    public function scrap_form($id)
    {
         $Mdl = new \App\Models\MdlScrapDoc();
$dataScrapDoc = $Mdl
                ->select('proforma_invoice.invoice_number as pi, work_order.kode as wo_code, department.name as dept_name, scrap_doc.*')
                ->join('work_order', 'work_order.id = scrap_doc.id_wo', 'left')
                ->join('department', 'department.id = scrap_doc.id_dept','left')    
                ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id','left')    
                ->where('scrap_doc.id', $id)->get()->getResultArray();

        $data['scrap_doc'] = $dataScrapDoc;

        // var_dump($data['scrap_doc']);
        // die();
        $data['content'] = view('admin/content/scrapdet',$data);
        return view('admin/index', $data);
    }
    public function listdataScrap(){
    
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();
        
        // Define the columns to select
        $select_columns = 'scrap_doc.*,work_order.kode as wo_code, proforma_invoice.invoice_number, department.name as dept_name';
        
        // Define the joins (you can add more joins as needed)
        $joins = [
              ['work_order', 'work_order.id = scrap_doc.id_wo', 'left'],
              ['proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left'],
              ['department', 'department.id = scrap_doc.id_dept', 'left'],
        ];

        $where = ['scrap_doc.id !=' => 0, 'scrap_doc.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL, 
            'proforma_invoice.invoice_number', 
            'work_order.kode', 
            'scrap_doc.id',
            'scrap_doc.start',
            'scrap_doc.end',
            'scrap_doc.id',
        );
        $column_search = array(
            'scrap_doc.nama', 
            'scrap_doc.kode', 
       
        );
        $order = array('scrap_doc.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('scrap_doc', $select_columns, $joins, $column_order, $column_search, $order, $where);
        
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id;
            $row[] = $lists->invoice_number;
            $row[] = $lists->wo_code;
            $row[] = $lists->code;
            $row[] = $lists->dept_name;
            $row[] = $lists->status;

// From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('scrap_doc', $where),
            "recordsFiltered" => $serverside_model->count_filtered('scrap_doc', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );
       

        return $this->response->setJSON($output);
}
function WoAvailablelistdata($idScrap)
{
    $db = \Config\Database::connect();

$builder = $db->table('work_order_detail');
$builder->select('
    materials.id AS material_id,
    materials.name AS material_name,
    materials.kode AS material_code,
    satuan.kode AS c_satuan,
    satuan.nama AS satuan,
    work_order_detail.quantity
     
');
$builder->join('billofmaterial', 'work_order_detail.product_id = billofmaterial.id_product');
$builder->join('materials', 'materials.id = billofmaterial.id_material');
$builder->join('materials_detail', 'materials.id = materials_detail.material_id');
$builder->join('satuan', 'satuan.id = materials_detail.satuan_id');
$builder->join('scrap_doc', 'scrap_doc.id_wo = work_order_detail.wo_id');
$builder->where('scrap_doc.id', $idScrap);
$builder->groupBy(['materials.id', 'materials.name']);
$builder->orderBy('materials.name');

$query = $builder->get();
$results = $query->getResult();
return json_encode($results);
}
public function addScrap(){
    if ($this->request->isAJAX()) {
            $scrap_doc_id = $this->request->getPost('scrap_doc_id');
            $material_id = $this->request->getPost('material_id');
            $reason = $this->request->getPost('reason');
            $quantity = $this->request->getPost('quantity');

            // Validasi input
            if (empty($scrap_doc_id) || empty($material_id) || empty($quantity)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Semua data wajib diisi.'
                ], ResponseInterface::HTTP_BAD_REQUEST);
            }
            $model = new \App\Models\MdlScrap(); // Ganti dengan nama model Anda

            // Cek apakah data dengan scrap_doc_id dan material_id cocok
            $existingData = $model->where('scrap_doc_id', $scrap_doc_id)
                                  ->where('material_id', $material_id)
                                  ->first();

            if ($existingData) {
                // Update quantity dengan penjumlahan
                $newQuantity = $existingData['quantity'] + $quantity;

                $model->update($existingData['id'], [
                    'quantity' => $newQuantity,
                    'reason' => $reason // Update alasan jika diperlukan
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil diperbarui.',
                    'data' => [
                        'scrap_doc_id' => $scrap_doc_id,
                        'material_id' => $material_id,
                        'quantity' => $newQuantity
                    ]
                ]);
            } else {
                // Insert data baru jika belum ada
                $model->insert([
                    'scrap_doc_id' => $scrap_doc_id,
                    'material_id' => $material_id,
                    'reason' => $reason,
                    'quantity' => $quantity
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil ditambahkan.',
                    'data' => [
                        'scrap_doc_id' => $scrap_doc_id,
                        'material_id' => $material_id,
                        'quantity' => $quantity
                    ]
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Hanya dapat diakses melalui AJAX.'
        ], ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
}
public function add(){

    $mdl = new \App\Models\MdlScrapDoc();
    $mdl->insert($_POST);
    if ($mdl->affectedRows() !== 0) {
        $riwayat = 'Berhasil menambahkan scrap doc '.$_POST['code'].' ';
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}
public function materialScrapList($idScrap){
    $model = new \App\Models\MdlScrap();
    $data = $model->select('scrap.*, materials.kode, materials.name, scrap.quantity as quantity, satuan.kode as satuan')
    ->join('materials', 'materials.id = scrap.material_id')
    ->join('materials_detail', 'materials_detail.material_id = materials.id')
    ->join('satuan', 'satuan.id = materials_detail.satuan_id')
    ->where('scrap_doc_id', $idScrap)->get()->getResultArray();
    return json_encode( $data );
}
public function deleteList($id){
    $userInfo = $_SESSION['auth'];

    $mdl = new \App\Models\MdlScrap();
    // $id = $this->request->getPost('id');

    // Ambil data sebelum dihapus untuk riwayat
    $dataBeforeDelete = $mdl->where( 'id', $id)->first();

    if ($dataBeforeDelete) {
        $mdl->where( 'id', $id)->delete();

        if ($mdl->affectedRows() != 0) {
            $riwayat = "{$userInfo['nama_depan']} {$userInfo['nama_belakang']} menghapus scrap id: {$id} dengan data: " . json_encode($dataBeforeDelete);
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 500)));
        }
    } else {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Data tidak ditemukan', 'code' => 404)));
    }

}
    
function posting($id) {
    $mdl = new \App\Models\MdlScrapDoc();
    $mdl->set('status', $_POST['status'])->where('id',$id)->update();
    if ($mdl->affectedRows() !== 0) {
        $riwayat = 'Berhasil ubah status ke '.$_POST['status'].' scrap id: ' . $id.'';
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}
public function printScrap($id){
    $model = new \App\Models\MdlScrap();
    $data['material'] = $model->select('scrap.*, materials.kode, materials.name, scrap.quantity as quantity, satuan.kode as satuan')
    ->join('materials', 'materials.id = scrap.material_id')
    ->join('materials_detail', 'materials_detail.material_id = materials.id')
    ->join('satuan', 'satuan.id = materials_detail.satuan_id')
    ->where('scrap_doc_id', $id)->get()->getResultArray();


    $Mdl = new \App\Models\MdlScrapDoc();
    $data['doc'] = $Mdl
                ->select('proforma_invoice.invoice_number as pi, work_order.kode as wo_code, department.name as dept_name, scrap_doc.*')
                ->join('work_order', 'work_order.id = scrap_doc.id_wo', 'left')
                ->join('department', 'department.id = scrap_doc.id_dept','left')    
                ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id','left')    
                ->where('scrap_doc.id', $id)->get()->getResultArray()[0];
    $options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);



    // Data untuk tampilan
    $data['title'] = 'Proforma Invoice';
    $html = view('admin/content/printScrap', $data);

    // Load HTML ke Dompdf
    $dompdf->loadHtml($html);

    // Atur ukuran kertas A4 dan orientasi landscape
    $dompdf->setPaper('A4', 'landscape');

    // Render PDF
    $dompdf->render();

    // Output PDF ke browser tanpa mengunduh otomatis
    $dompdf->stream("scrap_{$id}.pdf", ["Attachment" => false]);
}
}