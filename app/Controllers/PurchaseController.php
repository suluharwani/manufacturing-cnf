<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MdlPurchaseOrder;
use App\Models\MdlPurchaseOrderList;

class PurchaseController extends BaseController
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
    public function listdataPurchaseOrder()
    {

        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'purchase_order.*, supplier.supplier_name';

        // Define the joins (you can add more joins as needed)
        $joins = [
            ['supplier', 'supplier.id = purchase_order.supplier_id', 'left'],
        ];

        $where = ['purchase_order.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL,
            'purchase_order.code',
            'purchase_order.date',
            'purchase_order.status',
            'purchase_order.id',

        );
        $column_search = array(
            'purchase_order.kode',
            'supplier.supplier_name',

        );
        $order = array('purchase_order.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('purchase_order', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->code;
            $row[] = $lists->date;
            $row[] = $lists->status;
            $row[] = $lists->id;
            $row[] = $lists->supplier_name;



            // From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('purchase_order', $where),
            "recordsFiltered" => $serverside_model->count_filtered('purchase_order', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );


        return $this->response->setJSON($output);
    }
    public function add_po()
    {
        $mdl = new MdlPurchaseOrder();
        $mdl->insert($_POST);

        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Menambahkan Purchase order ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    function po($id)
    {

        $Mdl = new MdlPurchaseOrder();
        $MdlDetail = new MdlPurchaseOrderList();
        $dataPembelian = $Mdl
            ->select('country_data.*, purchase_order.*, supplier.id_currency as curr_id, currency.kode as curr_code, currency.nama as curr_name, supplier.supplier_name, supplier.address as supplier_address')
            ->join('supplier', 'supplier.id = purchase_order.supplier_id', 'left')
            ->join('currency', 'currency.id = supplier.id_currency', 'left')
            ->join('country_data', 'country_data.id_country = supplier.id_country', 'left')
            ->where('purchase_order.id', $id)->get()->getResultArray();
        // $dataPembelianDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materialscountry_datamaterials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataPembelianDetail);
// die();
        $data['po'] = $dataPembelian;

        // var_dump($data['pi']);
        // die();
        $data['content'] = view('admin/content/form_po', $data);
        return view('admin/index', $data);
    }
    public function listdataPo($id)
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'purchase_order_list.*, purchase_order_list.id as det_id,materials.name as nama, materials.kode as kode,materials.id as id_material, purchase_order_list.remarks as remarks';

        // Define the joins (you can add more joins as needed)
        $joins = [
            ['materials', 'materials.id = purchase_order_list.id_material', 'left'],
        ];

        $where = ['purchase_order_list.id_po ' => $id, 'purchase_order_list.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL,
            'materials.kode',
            'materials.name',
            'purchase_order_list.quantity',
            'purchase_order_list.price',
            'purchase_order_list.remarks',
            'purchase_order_list.id',
        );
        $column_search = array(
            'materials.name',
            'materials.kode',

        );
        $order = array('purchase_order_list.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('purchase_order_list', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id_material;
            $row[] = $lists->nama;
            $row[] = $lists->kode;
            $row[] = $lists->quantity;
            $row[] = $lists->price;
            $row[] = $lists->det_id;
            $row[] = $lists->remarks;


            // From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('purchase_order_list', $where),
            "recordsFiltered" => $serverside_model->count_filtered('purchase_order_list', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );


        return $this->response->setJSON($output);
    }
    public function addPOList()
    {

        $mdl = new MdlPurchaseOrderList();
        $mdl->insert($_POST);

        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Menambahkan Purchase order List ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function update($id){
        $mdl = new MdlPurchaseOrder();
        $mdl->update($id, $_POST);
        if ($mdl->affectedRows() !== 0) {
            $riwayat = 'Mengubah Purchase Order';
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(['message'=> '
                Tidak ada perubahan pada data', 'code'=>1]));
        }
    }
}
