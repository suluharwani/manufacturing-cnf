<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MaterialRequisitionProgress extends BaseController
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
    public function index()
    {
        // 
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

        $where = ['material_requisition.deleted_at' => NULL, 'material_requisition.completion' => 0, 'material_requisition.status' => 1];

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
 
        $data['content'] = view('admin/content/material_requisition_progress_form', $data);
        
        return view('admin/index', data: $data);
    }
    function posting($id)
{
    $mdl = new \App\Models\MdlMaterialRequisition();
    $userInfo = $_SESSION['auth'];

    // Set data dengan array asosiatif
    $data = [
        'completion' => $_POST['completion'],
        'server' => $userInfo['nama_depan'] . " " . $userInfo['nama_belakang'],
    ];

    $mdl->set($data)->where('id', $id)->update();

    if ($mdl->affectedRows() !== 0) {
        // Ambil data dari material_requisition_list
        $mdlList = new \App\Models\MdlMaterialRequisitionList();
        $dataList = $mdlList->where('id_material_requisition', $id)->findAll();

        if (!empty($dataList)) {
            $mdlProgress = new \App\Models\MdlMaterialRequisitionProgress();

            foreach ($dataList as $item) {
                // Copy data ke material_requisition_progress
                $mdlProgress->insert([
                    'id_material_requisition_list' => $item['id'],
                    'id_material' => $item['id_material'],
                    'id_currency' => $item['id_currency'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                ]);

                // Hapus data dari material_requisition_list
                // $mdlList->delete($item['id']);
            }
        }

        $riwayat = 'Berhasil ubah completion ke ' . $_POST['completion'] . ' requisition id: ' . $id;
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}

    
}
