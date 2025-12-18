<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DepartmentController extends BaseController
{
    public function index()
    {
        //
    }
    public function department_list(){
        $mdl = new \App\Models\MdlDepartment();
        $data = $mdl->findAll();
        return $this->response->setJSON($data);
    }


}
