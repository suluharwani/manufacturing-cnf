<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Homepage extends BaseController
{
    public function index()
    {
        return view('homepage');

    }
}
