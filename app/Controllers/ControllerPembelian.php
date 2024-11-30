<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MdlPembelian;
use App\Models\MdlPembelianDetail;
use App\Models\MdlCountry;
use App\Models\MdlSupplier;
use App\Models\MdlCurrency;
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
        $select_columns = 'pembelian.*, supplier.supplier_name';

        // Define joins
        $joins = [
            ['supplier', 'supplier.id = pembelian.id_supplier', 'left'],
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
            $row[] = $lists->posting;//8

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
function pembelianForm($idPembelian){
    $MdlPembelian = new MdlPembelian();
    $MdlPembelianDetail = new MdlPembelianDetail();
$dataPembelian = $MdlPembelian
                ->select('pembelian.*, supplier.supplier_name, currency.id as curr_id, currency.kode as curr_code, currency.nama as curr_name')
                ->join('supplier', 'supplier.id = pembelian.id_supplier')   
                ->join('currency', 'currency.id = supplier.id_currency')    
                ->where('pembelian.id', $idPembelian)->get()->getResultArray();
// $dataPembelianDetail = $MdlPembelianDetail
//                     ->select('materials.*')
//                     ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian")
//                     ->join("materials","materials.id = pembelian_detail.id_material")
//                     ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material")
//                     ->where('pembelian.id', $idPembelian)->find();
// var_dump($dataPembelianDetail);
// die();
$data['pembelian'] = $dataPembelian;
// $data['pembelianDetail'] = $dataPembelianDetail;
$data['content'] = view('admin/content/pembelian_form', $data);
return view('admin/index', $data);
}

    public function listdataPembelianDetail($idPembelian){
       $serverside_model = new \App\Models\MdlDatatableJoin();
        $request = \Config\Services::request();

        // Columns to select
        // $select_columns = 'supplier.supplier_name, pembelian.* ,pembelian_detail.*, materials.*, materials_detail.*, pembelian_detail.id as id_pembelian_detail';
         $select_columns =  'pembelian_detail.id as id_pembelian_detail,
                            pembelian_detail.id_material,
                            pembelian_detail.id_currency,
                            pembelian_detail.jumlah,
                            pembelian_detail.harga,
                            pembelian_detail.id_currency,
                            pembelian_detail.diskon1,
                            pembelian_detail.diskon2,
                            pembelian_detail.diskon3,
                            pembelian_detail.pajak,
                            pembelian_detail.potongan,
                            materials.kode as material_kode,
                            materials.name as material_name,
                            currency.kode as kode_currency,
                            currency.nama as nama_currency,
                            currency.rate';

        // Define joins
        $joins = [
            ["pembelian","pembelian.id = pembelian_detail.id_pembelian", 'left'],
            ['supplier', 'supplier.id = pembelian.id_supplier', 'left'],
            ["materials","materials.id = pembelian_detail.id_material", 'left'],
            ["currency","pembelian_detail.id_currency = currency.id", 'left'],
            ["materials_detail","materials_detail.material_id = pembelian_detail.id_material", 'left']
        ];

        $where = ['pembelian_detail.id !=' => 0, 'pembelian_detail.deleted_at' => NULL, 'pembelian_detail.id_pembelian'=>$idPembelian];

        // Columns for ordering and searching
        $column_order = [
            NULL,
           
        ];

        $column_search = [
            'pembelian.invoice',
 
        ];

        $order = ['pembelian_detail.id' => 'desc'];

        $list = $serverside_model->get_datatables('pembelian_detail', $select_columns, $joins, $column_order, $column_search, $order, $where);

        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no; //0
            $row[] = $lists->id_pembelian_detail; //1
            $row[] = $lists->material_kode; //2
            $row[] = $lists->material_name;//3
            $row[] = $lists->harga;//4
            $row[] = $lists->kode_currency;//5
            $row[] = $lists->nama_currency;//6
            $row[] = $lists->rate;//7
            $row[] = $lists->id_material;//8
            $row[] = $lists->jumlah;//9
            $row[] = $lists->diskon1;//10
            $row[] = $lists->diskon2;//11
            $row[] = $lists->diskon3;//12
            $row[] = $lists->pajak;//13
            $row[] = $lists->potongan;//14

        $data[] = $row;

                  }

                  $output = [
                    "draw" => $request->getPost("draw"),
                    "recordsTotal" => $serverside_model->count_all('pembelian_detail', $where),
                    "recordsFiltered" => $serverside_model->count_filtered('pembelian_detail', $select_columns, $joins, $column_order, $column_search, $order, $where),
                    "data" => $data,
                ];

                return $this->response->setJSON($output);  
    }

    public function getSupplierData($id)
    {

        $supplierModel = new MdlSupplier();
         $supplier = $supplierModel
                        ->select('supplier.*, currency.kode as currency_code, currency.nama as currency_name, country_data.country_name, country_data.code2 as country_code')
                        ->join('currency','currency.id = supplier.id_currency ')
                        ->join('country_data','country_data.id_country = supplier.id_country')
                        ->where('supplier.id', $id)
                        ->get()->getResultArray();
        return $this->response->setJSON($supplier[0]);
    }

    // Mengambil data negara
    public function getCountryData()
    {
        $mdl = new MdlCountry();
        $query = $mdl->get();
        $countries = $query->getResultArray();

        return $this->response->setJSON($countries);
    }

    // Mengambil data mata uang
    public function getCurrencyData()
    {
        $mdl = new MdlCurrency();
        $query = $builder->get();
        $currencies = $query->getResultArray();

        return $this->response->setJSON($currencies);
    }
        public function getSupplierDataByPurchase($id_pembelian)
    {
        // Ambil data pembelian berdasarkan ID
        $pembelianModel = new MdlPembelian();
        $pembelian = $pembelianModel->find($id_pembelian);

        if ($pembelian) {
            $id_supplier = $pembelian['id_supplier'];

            // Ambil data supplier dari model (misal, Anda memiliki model Supplier)
            $supplierModel = new MdlSupplier();

            $supplier = $supplierModel
                        ->select('supplier.*, currency.kode as currency_code, currency.nama as currency_name, country_data.country_name, country_data.code2 as country_code')
                        ->join('currency','currency.id = supplier.id_currency ')
                        ->join('country_data','country_data.id_country = supplier.id_country')
                        ->where('supplier.id', $id_supplier)
                        ->get()->getResultArray();
                       
       

            return $this->response->setJSON($supplier[0]);
        } else {
            return $this->response->setJSON([]);
        }
    }
    public function getSupplierList(){
        $model = new MdlSupplier();
        $supplier = $model->findAll();

        return $this->response->setJSON($supplier);
    }
    public function updateSupplier($id){
    $invoice = $_POST['invoice'];
    $id_supplier = $_POST['supplier'];
    $pajak = $_POST['pajak'];

    $data = array('id_supplier' =>$id_supplier ,
                  'invoice' =>$invoice ,
                  'pajak' =>$pajak 

     );
    $MdlPembelian = new MdlPembelian();
    $MdlPembelianDetail = new MdlPembelianDetail();
    $MdlPembelian->set($data)->where('id',$id)->update();

    if ($MdlPembelian->affectedRows()!== 0) {
        $pembelian = $MdlPembelian->find($id);

        if ($pembelian) {
            $id_supplier = $pembelian['id_supplier'];

            $supplierModel = new MdlSupplier();

            $supplier = $supplierModel
                        ->select('currency.id as currency_id')
                        ->join('currency','currency.id = supplier.id_currency ')
                        ->where('supplier.id', $id_supplier)
                        ->get()->getResultArray();
                       
       

            
            $MdlPembelianDetail->set(array('id_currency'=>$supplier[0]['currency_id'], 'pajak'=>$pajak))->where('id_pembelian',$id)->update();

        }
        return $this->response->setJSON([
        'status' => 'success',
        'message' => 'successfully',
    ]);
    }


    }

    public function addInvoice(){
    $data['id_supplier'] = $_POST['supplier'];
    $data['invoice'] = $_POST['invoice'];
    $data['tanggal_nota'] = $_POST['tanggal_nota'];
    $data['pajak'] = $_POST['pajak'];
    $data['status_pembayaran'] = 0;
    $data['posting'] = 0;
    $mdl = new MdlPembelian();
       if ($mdl->insert($data)) {
        // Jika berhasil
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Invoice Berhasil Ditambahkan'
                    ]);
                } else {
        // Jika gagal
                    $errorMessage = $mdlCustomer->errors() ? $mdlCustomer->errors() : 'Gagal menambahkan Invoice karena kesalahan internal.';

                    return $this->response->setJSON([
                        'status' => false,
                        'message' => $errorMessage
                    ]);
                }
    }
    public function deleteinvoice(){
        $id = $_POST['id'];
        $mdl = new MdlPembelian();

        $mdl->where(array('id' =>$id , 'posting'=>0));
        $mdl->delete();

        // Cek apakah data benar-benar dihapus
        if ($mdl->affectedRows() > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Tidak dapat dihapus, status terposting'
            ]);
        }
    }
    public function addMaterial(){
        $mdl = new MdlPembelianDetail();
        $data['id_material'] = $_POST['materialCode'];
        $data['jumlah'] = $_POST['materialQty'];
        $data['harga'] = $_POST['harga'];
        $data['id_currency'] = $_POST['id_currency'];
        $data['diskon1'] = $_POST['disc1'];
        $data['diskon2'] = $_POST['disc2'];
        $data['diskon3'] = $_POST['disc3'];
        $data['potongan'] = $_POST['potongan'];
        $data['pajak'] = $_POST['pajak'];
        $data['id_pembelian'] = $_POST['id_pembelian'];
        var_dump($data);
        die();
           if ($mdl->insert($data)) {
        // Jika berhasil
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Invoice Berhasil Ditambahkan'
                    ]);
                } else {
        // Jika gagal
                    $errorMessage = $mdlCustomer->errors() ? $mdlCustomer->errors() : 'Gagal menambahkan Invoice karena kesalahan internal.';

                    return $this->response->setJSON([
                        'status' => false,
                        'message' => $errorMessage
                    ]);
                }
    }
}
