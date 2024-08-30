<?php

namespace App\Controllers;
use AllowDynamicProperties; 
use App\Controllers\WarehouseController;
use Bcrypt\Bcrypt;

class Home extends BaseController
{
    protected $bcrypt;
    protected $userValidation;
    protected $bcrypt_version;
    protected $session;
    protected $db;
    protected $uri;
    protected $form_validation;
    public function __construct()
    {
      $this->request = \Config\Services::request();
      $this->db      = \Config\Database::connect();
      $this->session = session();
      $this->bcrypt = new Bcrypt();
      $this->bcrypt_version = '2a';
      $this->uri = service('uri');
      helper('form');
      $this->form_validation = \Config\Services::validation();
      $this->userValidation = new \App\Controllers\LoginValidation();

      $check = new \App\Controllers\CheckAccess();
      $check->logged();
 
    }
    public function index(): string
    {   
        $this->access('operator');
        $d = new WarehouseController();       
   
        $data['content'] = view('admin/content/dashboard');
        return view('admin/index',$data);
    }
    function access($page){
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'],$page);
        }
    public function material(){
        $data['content'] = view('admin/content/material');
        return view('admin/index',$data); 
    }

}
