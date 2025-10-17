<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MdlMaterialReturn;
use App\Models\MdlMaterialReturnList;
class MaterialReturnController extends BaseController
{
    public function index()
    {
        //
    }
    function materialReturn()
    {
        $data = [
            'title' => 'Material Return',
            'subtitle' => 'List Material Return',
            'breadcrumb' => [
                'Material Return' => '',
                'List Material Return' => ''
            ]
        ];
        return view('admin/content/material_return', $data);
    }
    function addMaterialReturn()
    {
        $data = [
            'title' => 'Material Return',
            'subtitle' => 'Add Material Return',
            'breadcrumb' => [
                'Material Return' => '',
                'Add Material Return' => ''
            ]
        ];
        return view('admin/content/add_material_return', $data);
    }
    function editMaterialReturn(){
        $data = [
            'title' => 'Material Return',
            'subtitle' => 'Edit Material Return',
            'breadcrumb' => [
                'Material Return' => '',
                'Edit Material Return' => ''
            ]
        ];
        return view('admin/content/edit_material_return', $data);
    }
    function deleteMaterialReturn(){
        $data = [
            'title' => 'Material Return',
            'subtitle' => 'Delete Material Return',
            'breadcrumb' => [
                'Material Return' => '',
                'Delete Material Return' => ''
            ]
        ];
        return view('admin/content/delete_material_return', $data);
    }   
    function listdataMaterialReturn(){
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();
        
        // Define the columns to select
        $select_columns = 'material_return.id,
         material_return.created_at,
         material_return.code,
         material_return.status,
         material_return.id_wo,
         material_return.id_dept,
         material_return.id_user,
         material_return.remarks,
         material_return.deleted_at,
         work_order.kode as wo,
         department.name as dept,
         users.nama_depan as nama_depan,
         users.nama_belakang as nama_belakang';
        
        // Define the joins (you can add more joins as needed)
        $joins = [
            // ['proforma_invoice','proforma_invoice.id = material_request.id_pi', 'left'],
            ['work_order', 'work_order.id = material_return.id_wo', 'left'],
            ['department', 'department.id = material_return.id_dept', 'left'],
            ['users', 'users.id = material_return.id_user', 'left'],


        ];

        $where = ['material_return.id !=' => 0, 'material_return.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL, 
            'material_return.created_at',
            'material_return.code',
            'material_return.wo',
            'material_return.dept',
            'material_return.status',
            'material_return.id',

        );
        $column_search = array(
            'material_return.code', 
            'material_return.pi', 
        );
        $order = array('material_return.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('material_return', $select_columns, $joins, $column_order, $column_search, $order, $where);
        
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id;
            $row[] = $lists->created_at;
            $row[] = $lists->code;
            $row[] = $lists->wo;
            $row[] = $lists->nama_depan.' '.$lists->nama_belakang;
            $row[] = $lists->remarks;
            $row[] = $lists->status;
// From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('material_return', $where),
            "recordsFiltered" => $serverside_model->count_filtered('material_return', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );

      //   return $this->response->setJSON($output);
      
        return json_encode($output);
    }
    public function material_return_form($id){
        $MdlMaterialReturn = new  MdlMaterialReturn();
    $MdlMaterialReturnList = new MdlMaterialReturnList();
$dataMaterialReturn = $MdlMaterialReturn
                ->select( select: 'material_return.id,
         material_return.created_at,
         material_return.code,
         material_return.status,
         material_return.id_wo,
         material_return.id_dept,
         material_return.id_user,
         material_return.remarks,
         material_return.deleted_at,
         work_order.kode as wo,
         department.name as dept,
         users.nama_depan as nama_depan,
         users.nama_belakang as nama_belakang')
                ->join('work_order', 'work_order.id = material_return.id_wo', 'left')
                ->join('department', 'department.id = material_return.id_dept', 'left')
                ->join('users','users.id = material_return.id_user', 'left')
            
                ->where('material_return.id', $id)->get()->getResultArray();
// $dataMaterialReturnDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materials","materials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataMaterialReturnDetail);
// die();
$data['material_return'] = $dataMaterialReturn;
// $data['pembelianDetail'] = $dataMaterialReturnDetail;
$data['content'] = view('admin/content/material_return_form', $data);
return view('admin/index', $data);
    }
    function listdataMaterialReturnList($id){
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Columns to select
        // $select_columns = 'supplier.supplier_name, pembelian.* ,pembelian_detail.*, materials.*, materials_detail.*, pembelian_detail.id as id_pembelian_detail';
        // "id","id_material_return","id_material","id_currency","jumlah","created_at","updated_at","deleted_at" 
        $select_columns =  'material_return_list.id,
         material_return_list.id_material_return,
         material_return_list.id_material,
         material_return_list.jumlah,
         material_return_list.created_at,
         material_return_list.updated_at,
         material_return_list.deleted_at,
         materials.kode as material_kode,
         materials.name as material_name,
         material_return.status as status,
        ';

        // Define joins
        $joins = [
            ["material_return","material_return.id = material_return_list.id_material_return", 'left'],
            ["materials","materials.id = material_return_list.id_material", 'left'],


        ];

        $where = ['material_return_list.id !=' => 0, 'material_return_list.deleted_at' => NULL, 'material_return_list.id_material_return'=>$id];

        // Columns for ordering and searching
        $column_order = [
            NULL,
           
        ];

        $column_search = [
            'material_return_list.code',
 
        ];

        $order = ['material_return_list.id' => 'desc'];

        $list = $serverside_model->get_datatables('material_return_list', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no; //0
            $row[] = $lists->id; //1
            $row[] = $lists->material_kode; //2
            $row[] = $lists->material_name;
            $row[] = $lists->jumlah;
            $row[] = $lists->status;

           

        $data[] = $row;

                  }

                  $output = [
                    "draw" => $request->getPost("draw"),
                    "recordsTotal" => $serverside_model->count_all('material_return_list', $where),
                    "recordsFiltered" => $serverside_model->count_filtered('material_return_list', $select_columns, $joins, $column_order, $column_search, $order, $where),
                    "data" => $data,
                ];

                return $this->response->setJSON($output);  
    }

}
