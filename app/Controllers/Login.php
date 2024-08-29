<?php

namespace App\Controllers;
use App\Controllers\WarehouseController;
use App\Models\MdlUser;
class Login extends BaseController
{
    public function index()
    {   
        $user = new MdlUser();       
        if ($user->countAllResults()>0) {
            return view('login/login');

        }else{
            return view('login/signup');

        }
        if ($this->request->getPost("submit") == "signup") {
            echo('');


            
          }
    }
  
}