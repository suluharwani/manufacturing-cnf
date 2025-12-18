<?php

namespace App\Controllers;

class Changelog extends BaseController
{
  protected $userValidation;
  protected $db;
  public function __construct()
  {
    $this->db      = \Config\Database::connect();
    $this->userValidation = new \App\Controllers\LoginValidation();
    helper('form');
  }
    public function index()
    {
        if (isset($_SESSION['logged']))
        {
         
        }
    }
    function riwayat($riwayat){
        if (isset($_SESSION['auth'])) {
            $nama_admin = $_SESSION['auth']['nama_depan']." ".$_SESSION['auth']['nama_belakang'];
            $id_admin = $_SESSION['auth']['id'];
          }else{
            $nama_admin = "Unknown user";
            $id_admin = 0;
          }
      
          $ip = $_SERVER['REMOTE_ADDR'];
          $changelog = ['nama_admin'=> $nama_admin,
          'id_google'=>$id_admin,
          'ip'=>$ip,
          'created_at'=>date("Y-m-d H:i:s"),
          'updated_at'=>date("Y-m-d H:i:s"),
          'riwayat'=> $riwayat,
        ];
        $builder_changelog = $this->db->table('changelog');
        $builder_changelog->insert($changelog);
        return true;
    }

}
