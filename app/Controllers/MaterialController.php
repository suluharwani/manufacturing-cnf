<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MaterialController extends BaseController
{
    public function index()
    {
        //
    }
    public function listdataMaterial(){
        $this->access('operator');
        $serverside_model = new \App\Models\Mdl_datatables();
        $request = \Config\Services::request();
        $list_data = $serverside_model;
        $where = ['id !=' => 0, 'deleted_at'=>NULL];
                //Column Order Harus Sesuai Urutan Kolom Pada Header Tabel di bagian View
                //Awali nama kolom tabel dengan nama tabel->tanda titik->nama kolom seperti pengguna.nama
        $column_order = array(NULL,'product.nama','product.status','product.id');
        $column_search = array('product.nama','product.judul');
        $order = array('product.id' => 'desc');
        $list = $list_data->get_datatables('product', $column_order, $column_search, $order, $where);
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
          $no++;
          $row    = array();
          $row[] = $no;
          $row[] = $lists->id;
          $row[] = $lists->nama;
          $row[] = $lists->status;
          $data[] = $row;
      }
      $output = array(
          "draw" => $request->getPost("draw"),
          "recordsTotal" => $list_data->count_all('product', $where),
          "recordsFiltered" => $list_data->count_filtered('product', $column_order, $column_search, $order, $where),
          "data" => $data,
      );
      
      return json_encode($output);
      }
}
