<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    public function index()
    {
        //
    }
    public function material()
    {

        $data['group'] = 'Material';
        $data['title'] = 'Material Report';

        $data['content'] = view('admin/content/report/material');
        return view('admin/index', $data);
    }
    public function finished_good()
    {
        $data['group'] = 'Finished Good';
        $data['title'] = 'Finished Good Report';

        $data['content'] = view('admin/content/report/finished_good');
        return view('admin/index', $data);

    }
    public function purchase()
    {
        $data['group'] = 'Purchase';
        $data['title'] = 'Purchase Report';

        $data['content'] = view('admin/content/report/purchase');
        return view('admin/index', $data);

    }
    public function customer_order()
    {
        $data['group'] = 'Customer Order';
        $data['title'] = 'Customer Order Report';

        $data['content'] = view('admin/content/report/customer_order');
        return view('admin/index', $data);

    }
    public function activity()
    {
        $data['group'] = 'Activity';
        $data['title'] = 'Activity Report';

        $data['content'] = view('admin/content/report/activity');
        return view('admin/index', $data);

    }
    public function materialStockCard()
    {
        $data['opening_balance'] = $this->openingBalance($_POST);
        $data['balance_before'] = $this->balanceBefore($_POST);
        $data['destruction'] = $this->materialDestruction($_POST);
        $data['pembelian'] = $this->materialPurchase($_POST);
        $data['return'] = $this->materialReturn($_POST);
        $data['stock_opname'] = $this->materialStockOpname($_POST);
        $data['material_requisition'] = $this->materialRequisition($_POST);
        $data['merge'] = $this->sortByDate(array_merge($data['opening_balance'], $data['pembelian'], $data['material_requisition'], $data['return'], $data['destruction'], $data['stock_opname']));
        return json_encode($data);
    }
    function sortByDate($data)
    {
        usort($data, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
        return $data;
    }
    public function openingBalance($params)
    {
        $mdl = new \App\Models\MdlStock();

        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date'];

        $query = $mdl->select('"Opening Balance" as source,
        "IN" as desc,
        "default stock" as activity,
        stock.stock_awal as jumlah, 
        stock.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code,
        ')
            ->join('materials', 'materials.id = stock.id_material')
            ->where('id_material', $materialId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->where('stock.created_at >=', $startDate)
                ->where('stock.created_at <=', $endDate);
        }
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;

    }
    public function materialReturn($params)
    {
        $mdl = new \App\Models\MdlMaterialReturnList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Return" as source,
        "IN" as desc, 
        CONCAT("Reference code:(", material_return.code , ") - ", material_return.remarks) as activity,
        material_return_list.jumlah as jumlah, 
        material_return_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_return_list.id_material') // Join with materials table
            ->join('material_return', 'material_return.id = material_return_list.id_material_return') // Join with materials table
            ->where('material_return_list.id_material', $materialId)
            ->where('material_return.status', 1);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_return_list.created_at >=', $startDate)
                ->where('material_return_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialRequisition($params)
    {
        $mdl = new \App\Models\MdlMaterialRequisitionProgress();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Requisition" as source,
        "OUT" as desc, 
        CONCAT("Reference code:(", material_requisition.code , ") - ", material_requisition.remarks) as activity,
        -(material_requisition_progress.jumlah) as jumlah, 
        material_requisition_progress.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_requisition_progress.id_material') // Join with materials table
            ->join('material_requisition_list', 'material_requisition_list.id = material_requisition_progress.id_material_requisition_list') // Join with materials table
            ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition') // Join with materials table
            ->where('material_requisition.status', 1)
            ->where('material_requisition_progress.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_requisition_progress.created_at >=', $startDate)
                ->where('material_requisition_progress.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialPurchase($params)
    {
        $mdl = new \App\Models\MdlPembelianDetail();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Purchasing" as source,
        "IN" as desc, 
        CONCAT("Reference code:(", pembelian.invoice , ") - ", pembelian.remarks) as activity,
        pembelian_detail.jumlah as jumlah, 
        pembelian_detail.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = pembelian_detail.id_material') // Join with materials table
            ->join('pembelian', 'pembelian.id = pembelian_detail.id_pembelian') // Join with materials table
            ->where('pembelian.posting', 1)
            ->where('pembelian_detail.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('pembelian_detail.created_at >=', $startDate)
                ->where('pembelian_detail.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialDestruction($params)
    {
        $mdl = new \App\Models\MdlMaterialDestructionList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Destruction" as source,
        "OUT" as desc, 
        CONCAT("Reference code:(", material_destruction.code , ") - ", material_destruction.remarks) as activity,
        -(material_destruction_list.jumlah) as jumlah, 
        material_destruction_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_destruction_list.id_material') // Join with materials table
            ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction') // Join with materials table
            ->where('material_destruction.status', 1)
            ->where('material_destruction_list.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_destruction_list.created_at >=', $startDate)
                ->where('material_destruction_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialStockOpname($params)
    {
        $mdl = new \App\Models\MdlStockOpnameList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Stock Opname" as source,
        "SO" as desc,
        CONCAT("Reference code:(", stock_opname.code , ") - ", stock_opname.remarks) as activity,
        (stock_opname_list.jumlah_akhir - stock_opname_list.jumlah_awal) as jumlah, 
        stock_opname_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = stock_opname_list.id_material')
            ->join('stock_opname', 'stock_opname.id = stock_opname_list.id_stock_opname')
            ->where('stock_opname.status', 1)
            ->where('stock_opname_list.id_material', value: $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('stock_opname_list.created_at >=', $startDate)
                ->where('stock_opname_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function balanceBefore($params)
    {
        // $startDate = $params['start_date'];
        $materialId = $params['material_id'];
        $endDate = $params['start_date'];

        // Set startDate to 1970-01-01 00:00:00
        $startDate = '1970-01-01 00:00:00';

        // Convert endDate to DateTime object, subtract one day, and keep the time
        $endDateObj = new \DateTime($endDate);
        $endDateObj->modify('-1 second');
        $endDate = $endDateObj->format('Y-m-d H:i:s');
        // var_dump($endDate);
        // Prepare data for passing to other methods
        $data['material_id'] = $materialId;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        // Get material destruction, purchase, return, stock opname, and requisition
        $stock_awal = $this->openingBalance($data);
        $destruction = $this->materialDestruction($data);
        $pembelian = $this->materialPurchase($data);
        $return = $this->materialReturn($data);
        $stock_opname = $this->materialStockOpname($data);
        $material_requisition = $this->materialRequisition($data);
        // var_dump($material_requisition);
        // Calculate the total balance by summing 'jumlah' from each result
        $totalBalance = 0;

        // Sum the 'jumlah' values from each result, ensure we handle empty or non-array values gracefully
        $totalBalance += $this->sumJumlah($stock_awal);
        $totalBalance += $this->sumJumlah($destruction);
        $totalBalance += $this->sumJumlah($pembelian);
        $totalBalance += $this->sumJumlah($return);
        $totalBalance += $this->sumJumlah($stock_opname);
        $totalBalance += $this->sumJumlah($material_requisition);

        return $totalBalance;
    }

    /**
     * Helper function to sum the 'jumlah' field from the query results.
     */
    private function sumJumlah($data)
    {
        $sum = 0;

        // Check if $data is an array and has 'jumlah' field in each entry
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                if (isset($item['jumlah'])) {
                    $sum += $item['jumlah'];  // Add the value of 'jumlah'
                }
            }
        }

        return $sum;
    }
    function getHeader()
    {
        $userInfo = $_SESSION['auth'];
        // Get the ID from the AJAX request
        $id = $this->request->getGet('id');

        // Load the model to fetch material data
        $materialModel = new \App\Models\MdlMaterial();

        // Fetch material data based on the ID

        $materialData = $materialModel->select('materials.kode as kode, materials.name as name,satuan.kode as satuan_kode, satuan.nama as satuan_nama ')
        ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
        ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')             
        ->where('materials.id',$id)->find();


        // If material data exists, return it as JSON
        $data = $materialData[0];
        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'code' => $data['kode'],
                    'name' => $data['name'],
                    'admin'=> $userInfo['nama_depan']." ".$userInfo['nama_belakang'],
                    'satuan'=> $data['satuan_nama']." (".$data['satuan_kode'].")"
                ]
            ]);
        } else {
            // Return an error if the material data is not found
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Material not found'
            ]);
        }
    }



}
