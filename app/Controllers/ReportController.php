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
        $data['balance_before'] =  

        $data['pembelian'] = $this->materialPurchase($_POST);

        return json_encode($data);
    }

    public function materialPurchase($params)
    {
        $mdlPembelian = new \App\Models\MdlPembelianDetail();
    
        // Get the material ID and date range from the POST request
        $materialId =$params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'
    
        // Initialize the query
        $query = $mdlPembelian->select('pembelian_detail.*, materials.name as materials_name, ,materials.kode as materials_code, currency.kode as curr_code, currency.nama as curr_name, currency.rate as curr_rate') // Select fields from both tables
            ->join('materials', 'materials.id = pembelian_detail.id_material') // Join with materials table
            ->join('currency', 'currency.id = pembelian_detail.id_currency') // Join with materials table
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

}
