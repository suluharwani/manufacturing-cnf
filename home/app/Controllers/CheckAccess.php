<?php

namespace App\Controllers;

class CheckAccess extends BaseController
{
    public function index()
    {
    }
    public function access($id,$page){
        if (isset($_SESSION['logged'])){
         if (isset($_SESSION['auth'])) {
            $mdlAccess = new \App\Models\MdlAccess();
            if ($mdlAccess->getAccess($id,$page) == 1) {
            }else{
                if ($_SESSION['auth']['level']==1) {
                    // code...
                }else{
                    echo view('errors/html/403');
                    exit();
                     } 
            }
        }else{
            header('HTTP/1.1 403 Access denied');
            header('Content-Type: application/json; charset=UTF-8');
            echo view('errors/html/403');
            exit(); 
        }
    }
    else
    {
        header('Location: '.base_url('/login'));
        exit(); 
    }  
}
public function clientAccess(){
    if (isset($_SESSION['c_logged'])){
     if (isset($_SESSION['auth'])) {
        $mdlClient = new \App\Models\MdlClient();
        if ($mdlClient->check_client_active($_SESSION['auth']['email'])) {
                    // code...
            return true;
        }else{
            return false;
            // echo view('errors/html/verify');
            // exit();
        } 
    }else{
            return false;
        
        // header('HTTP/1.1 403 Access denied');
        // header('Content-Type: application/json; charset=UTF-8');
        // echo view('errors/html/403');
        // exit(); 
    }
}
else
{

    return false;
}  
}
function logged(){
  if (isset($_SESSION['logged']))
  {

  }
  else
  {
    header('Location: '.base_url('/login'));
    exit(); 

}  
}
function clientLogged(){
  if (isset($_SESSION['logged']))
  {

  }
  else
  {
    header('Location: '.base_url('/client'));
    exit(); 

}  
}
}
