<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 
class DashboardController extends BaseController
{
      protected $changelog;
  public function __construct()
  {
    //   parent::__construct();
    $this->db      = \Config\Database::connect();
    $this->session = session();
    $this->uri = service('uri');
    helper('form');
    $this->form_validation = \Config\Services::validation();
    $this->userValidation = new \App\Controllers\LoginValidation();
    $this->changelog = new \App\Controllers\Changelog();

      //if sesion habis
      //check access
    $check = new \App\Controllers\CheckAccess();
    $check->logged();
      //check access

  }
  function access($page){
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'],$page);
  }
    public function index()
    {
        //
    }
    function getCurrencyData(){
        
        $mdl = new \App\Models\MdlCurrency();
        $data = $mdl->findAll();
        return json_encode($data);
    }
    function fetchAndSaveRates()
    {
        $MdlCurrency =  new \App\Models\MdlCurrency();
        // URL API yang akan di-fetch
        $apiUrl = 'https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_2TN5tXTv3eVnBJaSYpNVM2sTVjoiGy0KFL1ZNDMK&currencies=EUR%2CUSD%2CJPY%2CBGN%2CCZK%2CDKK%2CGBP%2CHUF%2CPLN%2CRON%2CSEK%2CCHF%2CISK%2CNOK%2CHRK%2CRUB%2CTRY%2CAUD%2CBRL%2CCAD%2CCNY%2CHKD%2CIDR%2CILS%2CINR%2CKRW%2CMXN%2CMYR%2CNZD%2CPHP%2CSGD%2CTHB%2CZAR&base_currency=IDR'; 

        // Inisialisasi CURL
        $ch = curl_init();

        // Set opsi CURL
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Eksekusi CURL dan ambil response
        $response = curl_exec($ch);

        // Cek apakah ada error
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return $this->response->setJSON([
                'success' => false,
                'message' => "CURL Error: " . $error
            ])->setStatusCode(500);
        }

        // Tutup CURL
        curl_close($ch);

        // Decode JSON
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Invalid data structure."
            ])->setStatusCode(400);
        }

        // Pastikan struktur data sesuai
        if (!isset($data['data']) || !is_array($data['data'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Invalid data structure."
            ])->setStatusCode(400);
        }

        // Simpan data ke database
        try {
            $MdlCurrency->saveRates($data['data']);
            return $this->response->setJSON([
                'success' => true,
                'message' => "Exchange rates saved successfully."
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Database Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
