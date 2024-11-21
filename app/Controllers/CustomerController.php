<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MdlCustomer;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties;

class CustomerController extends BaseController
{
    protected $changelog;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = session();
        helper(['form', 'url']);
        $this->form_validation = \Config\Services::validation();
        $this->changelog = new \App\Controllers\Changelog();

        // Check if session is active
        $check = new \App\Controllers\CheckAccess();
        $check->logged();
    }

    // Load Customer Data

    // List Customer Data (Server-side)
    public function listdataCustomerJoin()
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Columns to select
        $select_columns = 'customer.*, currency.nama as currency_name';

        // Define joins
        $joins = [
            ['currency', 'currency.id = customer.id_currency', 'left']
        ];

        $where = ['customer.id !=' => 0, 'customer.deleted_at' => NULL];

        // Columns for ordering and searching
        $column_order = [
            NULL,
            'customer.code',
            'customer.customer_name',
            'customer.status',
            'customer.id',
        ];

        $column_search = [
            'customer.code',
            'customer.customer_name',
            'customer.contact_name',
            'customer.contact_email',
        ];

        $order = ['customer.id' => 'desc'];

        $list = $serverside_model->get_datatables('customer', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no; //0
            $row[] = $lists->status; //1
            $row[] = $lists->id; //2
            $row[] = $lists->code;//3
            $row[] = $lists->customer_name;//4
            $row[] = $lists->contact_name;//5
            $row[] = $lists->contact_email;//6
            $row[] = $lists->contact_phone;//7
            $row[] = $lists->currency_name;//8
            $row[] = "<img src='" . base_url('assets/upload/1000/' . $lists->logo_url) . "' alt='" . $lists->customer_name . "' style='height: 50px;'>";//9
            $row[] = "<button class='btn btn-info btn-sm viewCustomer' data-id='{$lists->id}'>View</button>
            <button class='btn btn-success btn-sm logoCustomer' data-logo='{$lists->logo_url}' data-id='{$lists->id}' data-name = '{$lists->customer_name}'>Logo</button>
            <button class='btn btn-warning btn-sm editCustomer' data-id='{$lists->id}'>Edit</button>
                      <button class='btn btn-danger btn-sm deleteCustomer' data-id='{$lists->id}'>Delete</button>";//10
                      $data[] = $row;

                  }

                  $output = [
                    "draw" => $request->getPost("draw"),
                    "recordsTotal" => $serverside_model->count_all('customer', $where),
                    "recordsFiltered" => $serverside_model->count_filtered('customer', $select_columns, $joins, $column_order, $column_search, $order, $where),
                    "data" => $data,
                ];

                return $this->response->setJSON($output);
            }

    // Create customer
            public function create()
            {
                $data = $this->request->getPost();

                $validationRules = [
                    'customer_name' => 'required',
                    'address' => 'required',
                    'id_currency' => 'required|numeric',
                ];

    // Validasi data
                if (!$this->validate($validationRules)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => $this->validator->getErrors()
                    ]);
                }

                $mdlCustomer = new MdlCustomer();

    // Proses insert data
                if ($mdlCustomer->insert($data)) {
        // Jika berhasil
                    return $this->response->setJSON([
                        'status' => true,
                        'message' => 'Customer berhasil ditambahkan.'
                    ]);
                } else {
        // Jika gagal
                    $errorMessage = $mdlCustomer->errors() ? $mdlCustomer->errors() : 'Gagal menambahkan customer karena kesalahan internal.';

                    return $this->response->setJSON([
                        'status' => false,
                        'message' => $errorMessage
                    ]);
                }
            }

    // Update Customer
            public function update($id)
            {
                $data = $this->request->getPost();

                $validationRules = [
                    'customer_name' => 'required',
                    'address' => 'required',
                    'id_currency' => 'required|numeric',
                ];

                if (!$this->validate($validationRules)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => $this->validator->getErrors()
                    ]);
                }


                $mdlCustomer = new MdlCustomer();
                $mdlCustomer->update($id, $data);

                return $this->response->setJSON([
                    'status' => $this->db->getLastQuery()->getQuery(),
                    'message' => 'Customer berhasil diperbarui.'
                ]);
            }

    // Delete Customer
            public function delete($id)
            {
                $mdlCustomer = new MdlCustomer();
                $mdlCustomer->delete($id);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Customer berhasil dihapus.'
                ]);
            }

            function upload(){
              $mdl = new \App\Models\MdlCustomer();
              helper(['form', 'url']);

              $validateImage =  $this->form_validation->setRules([
                  'file' => [
                    'label'  => 'File',
                    'rules'  => 'max_size[file, 40960000]|mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
                    'errors' => [
                      'max_size' => 'Ukuran terlalu besar',
                      'mime_in' => 'Extensi harus jpg/jpeg/gif/png ',
                  ],
              ],
          ]);
      // exit();
              if ($validateImage->withRequest($this->request)->run()) {
                  $imageFile = $this->request->getFile('file');

                  $fileName = $imageFile->getRandomName();
                  $filepath =  FCPATH .'assets/upload/image';
          // manipulatoin
                  $image = \Config\Services::image();
          //thumbnail
                  $image->withFile($imageFile)
                  ->resize(100, 100, true, 'height')
                  ->save( FCPATH .'assets/upload/thumb/'. $fileName);
                  $image->withFile($imageFile)
                  ->resize(1000, 1000, true, 'height')
                  ->save(FCPATH .'assets/upload/1000/'. $fileName);
          //quality
                  $image->withFile($imageFile)
                  ->withResource()
                  ->save(FCPATH .'assets/upload/low/'. $fileName, 80);
            //manipulation
            //original image
                  $imageFile->move('assets/upload/image', $fileName);
            // original image
                  $data = [
                    'img_name' => $imageFile->getClientName(),
                    'file'  => $imageFile->getClientMimeType()
                ];
          // $save = $builder->insert($data);

                if (isset($_POST['id'])) {
                    $param = array('id'=>$_POST['id']);

                    $oldRecord = $mdl->where($param)->get()->getResultArray()[0];
                    if ($oldRecord['logo_url']!='' || $oldRecord['logo_url']!=null) {
            // code...

                        $OldFilepath = [];
                        $OldFilepath[0] = FCPATH . "assets/upload/1000/{$oldRecord['logo_url']}";
                        $OldFilepath[1] = FCPATH . "assets/upload/image/{$oldRecord['logo_url']}";
                        $OldFilepath[2] = FCPATH . "assets/upload/low/{$oldRecord['logo_url']}";
                        $OldFilepath[3] = FCPATH . "assets/upload/thumb/{$oldRecord['logo_url']}";
                        if (file_exists($OldFilepath[0])) {
                          unlink( $OldFilepath[0]); 
                      }
                      if (file_exists($OldFilepath[1])) {
                          unlink( $OldFilepath[1]); 
                      }
                      if (file_exists($OldFilepath[2])) {
                          unlink( $OldFilepath[2]); 
                      }
                      if (file_exists($OldFilepath[3])) {
                          unlink( $OldFilepath[3]); 
                      }
                  }
                  $mdl->set('logo_url',$fileName);
                  $mdl->where($param);
                  $mdl->update();
                  if ($mdl->affectedRows()>0) {
                   $response = [
                    'success' => true,
                    'picture' => $fileName,
                    'msg' => "Gambar: {$imageFile->getClientName()} berhasil diupload"
                ];
                $riwayat = "Mengubah Logo Customer ID : {$param['id']}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 200 OK');
            }else {
              header('HTTP/1.1 500 Internal Server Error');
              header('Content-Type: application/json; charset=UTF-8');
              die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
          }
      }
  }else{
      $response = [
        'success' => false,
        'data' => '',
        'msg' => $validateImage->getError('file')
    ];
}


return $this->response->setJSON($response);
}
public function get($id)
{
    try {
            // Load model untuk mengambil data Customer
        $mdlCustomer = new MdlCustomer();

            // Ambil semua data Customer
        $customers = $mdlCustomer->select('*, customer.id as cus_id')
                          ->join('country_data','country_data.id_country = customer.id_country','left')
                          ->join('currency','currency.id = customer.id_currency','left')
                          ->where('customer.id', $id)->find();

            // Kembalikan data dalam format JSON
        return $this->response->setJSON($customers);
    } catch (\Exception $e) {
            // Tangani error jika terjadi
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage(),
        ])->setStatusCode(500);
    }
}

}
