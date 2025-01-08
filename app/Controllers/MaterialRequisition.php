<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties;
class MaterialRequisition extends BaseController
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
    public function addDocument()
    {
        $userInfo = $_SESSION['auth'];
        $mdl = new \App\Models\MdlMaterialDestruction();
        $data['code'] = $this->request->getPost('code');
        $data['remarks'] = $this->request->getPost('remarks');
        $data['id_dept'] = $this->request->getPost('id_dept');
        $data['id_user'] = $userInfo['id'];

        $mdl->insert($data);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = "".$userInfo['nama_depan']." Menambahkan Material Destruction ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function listdata(){
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'material_requisition.*, department.name as department_name, users.nama_depan as nama_depan, users.nama_belakang as nama_belakang, work_order.kode as wo_code';

        // Define the joins (you can add more joins as needed)
        $joins = [
            ['department', 'department.id = material_requisition.id_dept', 'left'],
            ['users', 'users.id = material_requisition.id_user', 'left'],
            ['work_order', 'work_order.id = material_requisition.id_wo', 'left'],
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
    public function delete(){
        $userInfo = $_SESSION['auth'];

        $mdl = new \App\Models\MdlMaterialDestruction();
        $id = $this->request->getPost('id');
        $code = $this->request->getPost('code');
        $mdl->where('id',$id);
        $mdl->delete();
        if ($mdl->affectedRows()!=0) {
          $riwayat = "{$userInfo['nama_depan']} {$userInfo['nama_belakang']} Menghapus dokumen pemusnahan $code";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => $code)));
        }
    }
    public function destruction_form($id){
        $MdlMaterialReturn = new \App\Models\MdlMaterialDestruction();
        $MdlMaterialReturnList = new \App\Models\MdlMaterialDestructionList();
    $dataDoc = $MdlMaterialReturn
                    ->select( select: 'material_destruction.*,
             department.name as dept,
             users.nama_depan as nama_depan,
             users.nama_belakang as nama_belakang')
                    ->join('department', 'department.id = material_destruction.id_dept', 'left')
                    ->join('users','users.id = material_destruction.id_user', 'left')
                
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
    public function addMD(){
        $userInfo = $_SESSION['auth'];
        $mdlList = new \App\Models\MdlMaterialDestructionList();

        $dataMaterial = [
          "id_material" =>  $_POST["id_material"],
          "jumlah" =>  $_POST["quantity"],
          "remarks" =>  $_POST["remarks"],
          "id_material_destruction" =>  $_POST["id_material_destruction"],
          
        ];
        var_dump($dataMaterial);

        if ($mdlList->insert($dataMaterial)) {
        
    
            $riwayat = "User ".$userInfo['nama_depan']." ".$userInfo['nama_belakang']." menambahkan material id: ".$_POST['id_material']."";
            header('HTTP/1.1 200 OK');
        
        }else{
          $riwayat = "User ".$userInfo['nama_depan']." gagal menambahkan material id: ".$_POST['id_material'];
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'User exist, gagal menambahkan data.', 'code' => 3)));
        }
        $this->changelog->riwayat($riwayat);
    }
    function datamd($id){
        $mdl = new \App\Models\MdlMaterialDestructionList();
        $data = $mdl->select('material_destruction_list.*, materials.name as material,materials.kode as code, material_destruction.status as status')
                    ->join('materials', 'materials.id = material_destruction_list.id_material', 'left')
                    ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction', 'left')
                    ->where('id_material_destruction', $id)->findAll();
        return json_encode($data);
    }

    function deleteList($id) {
        $mdl = new \App\Models\MdlMaterialDestructionList();
        $mdl->delete($id);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Menghapus Material Request List';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    function posting($id) {
        $mdl = new \App\Models\MdlMaterialDestruction();
        $mdl->set('status', $_POST['status'])->where('id',$id)->update();
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Berhasil ubah status ke '.$_POST['status'].' destruction id: ' . $id.'';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }


}
