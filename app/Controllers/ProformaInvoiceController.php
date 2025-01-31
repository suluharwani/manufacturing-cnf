<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceDetail;
use App\Models\MdlCustomer;
use Dompdf\Dompdf;
use Dompdf\Options;
class ProformaInvoiceController extends BaseController
{
    protected $changelog;
    public function __construct()
    {
        //   parent::__construct();
        $this->db = \Config\Database::connect();
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
        $this->proformaInvoiceDetailModel = new ProformaInvoiceDetail();

    }
    public function pi($id)
    {
        $Mdl = new ProformaInvoice();
        $MdlDetail = new ProformaInvoiceDetail();
        $dataPembelian = $Mdl
            ->select('country_data.*, proforma_invoice.*, customer.id_currency as curr_id, currency.kode as curr_code, currency.nama as curr_name, customer.customer_name')
            ->join('customer', 'customer.id = proforma_invoice.customer_id', 'left')
            ->join('currency', 'currency.id = customer.id_currency', 'left')
            ->join('country_data', 'country_data.id_country = customer.id_country', 'left')
            ->where('proforma_invoice.id', $id)->get()->getResultArray();
        // $dataPembelianDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materialscountry_datamaterials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataPembelianDetail);
// die();
        $data['pi'] = $dataPembelian;

        // var_dump($data['pi']);
        // die();
        $data['content'] = view('admin/content/form_pi', $data);
        return view('admin/index', $data);
    }
    public function listdataPi($id)
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'proforma_invoice.status as status,proforma_invoice.invoice_number as pi, proforma_invoice_details.*, proforma_invoice_details.id as det_id,product.nama as nama, product.kode as kode,product.id as id_product';

        // Define the joins (you can add more joins as needed)
        $joins = [
            ['product', 'product.id = proforma_invoice_details.id_product', 'left'],
            ['proforma_invoice', 'proforma_invoice.id = proforma_invoice_details.invoice_id', 'left'],
        ];

        $where = ['proforma_invoice_details.invoice_id ' => $id, 'proforma_invoice_details.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL,
            'product.nama',
            'product.kode',
            'id_product',
            'id_product',
            'id_product',
            'id_product',
            'id_product',
        );
        $column_search = array(
            'product.nama',
            'product.kode',

        );
        $order = array('proforma_invoice_details.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('proforma_invoice_details', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->id_product;
            $row[] = $lists->nama;
            $row[] = $lists->kode;
            $row[] = $lists->quantity;
            $row[] = $lists->unit_price;
            $row[] = $lists->total_price;
            $row[] = $lists->det_id;
            $row[] = $lists->pi;
            $row[] = $lists->status;


            // From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('proforma_invoice_details', $where),
            "recordsFiltered" => $serverside_model->count_filtered('proforma_invoice_details', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );


        return $this->response->setJSON($output);
    }
    public function listdata()
{
    $serverside_model = new \App\Models\MdlDatatableJoin();
    $request = \Config\Services::request();

    // Define the columns to select
    $select_columns = 'proforma_invoice.*, customer.customer_name, customer.code as cus_code, customer.address as customer_address';

    // Define the joins (you can add more joins as needed)
    $joins = [
        ['customer', 'customer.id = proforma_invoice.customer_id', 'left'],
    ];

    $where = ['proforma_invoice.deleted_at' => NULL];

    // Column Order Must Match Header Columns in View
    $column_order = array(
        NULL,
        'proforma_invoice.invoice_number',
        'proforma_invoice.invoice_date',
        'customer.customer_name',
        'proforma_invoice.id',
    );

    $column_search = array(
        'customer.customer_name',
        'proforma_invoice.invoice_number',
    );

    $order = array('proforma_invoice.id' => 'desc');

    // Call the method to get data with dynamic joins and select fields
    $list = $serverside_model->get_datatables('proforma_invoice', $select_columns, $joins, $column_order, $column_search, $order, $where);

    $start = $request->getPost("start");
    $no = $start;

    // Use array_map to transform the data
    $data = array_map(function ($lists) use (&$no) {
        $no++;
        return [
            $no,
            $lists->id,
            $lists->invoice_number,
            $lists->invoice_date,
            $lists->customer_name,
        ];
    }, $list);

    $output = array(
        "draw" => $request->getPost("draw"),
        "recordsTotal" => $serverside_model->count_all('proforma_invoice', $where),
        "recordsFiltered" => $serverside_model->count_filtered('proforma_invoice', $select_columns, $joins, $column_order, $column_search, $order, $where),
        "data" => $data,
    );

    return json_encode($output);
}

