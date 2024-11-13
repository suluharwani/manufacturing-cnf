<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Homepage extends BaseController
{
    public function index()
    {
        // $data['title'] = $_ENV['perusahaan']; ;
        echo('<img src= "'.base_url('assets/images.png').'" width="600px">  ');

        // $data['content'] = view('admin/content/material');
        // return view('admin/index', $data);
    }
}
