<?php

namespace App\Controllers;
use AllowDynamicProperties;

class LoginValidation extends BaseController
{
    public function index()
    {
     
    }
    public function validate_user(){
      $Mdl_user = new \App\Models\MdlUser();
      if (isset($_SESSION['auth']) && $this->_make_sure_is_login()) {
        if ($Mdl_user->check_admin_active($_SESSION['auth']['email'])) {
          return TRUE;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
    public function validate_client(){
      $Mdl_user = new \App\Models\MdlUser();
      if (isset($_SESSION['auth']) && $this->_make_sure_is_client_login()) {
        if ($Mdl_user->check_client_active($_SESSION['auth']['email'])) {
          return TRUE;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
    function _make_sure_is_client_login(){
      if (isset($_SESSION['login_data_client'])) {
        return TRUE;
      }else{
        return FALSE;
      }
    }
    function _make_sure_is_login(){
      if (isset($_SESSION['login_data'])) {
        return TRUE;
      }else{
        return FALSE;
      }
    }
    // function recaptchaValidation($token_generate){
    //  $url = "https://www.google.com/recaptcha/api/siteverify";
    //  $secret = $_ENV['recaptchaSecretKey'];
    // //  $response = $this->request->getPost("token_generate");
    //  $request = file_get_contents($url.'?secret='.$secret.'&response='.$token_generate);
    //  return json_decode($request);
    // }
}
