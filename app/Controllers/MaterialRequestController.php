<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MaterialRequestController extends BaseController
{
  
    public function materialRequestList()
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();
        
        // Define the columns to select
        $select_columns = 'material_request.id,material_request.status, material_request.kode,material_request.created_at, material_request.remarks, material_request.created_at, proforma_invoice.invoice_number as pi';
        
        // Define the joins (you can add more joins as needed)
        $joins = [
            ['proforma_invoice', 'proforma_invoice.id = material_request.id_pi', 'left'],


        ];

        $where = ['material_request.id !=' => 0, 'material_request.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL, 
            'material_request.created_at',
            'material_request.kode',
            'proforma_invoice.invoice_number',
            'material_request.remarks',
            'material_request.status',
            'material_request.id',

        );
        $column_search = array(
            'material_request.kode', 
            'material_request.pi', 
        );
        $order = array('material_request.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('material_request', $select_columns, $joins, $column_order, $column_search, $order, $where);
        
        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id;
            $row[] = $lists->created_at;
            $row[] = $lists->kode;
            $row[] = $lists->pi;
            $row[] = $lists->remarks;
            $row[] = $lists->status;
// From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('material_request', $where),
            "recordsFiltered" => $serverside_model->count_filtered('material_request', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );

      //   return $this->response->setJSON($output);
      
        return json_encode($output);
    }
}