    function getCustomerList()
    {
        $model = new MdlCustomer();
        $customer = $model->findAll();

        return $this->response->setJSON($customer);

    }
    public function add()
    {
        $mdl = new ProformaInvoice();
        $mdl->insert($_POST);

        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Menambahkan Proforma Invoice ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function addProduct()
    {
        $mdl = new ProformaInvoiceDetail();
        $mdl->insert($_POST);

        if ($mdl->affectedRows() !== 0) {
            $riwayat = "Menambahkan Proforma Invoice ";
            $this->changelog->riwayat($riwayat);
            header('HTTP/1.1 200 OK');
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
        }
    }
    public function get_list()
    {
        $mdl = new ProformaInvoice();
        $data = $mdl->orderBy('id', 'desc')->get()->getResultArray();
        return json_encode($data);
    }

    public function get_list_json()
    {
        $mdl = new ProformaInvoice();
        $pi = $mdl->orderBy('id', 'DESC')->findAll();

        return $this->response->setJSON($pi);
    }

    public function getProduct($id)
    {
        // Fetch product data from the model
        $product = $this->proformaInvoiceDetailModel->getProductById($id);

        if ($product) {
            // Return success response with product data
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $product
            ]);
        } else {
            // Return error if product not found
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
    }

    // Update product details
    public function updateProduct($id)
    {
        $data = [
            'invoice_id' => $this->request->getPost('invoice_id'),
            'id_product' => $this->request->getPost('id_product'),
            'id_currency' => $this->request->getPost('id_currency'),
            'item_description' => $this->request->getPost('item_description'),
            'hs_code' => $this->request->getPost('hs_code'),
            'quantity' => $this->request->getPost('quantity'),
            'unit' => $this->request->getPost('unit'),
            'unit_price' => $this->request->getPost('unit_price'),
            'total_price' => $this->request->getPost('total_price'),
            'remarks' => $this->request->getPost('remarks'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updateResult = $this->proformaInvoiceDetailModel->updateProduct($id, $data);

        if ($updateResult) {
            // Return success response
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product updated successfully'
            ]);
        } else {
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update product'
            ]);
        }
    }

    // Delete product
    public function deleteProduct($id)
    {
        $deleteResult = $this->proformaInvoiceDetailModel->deleteProduct($id);

        if ($deleteResult) {
            // Return success response
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);
        } else {
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete product'
            ]);
        }
    }
    public function piDoc($id)
    {
        $Mdl = new ProformaInvoice();
        $MdlDetail = new ProformaInvoiceDetail();
        $dataPembelian = $Mdl
            ->select('country_data.*, proforma_invoice.*, customer.id_currency as curr_id, currency.kode as curr_code, currency.nama as curr_name, customer.customer_name')
            ->join('customer', 'customer.id = proforma_invoice.customer_id', 'left')
            ->join('currency', 'currency.id = customer.id_currency', 'left')
            ->join('country_data', 'country_data.id_country = customer.id_country', 'left')
            ->where('proforma_invoice.id', $id)->get()->getResultArray();
        // $dataPembelianDetail = $MdlPembelianDetail
        //                     ->select('materials.*')
        //                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
        //                     ->join("materialscountry_datamaterials.id = pembelian_detail.id_material")
        //                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
        //                     ->where('pembelian.id', $idPembelian)->find();
        // var_dump($dataPembelianDetail);
        // die();
        $data['pi'] = $dataPembelian;

        // var_dump($data['pi']);
        // die();
        $data['content'] = view('admin/content/form_pi_doc', $data);
        return view('admin/index', $data);
    }
    public function upload()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,1024]|mime_in[file,application/pdf,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Please select a file to upload.',
                    'max_size' => 'File size must be less than 1MB.',
                    'mime_in' => 'Only PDF, JPG, JPEG, and PNG files are allowed.'
                ]
            ],
            'document_name' => [
                'label' => 'Document Name',
                'rules' => 'required',
            ],
            'document_code' => [
                'label' => 'Document Code',
                'rules' => 'required',
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Get the validation errors
            $errors = $validation->getErrors();

            // Format the errors into a string
            $errorMessage = implode(", ", $errors);

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMessage
            ]);
        }

        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);

            $model = new \App\Models\MdlDocumentPi();
            $data = [
                'id_pi' => $this->request->getPost('id_pi'),  // Assuming you have an id_pi field in your form
                // 'document' => $file->getName(),
                'file_path' => 'uploads/' . $newName,
                'document' => $this->request->getPost('document_name'),
                'code' => $this->request->getPost('document_code')
            ];

            if ($model->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'File uploaded successfully.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save file information to the database.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File upload failed.'
            ]);
        }
    }
    public function file($id)
    {
        $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Define the columns to select
        $select_columns = 'document_pi.*';

        // Define the joins (you can add more joins as needed)
        $joins = [
        ];

        $where = ['document_pi.id_pi ' => $id, 'document_pi.deleted_at' => NULL];

        // Column Order Must Match Header Columns in View
        $column_order = array(
            NULL,
            'document_pi.code',
            'document_pi.document',
            'path_file',
            'id',
        );
        $column_search = array(
            'document_pi.code',
            'document_pi.document',

        );
        $order = array('document_pi.id' => 'desc');

        // Call the method to get data with dynamic joins and select fields
        $list = $serverside_model->get_datatables('document_pi', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = array();
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lists->code;
            $row[] = $lists->document;
            $row[] = $lists->file_path;
            $row[] = $lists->id;
            $row[] = $lists->created_at;


            // From joined suppliers table
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $serverside_model->count_all('document_pi', $where),
            "recordsFiltered" => $serverside_model->count_filtered('document_pi', $select_columns, $joins, $column_order, $column_search, $order, $where),
            "data" => $data,
        );


        return $this->response->setJSON($output);
    }
    public function updateDocument()
    {
        $id = $this->request->getPost('id');
        $documentName = $this->request->getPost('document_name');
        $documentCode = $this->request->getPost('document_code');
    
        $model = new \App\Models\MdlDocumentPi();
    
        // Validate input
        if (empty($documentName) || empty($documentCode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Document name and code are required.'
            ]);
        }
    
        // Update the document
        $data = [
            'document' => $documentName,
            'code' => $documentCode,
            'updated_at' => date('Y-m-d H:i:s') // Update the timestamp
        ];
    
        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Document updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update document.'
            ]);
        }
    }
    
    public function deleteinvoice($id){
        $model = new ProformaInvoice();
        if ($model->delete($id)){
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Document deleted successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete document.'
            ]);
        }

    }
    public function delete($id)
    {
        $model = new \App\Models\MdlDocumentPi();
    
        // Find the document by ID
        $document = $model->find($id);
    
        if ($document) {
            // Get the file path
            $filePath = FCPATH . $document['file_path']; // FCPATH is the path to the public folder
    
            // Delete the document from the database
            if ($model->delete($id)) {
                // Check if the file exists and delete it
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file
                }
    
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Document deleted successfully.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete document from the database.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Document not found.'
            ]);
        }
    }
    
