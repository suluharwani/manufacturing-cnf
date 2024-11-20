<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MdlSupplier;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties;

class SupplierController extends BaseController
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

    // Load Supplier Data

    // List Supplier Data (Server-side)
    public function listdataSupplierJoin()
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Columns to select
        $select_columns = 'supplier.*, currency.nama as currency_name';

        // Define joins
        $joins = [
            ['currency', 'currency.id = supplier.id_currency', 'left']
        ];

        $where = ['supplier.id !=' => 0, 'supplier.deleted_at' => NULL];

        // Columns for ordering and searching
        $column_order = [
            NULL,
            'supplier.code',
            'supplier.supplier_name',
            'supplier.status',
            'supplier.id',
        ];

        $column_search = [
            'supplier.code',
            'supplier.supplier_name',
            'supplier.contact_name',
            'supplier.contact_email',
        ];

        $order = ['supplier.id' => 'desc'];

        $list = $serverside_model->get_datatables('supplier', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no; //0
            $row[] = $lists->status; //1
            $row[] = $lists->id; //2
            $row[] = $lists->code;//3
            $row[] = $lists->supplier_name;//4
            $row[] = $lists->contact_name;//5
            $row[] = $lists->contact_email;//6
            $row[] = $lists->contact_phone;//7
            $row[] = $lists->currency_name;//8
            $row[] = "<img src='" . base_url('uploads/supplier_logos/' . $lists->logo_url) . "' alt='" . $lists->supplier_name . "' style='height: 50px;'>";//9
            $row[] = "<button class='btn btn-info btn-sm viewSupplier' data-id='{$lists->id}'>View</button>
                      <button class='btn btn-warning btn-sm editSupplier' data-id='{$lists->id}'>Edit</button>
                      <button class='btn btn-danger btn-sm deleteSupplier' data-id='{$lists->id}'>Delete</button>";//10
            $data[] = $row;

        }

        $output = [
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('supplier', $where),
            "recordsFiltered" => $serverside_model->count_filtered('supplier', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        ];

        return $this->response->setJSON($output);
    }

    // Create Supplier
    public function create()
    {
        $data = $this->request->getPost();

        $validationRules = [
            'supplier_name' => 'required',
            'contact_name' => 'required',
            'contact_email' => 'required|valid_email',
            'contact_phone' => 'required',
            'address' => 'required',
            'id_currency' => 'required|numeric',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        if ($this->request->getFile('logo')->isValid()) {
            $data['logo_url'] = $this->uploadLogo();
        }

        $mdlSupplier = new MdlSupplier();
        $mdlSupplier->insert($data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Supplier berhasil ditambahkan.'
        ]);
    }

    // Update Supplier
    public function update($id)
    {
        $data = $this->request->getPost();

        $validationRules = [
            'supplier_name' => 'required',
            'contact_name' => 'required',
            'contact_email' => 'required|valid_email',
            'contact_phone' => 'required',
            'address' => 'required',
            'id_currency' => 'required|numeric',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        if ($this->request->getFile('logo')->isValid()) {
            $data['logo_url'] = $this->uploadLogo();
        }

        $mdlSupplier = new MdlSupplier();
        $mdlSupplier->update($id, $data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Supplier berhasil diperbarui.'
        ]);
    }

    // Delete Supplier
    public function delete($id)
    {
        $mdlSupplier = new MdlSupplier();
        $mdlSupplier->delete($id);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Supplier berhasil dihapus.'
        ]);
    }

    // Upload Supplier Logo
    private function uploadLogo()
    {
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/supplier_logos', $newName);
            return $newName;
        }
        return null;
    }
        public function get($id)
    {
        try {
            // Load model untuk mengambil data supplier
            $mdlSupplier = new MdlSupplier();

            // Ambil semua data supplier
            $suppliers = $mdlSupplier->where('id', $id)->find();

            // Kembalikan data dalam format JSON
            return $this->response->setJSON($suppliers);
        } catch (\Exception $e) {
            // Tangani error jika terjadi
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

}
