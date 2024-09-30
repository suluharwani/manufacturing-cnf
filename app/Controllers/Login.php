<?php

namespace App\Controllers;
use App\Controllers\WarehouseController;
use App\Models\MdlUser;
use Bcrypt\Bcrypt;

class Login extends BaseController
{
    protected $bcrypt;
    protected $userValidation;
    protected $changelog;
    protected $bcrypt_version;
    protected $session;
    protected $db;
    protected $uri;
    protected $form_validation;
    public function __construct()
    {
      $this->db = \Config\Database::connect();
      $this->session = session();
      $this->bcrypt = new Bcrypt();
      $this->bcrypt_version = '2a';
      $this->uri = service('uri');
      $this->form_validation = \Config\Services::validation();
      $this->userValidation = new \App\Controllers\LoginValidation();
      $this->changelog = new \App\Controllers\Changelog();
      helper('form');
    }
    public function index()
    {   
        $userModel = new MdlUser();       

        if ($this->request->getPost("submit") == "signup") {
            
            $this->form_validation->setRules(
                [
                  'email' => [
                    'label' => 'Email',
                    'rules' => 'required|min_length[4]|max_length[39]'
                  ],
                  'nama_depan' => [
                    'label' => 'Nama Depan',
                    'rules' => 'required|min_length[4]|max_length[39]'
                  ],
                  'nama_belakang' => [
                    'label' => 'Nama Belakang',
                    'rules' => 'required|min_length[4]|max_length[39]'
                  ],
                  'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[4]|max_length[39]'
                  ]
                ]
              );
              if ($this->form_validation->withRequest($this->request)->run()) {
                $newUserData =  array(
                                      'email'=>$_POST['email'],
                                      'nama_depan'=>$_POST['nama_depan'],
                                      'nama_belakang'=>$_POST['nama_belakang'],
                                      'level'=>1,
                                      'status'=>1,
                                      'password'=>$this->bcrypt->encrypt($_POST['password'] , $this->bcrypt_version)
                                    );
                $userModel->insert($newUserData);
                if ($userModel->affectedRows() > 0) {
                  $riwayat = "User {$_POST['nama_depan']} {$_POST['nama_belakang']} berhasil mendaftar sebagai admin";
                  $this->changelog->riwayat($riwayat);
                  return redirect()->to('/dashboard');
                }
              } else {
                //  validation not ok
      
                $this->session->setFlashdata('login_error', $this->form_validation->getErrors());
              }
            }else if ($this->request->getPost("submit") == "login") {

                  // validation ok
                  //password
          
                  $this->form_validation->setRules(
                    [
                      'email' => [
                        'label' => 'Email',
                        'rules' => 'required|min_length[4]|max_length[39]'
                      ],
                      'password' => [
                        'label' => 'Password',
                        'rules' => 'required|min_length[4]|max_length[39]'
                      ]
                    ]
                  );
                  if ($this->form_validation->withRequest($this->request)->run()) {
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $user = $userModel->get_cipherpass($email);
          
                    if ($user >0 && $user['status'] == 1) {
                      if ($user['level'] >= 2) {
                        $this->session->setFlashdata('login_error', array("not_admin"=>"Anda bukan Administrator, silakan hubungi Administrator untuk meminta halaman login"));
                      }
                      if ($user['password'] != NULL || $user['password'] != '' ) {
          
          
                        if ($this->bcrypt->verify($password, $user['password'])) {
          
                          $data_user = [
                            'id' => $user['id'],
                            'nama_depan'=> $user['nama_depan'],
                            'level'=> $user['level'],
                            'nama_belakang'=> $user['nama_belakang'],
                            'name'=> $user['nama_depan']." ".$user['nama_belakang'],
                            'email'=> $user['email'],
                          ];
              
                          $userModel->where("email", $email);
                          $profile = $userModel->get()->getResultArray();
                          $this->session->set('profile', $profile);
                          $this->session->set('logged', true);
                          $this->session->set('auth', $data_user);
          
                          return redirect()->to('/dashboard');
                        }else{
                          $this->session->setFlashdata('login_error',  array("failed"=>"Login Failed: Incorrect username or password"));
                        }
                      }else{
                        $this->session->setFlashdata('login_error', array("failed"=>"Password tidak ada: Silakan login dengan google account"));
                      }
                    }else{
                      $this->session->setFlashdata('login_error', array("notActive"=>"User tidak ada/tidakaktif, silakan hubungi Administrator"));
                    }
                  } else {
                    //  validation not ok
                    $this->session->setFlashdata('login_error', $this->form_validation->getErrors());
                  }
          
                
              }

            //view login/signup
        if ($userModel->CountAllResults() > 0){
            return view('login/login');
        }else{
            return view('login/signup');

        }
      
    }
    public function logout()
  {
    Session()->destroy();
    return Redirect()->to(base_url('/'));
  }
}