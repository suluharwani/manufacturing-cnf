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
    public function material(){

      $data['group'] = 'Material';
      $data['title'] = 'Material Report';

      $data['content'] = view('admin/content/report/material');
      return view('admin/index', $data);
  }
  public function finished_good(){
      $data['group'] = 'Finished Good';
      $data['title'] = 'Finished Good Report';

      $data['content'] = view('admin/content/report/finished_good');
      return view('admin/index', $data);

  }
  public function purchase(){
      $data['group'] = 'Purchase';
      $data['title'] = 'Purchase Report';

      $data['content'] = view('admin/content/report/purchase');
      return view('admin/index', $data);

  }
  public function customer_order(){
      $data['group'] = 'Customer Order';
      $data['title'] = 'Customer Order Report';

      $data['content'] = view('admin/content/report/customer_order');
      return view('admin/index', $data);

  }
  public function activity(){
      $data['group'] = 'Activity';
      $data['title'] = 'Activity Report';

      $data['content'] = view('admin/content/report/activity');
      return view('admin/index', $data);

  }
  public function materialStockCard(){
  
    $mdlPembelian = new \App\Models\MdlPembelianDetail();
    $data['pembelian'] = $mdlPembelian
        ->where('id_material', $_POST['material_id'])
        ->where('created_at >=', $_POST['start_date'])
        ->where('created_at <=', $_POST['end_date'])
        ->findAll();
    return json_encode($data);
  }
}