public function getDocumentDetails()
{
    $id = $this->request->getGet('id');
    $model = new \App\Models\MdlDocumentPi();
    $document = $model->find($id);

    if ($document) {
        return $this->response->setJSON([
            'status' => 'success',
            'document' => $document
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Document not found.'
        ]);
    }
}
public function finish($id){
    $productionModel = new \App\Models\MdlProductionProgress();

        if ($productionModel->finish($id)) {
            return $this->response->setStatusCode(200)->setJSON(['status'=> "success",'message' => 'Production progress soft deleted and proforma invoice status updated successfully.']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Error: Failed to update production progress and proforma invoice status.']);
        }
}
public function batalFinish($id)
{
    $productionModel = new \App\Models\MdlProductionProgress();


    if ($productionModel->batalFinish($id)) {
        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Production progress reset and proforma invoice status reset successfully.']);
    } else {
        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error: Failed to reset production progress and proforma invoice status.']);
    }
}

public function print($id)
{
    $invoiceModel = new ProformaInvoice();
    $data = $invoiceModel->getInvoiceData($id);

    // Load the view and pass the data
    $html = view('admin/content/report/invoice', $data);

    // Initialize Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    // Load HTML content
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream("invoice_{$data['invoice']['invoice_number']}.pdf", ["Attachment" => false]);
}
public function printDeliveryNote($id)
    {
        $deliveryNoteModel = new ProformaInvoice();
        $data = $deliveryNoteModel->getDeliveryNoteData($id);

        // Load the view and pass the data
        $html = view('admin/content/report/deliveryNote', $data);


        // Initialize Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Load HTML content
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("surat_jalan_{$data['invoice']['invoice_number']}.pdf", ["Attachment" => false]);
    }

}