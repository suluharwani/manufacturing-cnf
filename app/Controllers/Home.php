<?php

namespace App\Controllers;
use App\Controllers\WarehouseController;
class Home extends BaseController
{
    public function index(): string
    {   
        $d = new WarehouseController();       
   
        $data['content'] = view('admin/content/blank');
        return view('admin/index',$data);
    }
}
