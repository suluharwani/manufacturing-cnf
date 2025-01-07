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
        $data['balance_before'] =  $this->balanceBefore($_POST);
        $data['destruction'] = $this->materialDestruction($_POST);
        $data['pembelian'] = $this->materialPurchase($_POST);
        $data['return'] = $this->materialReturn($_POST);
        $data['stock_opname'] = $this->materialStockOpname($_POST);
        $data['material_requisition'] = $this->materialRequisition($_POST);
        $data['merge'] = $this->sortByDate(array_merge($data['pembelian'], $data['material_requisition'], $data['return'],$data['destruction'],$data['stock_opname']));
        return json_encode($data);
    }
    function sortByDate($data) {
        usort($data, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
        return $data;
    }
    public function materialReturn($params){
        $mdl = new \App\Models\MdlMaterialReturnList();
    
        // Get the material ID and date range from the POST request
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdl->select(' 
        "Material Return" as source,
        "IN" as desc, 
        material_return_list.jumlah as jumlah, 
        material_return_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_return_list.id_material') // Join with materials table
            ->where('material_return_list.id_material', $materialId);
    
        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_return_list.created_at >=', value: $startDate)
                  ->where('material_return_list.created_at <=', $endDate);
        }
    
        // Fetch the purchase details
        $data = $query->findAll();

    
        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialRequisition($params){
        $mdl = new \App\Models\MdlMaterialRequisitionProgress();
    
        // Get the material ID and date range from the POST request
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdl->select(' 
        "Material Requisition" as source,
        "OUT" as desc, 
        -(material_requisition_progress.jumlah) as jumlah, 
        material_requisition_progress.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_requisition_progress.id_material') // Join with materials table
            ->where('material_requisition_progress.id_material', $materialId);
    
        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_requisition_progress.created_at >=', value: $startDate)
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
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdl->select(' 
        "Purchasing" as source,
        "IN" as desc, 
        pembelian_detail.jumlah as jumlah, 
        pembelian_detail.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = pembelian_detail.id_material') // Join with materials table
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
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdl->select(' 
        "Material Destruction" as source,
        "OUT" as desc, 
        -(material_destruction_list.jumlah) as jumlah, 
        material_destruction_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_destruction_list.id_material') // Join with materials table
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
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdl->select(' 
        "Material Stock Opname" as source,
        "SO" as desc, 
        (stock_opname_list.jumlah_akhir - stock_opname_list.jumlah_awal) as jumlah, 
        stock_opname_list.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = stock_opname_list.id_material') // Join with materials table
            ->where('stock_opname_list.id_material', $materialId);
    
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
        $mdlPembelian = new \App\Models\MdlPembelianDetail();
        $mdlStock = new \App\Models\MdlStock(); // Model untuk tabel stock
    
        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date'];
    
        // Query to get the opening stock from the stock table
        $openingStockQuery = $mdlStock->select('stock_awal')
            ->where('id_material', $materialId)
            ->first();
        
        $openingStock = $openingStockQuery ? $openingStockQuery['stock_awal'] : 0;
    
        // Query to sum pembelian_detail.jumlah up to the start date
        $purchasesQuery = $mdlPembelian->select('SUM(jumlah) as total_purchases')
            ->where('id_material', $materialId)
            ->where('created_at <', $startDate)
            ->first();
        
        $totalPurchases = $purchasesQuery ? $purchasesQuery['total_purchases'] : 0;
    
        // Calculate total balance
        $totalBalance = $totalPurchases + $openingStock;
    
        // Return the total balance as a response
        return $totalBalance;
    }
    


}
