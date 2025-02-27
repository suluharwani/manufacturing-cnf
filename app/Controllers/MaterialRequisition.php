<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties;
use Dompdf\Dompdf;
use Dompdf\Options;
class MaterialRequisition extends BaseController
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

    function access($page)
    {
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'], $page);
    }
    public function index()
    {
        // 
    }
    public function addDocument()
    {
        $userInfo = $_SESSION['auth'];
        $mdl = new \App\Models\MdlMaterialRequisition();
        $data['code'] = $this->request->getPost('code');
        $data['id_wo'] = $this->request->getPost('id_wo');
        $data['id_dept'] = $this->request->getPost('id_dept');
        $data['id_user'] = $userInfo['id'];
        $data['requestor'] =$this->request->getPost('requestor');
        $data['server'] =  $userInfo['nama_depan'] . ' ' . $userInfo['nama_belakang'];
        $data['remarks'] = $this->request->getPost('remarks');

        $mdl->insert($data);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "" . $userInfo['nama_depan'] . " Menambahkan Maerial Requisition ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function listdata()
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'material_requisition.*, department.name as department_name, users.nama_depan as nama_depan, users.nama_belakang as nama_belakang, work_order.kode as wo_code, proforma_invoice.invoice_number as pi';

        // Define the joins (you can add more joins as needed)
        $joins = [
            ['department', 'department.id = material_requisition.id_dept', 'left'],
            ['users', 'users.id = material_requisition.id_user', 'left'],
            ['work_order', 'work_order.id = material_requisition.id_wo', 'left'],
            ['proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left'],
        ];

        $where = ['material_requisition.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL,
            'material_requisition.created_at',
            'material_requisition.code',
            'department_name',
            'nama_depan',
            'material_requisition.status',
            'id',

        );
        $column_search = array(
            'wo_code',
            'department_name',
            'nama_depan',
            'nama_belakang',

        );
        $order = array('material_requisition.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('material_requisition', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->created_at;
            $row[] = $lists->code;
            $row[] = $lists->wo_code;
            $row[] = $lists->department_name;
            $row[] = $lists->nama_depan . ' ' . $lists->nama_belakang;
            $row[] = $lists->status;
            $row[] = $lists->id;
            $row[] = $lists->pi;
            $row[] = $lists->requestor;



            // From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('material_requisition', $where),
            "recordsFiltered" => $serverside_model->count_filtered('material_requisition', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );


        return $this->response->setJSON($output);
    }
    function WoAvailablelistdata($idMR)  
    {  
        $db = \Config\Database::connect();  

        $builder = $db->table('work_order_detail');  
        $builder->select('  
            materials.id AS material_id,  
            materials.name AS material_name,  
            satuan.kode AS c_satuan,  
            satuan.nama AS satuan,  
             billofmaterial.penggunaan,
            work_order_detail.quantity,
            COALESCE(  
                (  
                    SELECT SUM(DISTINCT m_progress.jumlah)  
                    FROM material_requisition_progress m_progress  
                    JOIN material_requisition_list ON m_progress.id_material_requisition_list = material_requisition_list.id  
                    JOIN material_requisition wo ON material_requisition_list.id_material_requisition = material_requisition.id  
                    JOIN work_order ON material_requisition.id_wo = work_order.id  
                    JOIN proforma_invoice pi ON work_order.invoice_id = pi.id  
                    WHERE m_progress.id_material = materials.id  
                      AND material_requisition.id = ' . $idMR . '  
                ),   
                0  
            ) AS terpenuhi,  
            ROUND((SUM(billofmaterial.penggunaan * work_order_detail.quantity) - COALESCE(  
                (  
                    SELECT SUM(DISTINCT m_progress.jumlah)  
                    FROM material_requisition_progress m_progress  
                    JOIN material_requisition_list ON m_progress.id_material_requisition_list = material_requisition_list.id  
                    JOIN material_requisition wo ON material_requisition_list.id_material_requisition = material_requisition.id  
                    JOIN work_order ON material_requisition.id_wo = work_order.id  
                    JOIN proforma_invoice pi ON work_order.invoice_id = pi.id  
                    WHERE m_progress.id_material = materials.id  
                      AND material_requisition.id = ' . $idMR . '  
                ),   
                0  
            )),2) AS remaining_quantity,  
            COALESCE(  
                (  
                    SELECT ROUND(SUM(material_requisition_list.jumlah),2)  
                    FROM material_requisition_list  
                    JOIN material_requisition ON material_requisition_list.id_material_requisition = material_requisition.id  
                    WHERE material_requisition_list.id_material = materials.id  
                      AND material_requisition.status = 1  
                      AND material_requisition.id = ' . $idMR . '  
                ),   
                0  
            ) AS total_requisition,  
            COALESCE(  
                (  
                    SELECT ROUND(SUM(material_requisition_list.jumlah),2)  
                    FROM material_requisition_list  
                    WHERE material_requisition_list.id_material = materials.id  
                      AND material_requisition_list.id_material_requisition = ' . $idMR . '  
                ),   
                0  
            ) AS total_requisition_unposting  
        ');  
        
        $builder->join('billofmaterial', 'work_order_detail.product_id = billofmaterial.id_product');  
        $builder->join('materials', 'materials.id = billofmaterial.id_material');  
        $builder->join('materials_detail', 'materials.id = materials_detail.material_id');  
        $builder->join('satuan', 'satuan.id = materials_detail.satuan_id');  
        $builder->join('material_requisition', 'material_requisition.id_wo = work_order_detail.wo_id');  
        $builder->join('material_requisition_list', 'material_requisition_list.id_material_requisition = material_requisition.id', 'left');  
        $builder->join('work_order', 'work_order_detail.wo_id = work_order.id');  
        $builder->join('proforma_invoice', 'work_order.invoice_id = proforma_invoice.id');  
        
        $builder->where('material_requisition.id', $idMR);  
        $builder->groupBy(['materials.id', 'materials.name']);  
        $builder->orderBy('materials.name');  
        
        $query = $builder->get();  
        $results = $query->getResult();  
        return json_encode($results);
    }  
    function WoAvailablelistdatafinishing($idMR)  
    {  
        $db = \Config\Database::connect();  
      
        $builder = $db->table('work_order_detail');  
        $builder->select('  
            materials.id AS material_id,  
            materials.name AS material_name,  
            satuan.kode AS c_satuan,  
            satuan.nama AS satuan,  
            billofmaterialfinishing.penggunaan,
            work_order_detail.quantity,
        COALESCE(  
            (  
                SELECT SUM(DISTINCT m_progress.jumlah)  
                FROM material_requisition_progress m_progress  
                JOIN material_requisition_list ON m_progress.id_material_requisition_list = material_requisition_list.id 
                JOIN material_requisition wo ON material_requisition_list.id_material_requisition = material_requisition.id 
                JOIN work_order  ON material_requisition.id_wo = work_order.id 
                JOIN proforma_invoice pi ON work_order.invoice_id = pi.id  
                WHERE m_progress.id_material = materials.id AND pi.id = work_order.invoice_id  
            ),   
            0  
        ) AS terpenuhi,  
            ROUND((SUM(billofmaterialfinishing.penggunaan * work_order_detail.quantity) - COALESCE(  
                (  
                    SELECT SUM(DISTINCT m_progress.jumlah)  
                    FROM material_requisition_progress m_progress  
                JOIN material_requisition_list ON m_progress.id_material_requisition_list = material_requisition_list.id 
                JOIN material_requisition wo ON material_requisition_list.id_material_requisition = material_requisition.id 
                JOIN work_order  ON material_requisition.id_wo = work_order.id 
                JOIN proforma_invoice pi ON work_order.invoice_id = pi.id  
                WHERE m_progress.id_material = materials.id AND pi.id = work_order.invoice_id  
                ),   
                0  
            )),2) AS remaining_quantity,  
            COALESCE(  
                (  
                    SELECT ROUND(SUM(material_requisition_list.jumlah),2)  
                    FROM material_requisition_list  
                    JOIN material_requisition ON material_requisition_list.id_material_requisition = material_requisition.id  
                    WHERE material_requisition_list.id_material = materials.id AND material_requisition.status = 1  
                ),   
                0  
            ) AS total_requisition,  
            COALESCE(  
                (  
                    SELECT ROUND(SUM(material_requisition_list.jumlah),2)  
                    FROM material_requisition_list  
                    WHERE material_requisition_list.id_material = materials.id  
                ),   
                0  
            ) AS total_requisition_unposting  
        ');  
        $builder->join('billofmaterialfinishing', 'work_order_detail.product_id = billofmaterialfinishing.id_product');  
        $builder->join('materials', 'materials.id = billofmaterialfinishing.id_material');  
        $builder->join('materials_detail', 'materials.id = materials_detail.material_id');  
        $builder->join('satuan', 'satuan.id = materials_detail.satuan_id');  
        $builder->join('material_requisition', 'material_requisition.id_wo = work_order_detail.wo_id');  
        $builder->join('material_requisition_list', 'material_requisition_list.id_material_requisition = material_requisition.id', 'left'); // Menggunakan LEFT JOIN  
        $builder->join('work_order', 'work_order_detail.wo_id = work_order.id');
        $builder->join('proforma_invoice', 'work_order.invoice_id = proforma_invoice.id');
        $builder->where('material_requisition.id', $idMR);  
        $builder->groupBy(['materials.id', 'materials.name']);  
        $builder->orderBy('materials.name');  
      
        $query = $builder->get();  
        $results = $query->getResult();  
        return json_encode($results);  
    }  
    
    

public function deleteList($id)
{
    $userInfo = $_SESSION['auth'];

    $mdl = new \App\Models\MdlMaterialRequisitionList();
    // $id = $this->request->getPost('id');

    // Ambil data sebelum dihapus untuk riwayat
    $dataBeforeDelete = $mdl->where('id', $id)->first();

    if ($dataBeforeDelete) {
        $mdl->delete();

        if ($mdl->affectedRows() != 0) {
            $riwayat = "{$userInfo['nama_depan']} {$userInfo['nama_belakang']} menghapus dokumen id: {$id} dengan data: " . json_encode($dataBeforeDelete);
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

    public function destruction_form($id)
    {
        $MdlMaterialReturn = new \App\Models\MdlMaterialDestruction();
        $MdlMaterialReturnList = new \App\Models\MdlMaterialDestructionList();
        $dataDoc = $MdlMaterialReturn
            ->select(select: 'material_destruction.*,
             department.name as dept,
             users.nama_depan as nama_depan,
             users.nama_belakang as nama_belakang')
            ->join('department', 'department.id = material_destruction.id_dept', 'left')
            ->join('users', 'users.id = material_destruction.id_user', 'left')

            ->where('material_destruction.id', $id)->get()->getResultArray();
        // $dataDetail = $MdlPembelianDetail
        //                     ->select('materials.*')
        //                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
        //                     ->join("materials","materials.id = pembelian_detail.id_material")
        //                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
        //                     ->where('pembelian.id', $idPembelian)->find();
        // var_dump($dataMaterialReturnDetail);
        // die();
        $data['material_destruction'] = $dataDoc[0];
        // $data['pembelianDetail'] = $dataMaterialReturnDetail;
        $data['content'] = view('admin/content/material_destruction_form', $data);
        return view('admin/index', $data);
    }
    public function addMD()
    {
        $userInfo = $_SESSION['auth'];
        $mdlList = new \App\Models\MdlMaterialDestructionList();

        $dataMaterial = [
            "id_material" => $_POST["id_material"],
            "jumlah" => $_POST["quantity"],
            "remarks" => $_POST["remarks"],
            "id_material_destruction" => $_POST["id_material_destruction"],

        ];
        var_dump($dataMaterial);

        if ($mdlList->insert($dataMaterial)) {


            $riwayat = "User " . $userInfo['nama_depan'] . " " . $userInfo['nama_belakang'] . " menambahkan material id: " . $_POST['id_material'] . "";
            header('HTTP/1.1 200 OK');

        } else {
            $riwayat = "User " . $userInfo['nama_depan'] . " gagal menambahkan material id: " . $_POST['id_material'];
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
    }
    function datamd($id)
    {
        $mdl = new \App\Models\MdlMaterialDestructionList();
        $data = $mdl->select('material_destruction_list.*, materials.name as material,materials.kode as code, material_destruction.status as status')
            ->join('materials', 'materials.id = material_destruction_list.id_material', 'left')
            ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction', 'left')
            ->where('id_material_destruction', $id)->findAll();
        return json_encode($data);
    }

    // function deleteList($id)
    // {
    //     $mdl = new \App\Models\MdlMaterialDestructionList();
    //     $mdl->delete($id);
    //     if ($mdl->affectedRows() !== 0) {
    //         $riwayat = 'Menghapus Material Request List';
    //         $this->changelog->riwayat($riwayat);
    //         header('HTTP/1.1 200 OK');
    //     } else {
    //         header('HTTP/1.1 500 Internal Server Error');
    //         header('Content-Type: application/json; charset=UTF-8');
    //         die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    //     }
    // }
    function posting($id)
    {
        $mdl = new \App\Models\MdlMaterialRequisition();
        $mdl->set('status', $_POST['status'])->where('id', $id)->update();
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Berhasil ubah status ke ' . $_POST['status'] . ' requisition id: ' . $id . '';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function mr($id)
    {
        $mdl = new \App\Models\MdlMaterialRequisition();

        $data['mreq'] = $mdl->select('material_requisition.*,
        proforma_invoice.invoice_number as pi,
         work_order.kode as wo,
         department.name as dep,
         users.nama_depan as nama_depan,
         users.nama_belakang as nama_belakang,
         proforma_invoice.invoice_number as pi')
            ->join('department', 'department.id = material_requisition.id_dept', 'left')
            ->join('users', 'users.id = material_requisition.id_user', 'left')
            ->join('work_order', 'work_order.id = material_requisition.id_wo', 'left')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left')
            ->where('material_requisition.id', $id)->first();
 
        $data['content'] = view('admin/content/material_requisition_form', $data);
        
        return view('admin/index', data: $data);
    }

    function dataRequestList($id){
        $mdl = new \App\Models\MdlMaterialRequisitionList();
        $data = $mdl->select('material_requisition_list.*, material_requisition.status, materials.name as material_name, materials.kode as material_code')
                    ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition')
                    ->join('materials', 'materials.id = material_requisition_list.id_material', 'left')
                    ->where('id_material_requisition', $id);
                
        $results = $data->get()->getResult();
        return json_encode(value: $results);



    }
    public function submitRequest()
    {
        $userInfo = $_SESSION['auth'];
        $Mdl = new \App\Models\MdlMaterialRequisitionList();
    
        // Data yang diterima dari POST
        $idMaterialRequisition = $_POST['id_material_requisition'];
        $idMaterial = $_POST['id_material'];
        $jumlah = $_POST['jumlah'];
    
        // Cek apakah entri sudah ada
        $existingEntry = $Mdl->where('id_material_requisition', $idMaterialRequisition)
                             ->where('id_material', $idMaterial)
                             ->first();
    
        if ($existingEntry) {
            // Update jumlah jika entri sudah ada
            $newJumlah = $existingEntry['jumlah'] + $jumlah;
            $updateData = [
                'jumlah' => $newJumlah
            ];
    
            if ($Mdl->update($existingEntry['id'], $updateData)) {
                $riwayat = "User {$userInfo['nama_depan']} memperbarui jumlah untuk MRN id: {$idMaterialRequisition}, id material: {$idMaterial}, jumlah baru: {$newJumlah}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 200 OK');
            } else {
                $riwayat = "User {$userInfo['nama_depan']} gagal memperbarui jumlah untuk MRN id: {$idMaterialRequisition}, id material: {$idMaterial}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(['message' => 'Gagal memperbarui data.', 'code' => 2]));
            }
        } else {
            // Insert data baru jika entri tidak ada
            if ($Mdl->insert($_POST)) {
                $riwayat = "User {$userInfo['nama_depan']} menambahkan dokumen: MRN id: {$idMaterialRequisition}, id material: {$idMaterial}, jumlah: {$jumlah}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 200 OK');
            } else {
                $riwayat = "User {$userInfo['nama_depan']} gagal menambahkan MRN id: {$idMaterialRequisition}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(['message' => 'Gagal menambahkan data.', 'code' => 3]));
            }
        }
    }
    function print($id){

        $mdl = new \App\Models\MdlMaterialRequisition();

        $data['mreq'] = $mdl->select('material_requisition.*,
        proforma_invoice.invoice_number as pi,
         work_order.kode as wo,
         department.name as dep,
         users.nama_depan as nama_depan,
         users.nama_belakang as nama_belakang,
         proforma_invoice.invoice_number as pi')
            ->join('department', 'department.id = material_requisition.id_dept', 'left')
            ->join('users', 'users.id = material_requisition.id_user', 'left')
            ->join('work_order', 'work_order.id = material_requisition.id_wo', 'left')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left')
            ->where('material_requisition.id', $id)->first();
            $mdlList = new \App\Models\MdlMaterialRequisitionList();
            $data['material'] = $mdlList->select('satuan.kode as satuan_kode, material_requisition_list.*, material_requisition.status, materials.name as material_name, materials.kode as material_code')
                        ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition')
                        ->join('materials', 'materials.id = material_requisition_list.id_material', 'left')
                        ->join('materials_detail', 'materials.id = materials_detail.material_id', 'left')
                        ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
                        ->where('id_material_requisition', $id)->findAll();
        
        $options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);


    
        // Data untuk tampilan
        $data['title'] = 'Proforma Invoice';
        $html = view('admin/content/printReq', $data);
    
        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);
    
        // Atur ukuran kertas A4 dan orientasi landscape
        $dompdf->setPaper('A4', 'landscape');
    
        // Render PDF
        $dompdf->render();
    
        // Output PDF ke browser tanpa mengunduh otomatis
        $dompdf->stream("REQ_{$data['mreq']['code']}.pdf", ["Attachment" => false]);

    }
    
}
