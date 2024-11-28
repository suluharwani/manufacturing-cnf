<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MdlPembelian;
use App\Models\MdlPembelianDetail;
class ControllerPembelian extends BaseController
{
    public function index()
    {
        //
    }
    public function listdataPembelian(){
       $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Columns to select
        $select_columns = 'pembelian.*, supplier.supplier_name, sum(pembelian_detail.jumlah * pembelian_detail.harga) as total_harga';

        // Define joins
        $joins = [
            ['supplier', 'supplier.id = pembelian.id_supplier', 'left'],
            ['pembelian_detail', 'pembelian_detail.id_pembelian = pembelian.id', 'left']
        ];

        $where = ['pembelian.id !=' => 0, 'pembelian.deleted_at' => NULL];

        // Columns for ordering and searching
        $column_order = [
            NULL,
            'pembelian.tanggal_nota',
            'pembelian.invoice',
            'pembelian.id_supplier',
           
        ];

        $column_search = [
            'pembelian.invoice',
            'supplier.supplier_name'
        ];

        $order = ['pembelian.id' => 'desc'];

        $list = $serverside_model->get_datatables('pembelian', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no; //0
            $row[] = $lists->id; //1
            $row[] = $lists->id_supplier; //2
            $row[] = $lists->invoice;//3
            $row[] = $lists->tanggal_nota;//4
            $row[] = $lists->tanggal_jatuh_tempo;//5
            $row[] = $lists->status_pembayaran;//6
            $row[] = $lists->supplier_name;//7
            $row[] = $lists->total_harga;//8

        $data[] = $row;

                  }

                  $output = [
                    "draw" => $request->getPost("draw"),
                    "recordsTotal" => $serverside_model->count_all('pembelian', $where),
                    "recordsFiltered" => $serverside_model->count_filtered('pembelian', $select_columns, $joins, $column_order, $column_search, $order, $where),
                    "data" => $data,
                ];

                return $this->response->setJSON($output);  
    }
}
