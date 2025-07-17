<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MdlPembelian;
use App\Models\MdlPembelianDetail;
use App\Models\MdlCountry;
use App\Models\MdlSupplier;
use App\Models\MdlCurrency;
use App\Models\MdlStock;
use App\Models\MdlPurchaseOrder;
use App\Models\MdlPurchaseOrderList;
use Dompdf\Dompdf;
use Dompdf\Options;
class ControllerPembelian extends BaseController
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
                ->select('purchase_order.code as po,pembelian.*, supplier.supplier_name, currency.id as curr_id, currency.kode as curr_code, currency.nama as curr_name')
                ->join('supplier', 'supplier.id = pembelian.id_supplier','left')   
                ->join('currency', 'currency.id = supplier.id_currency','left')    
                ->join('purchase_order', 'purchase_order.id = pembelian.id_po', 'left')    
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
                            currency.rate,
                            pembelian.posting';

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
            $row[] = $lists->posting;//15

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
    $data['document'] = $_POST['document'];
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
        // $data['diskon1'] = $_POST['disc1'];
        // $data['diskon2'] = $_POST['disc2'];
        // $data['diskon3'] = $_POST['disc3'];
        // $data['potongan'] = $_POST['potongan'];
        $data['pajak'] = $_POST['pajak'];
        $data['id_pembelian'] = $_POST['id_pembelian'];

        $mdl->insert($data);
           if ($mdl->affectedRows()!=0) {
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
 public function updateMaterial($id)
    {
        $MdlPembelianDetail = new MdlPembelianDetail();

        $input = $this->request->getPost();

        if (!$this->validate([
            'materialCode' => 'required',
            'materialQty' => 'required|numeric',
            'harga' => 'required|numeric',
            'id_currency' => 'required|numeric',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'material_code' => $input['materialCode'],
            'material_qty' => $input['materialQty'],
            'harga' => $input['harga'],
            'id_currency' => $input['id_currency'],
            'disc1' => $input['disc1'],
            'disc2' => $input['disc2'],
            'disc3' => $input['disc3'],
            'potongan' => $input['potongan'],
            'pajak' => $input['pajak'],
        ];

        $updated = $MdlPembelianDetail->update($id, $data);

        if ($updated) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Material updated successfully!'
            ]);
        } else {
            return $this->fail('Failed to update material.');
        }
    }
    // Fungsi untuk menghapus material
    public function delete($id)
    {
        $MdlPembelianDetail = new MdlPembelianDetail();
        // if (!$MdlPembelianDetail->find($id)) {
        //      return $this->response->setJSON([
        //         'status' => false,
        //         'message' => 'Material not deleted!'
        //     ]);
        // }

        if ($MdlPembelianDetail->delete($id)) {
            return  $this->response->setJSON([
                'status' => 'success',
                'message' => 'Material deleted successfully!'
            ]);
        } else {
             return  $this->response->setJSON([
                'status' => false,
                'message' => 'Delete failed!'
            ]);
        }
    }
    function get($id){
        $MdlPembelianDetail = new MdlPembelianDetail();
        $data = $MdlPembelianDetail->select('pembelian_detail.id as id_pembelian_detail,
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
                            currency.rate')
        ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian", 'left')
        ->join('supplier', 'supplier.id = pembelian.id_supplier', 'left')
        ->join("materials","materials.id = pembelian_detail.id_material", 'left')
        ->join("currency","pembelian_detail.id_currency = currency.id", 'left')
        ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material", 'left')
        ->where("pembelian_detail.id ", $id)->get()->getResultArray();
        return json_encode($data[0]);
    }
public function unposting()
{
    $id = $_POST['id'];
    $MdlPembelianDetail = new MdlPembelianDetail();
    $MdlPembelian = new MdlPembelian();
    $MdlStock = new MdlStock();

    // Ambil data pembelian detail berdasarkan id_pembelian
    $data = $MdlPembelianDetail
                ->where('id_pembelian', $id)
                ->findAll();

    // Perulangan untuk setiap detail pembelian
    foreach ($data as $detail) {
        // Mengurangi stok yang telah ditambahkan saat posting
        $existingStock = $MdlStock->where('id_material', $detail['id_material'])->first();

        if ($existingStock) {
            // Kurangi stok masuk sesuai jumlah yang dibeli
            $existingStock['stock_masuk'] -= $detail['jumlah'];

            // Update record stock setelah dikurangi
            $MdlStock->update($existingStock['id'], $existingStock);
        }

        // Tambahkan logika lain jika perlu untuk mengurangi data terkait lainnya
    }

    // Setelah selesai, update status pembelian menjadi 0 (unposted)
    $MdlPembelian->update($id, ['posting' => 0]);
    
    if ($MdlPembelian->affectedRows() !== 0) {
        $riwayat = "Membatalkan Pembelian id: {$id}";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}
    public function posting(){
        $id = $_POST['id'];
        $MdlPembelianDetail = new MdlPembelianDetail();
        $MdlPembelian = new MdlPembelian();
        $MdlStock = new MdlStock();

        // Ambil data pembelian detail berdasarkan id_pembelian
    $data = $MdlPembelianDetail
                ->where('id_pembelian', $id)
                ->findAll();
    
    // Perulangan untuk setiap detail pembelian
    foreach ($data as $detail) {
              $hargaDasar = $detail['harga'];
      $diskon1 = $detail['diskon1'] || 0;  // Jika diskon1 kosong, anggap 0
      $diskon2 = $detail['diskon2'] || 0;  // Jika diskon2 kosong, anggap 0
      $diskon3 = $detail['diskon3'] || 0;  // Jika diskon3 kosong, anggap 0
      $potongan = $detail['potongan'] || 0;  // Jika potongan kosong, anggap 0
      $pajak = $detail['pajak'] || 0;     // Jika pajak kosong, anggap 0

      // Menghitung harga setelah diskon bertingkat
      $hargaSetelahDiskon = $hargaDasar - ($hargaDasar * ($diskon1 / 100)) - ($hargaDasar * ($diskon2 / 100)) - ($hargaDasar * ($diskon3 / 100)) - $potongan;

      // Menghitung pajak dari harga setelah diskon
      $hargaDenganPajak = $hargaSetelahDiskon + ($hargaSetelahDiskon * ((float)$detail['pajak'] / 100));

        // Cek apakah data material sudah ada di tabel stock
        $existingStock = $MdlStock->where('id_material', $detail['id_material'])->first();

        if ($existingStock) {

            $existingStock['stock_masuk'] += $detail['jumlah'];
            $existingStock['price'] = $hargaDenganPajak;
            $existingStock['id_currency'] = $detail['id_currency'];

            // Update record stock
            $MdlStock->update($existingStock['id'], $existingStock);
        } else {
            // Jika tidak ada, insert data stock baru
            $stockData = [
                'id_material' => $detail['id_material'],
                // 'stock_awal' => $detail['stock_awal'],
                'stock_masuk' => $detail['harga'],   
                // 'stock_keluar' => $detail['stock_keluar'],
                'price' => $hargaDenganPajak,
                'id_currency' => $detail['id_currency'],

            ];

            // Insert data stock
            $MdlStock->insert($stockData);
        }
    }

    // Setelah selesai, update status pembelian menjadi 1
    $MdlPembelian->update($id, ['posting' => 1]);
    if ($MdlPembelian->affectedRows()!==0) {
                $riwayat = "Menambah Pembelian id: {$id}";
                $this->changelog->riwayat($riwayat);
                header('HTTP/1.1 200 OK');

        } else {
             header('HTTP/1.1 500 Internal Server Error');
              header('Content-Type: application/json; charset=UTF-8');
              die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }

    }
    public function importpo(){
        $code = $_POST['kode'];
        $id = $_POST['id'];
        $curr = $_POST['curr'];
        

        return $this->importPOToPembelianDetail($code, $id, $curr);

    }
    public function importPOToPembelianDetail($poCode, $idPembelian, $curr)
    {
        // Load models
        $poModel = new MdlPurchaseOrder();
        $poListModel = new MdlPurchaseOrderList();
        $pembelianDetailModel = new MdlPembelianDetail();

        $mdlPembelian = new MdlPembelian();
       
    
        // Get PO ID based on PO Code
        $po = $poModel->where('code', $poCode)->first();
        if (!$po) {
            return $this->response->setJSON([
                "status" => false,
                "message" => "Purchase Order dengan code $poCode tidak ditemukan."
            ]);  
        }
    
        $poId = $po['id'];

        //update po
        $mdlPembelian->set(array('id_po'=>$poId))->where('id',$idPembelian)->update();
    
        // Get PO List based on PO ID
        $poList = $poListModel->where('id_po', $poId)->findAll();
        if (empty($poList)) {
            return $this->response->setJSON([
                "status" => false,
                "message" => "Tidak ada data di Purchase Order List untuk PO ID $poId."
            ]);
        }
    
        // Insert or update data into Pembelian Detail
        foreach ($poList as $item) {
            // Cek apakah sudah ada data dengan id_pembelian dan id_material yang sama
            $existingDetail = $pembelianDetailModel
                ->where('id_pembelian', $idPembelian)
                ->where('id_material', $item['id_material'])
                ->first();
    
            if ($existingDetail) {
                // Jika sudah ada, update jumlahnya
                $newJumlah = $existingDetail['jumlah'] + $item['quantity'];
                $pembelianDetailModel->update($existingDetail['id'], ['jumlah' => $newJumlah]);
            } else {
                // Jika belum ada, insert data baru
                $data = [
                    'id_pembelian' => $idPembelian,
                    'id_material' => $item['id_material'],
                    'id_currency' => $curr,
                    'jumlah' => $item['quantity'],
                    'harga' => $item['price'],
                    'status_pembayaran' => 0,
                    'diskon1' => 0,
                    'diskon2' => 0,
                    'diskon3' => 0,
                    'pajak' => $item['vat'],
                    'potongan' => 0,
                ];
                $pembelianDetailModel->insert($data);
            }
        }
    
        return $this->response->setJSON([
            "status" => true,
            "message" => "Data berhasil diimpor ke Pembelian Detail."
        ]);
    }
    
    public function printGRN($id){
        $MdlPembelian = new MdlPembelian();
        $MdlPembelianDetail = new MdlPembelianDetail();

        $data['grn'] = $MdlPembelian
                ->select('purchase_order.code as po, pembelian.*, supplier.supplier_name,supplier.*, currency.id as curr_id, currency.kode as curr_code, currency.nama as curr_name')
                ->join('supplier', 'supplier.id = pembelian.id_supplier')   
                ->join('currency', 'currency.id = supplier.id_currency')
                ->join('purchase_order', 'purchase_order.id = pembelian.id_po', 'left')
                ->where('pembelian.id', $id)->get()->getResultArray()[0];
        $data['grnDet'] = $MdlPembelianDetail 
                ->select('pembelian_detail.id as id_pembelian_detail,
                            pembelian_detail.id_material,
                            pembelian_detail.id_currency,
                            pembelian_detail.jumlah,
                            pembelian_detail.harga,
                            pembelian_detail.id_currency,
                            pembelian_detail.diskon1,
                            pembelian_detail.diskon2,
                            pembelian_detail.diskon3,
                            pembelian_detail.remarks,
                            pembelian_detail.pajak as vat,
                            pembelian_detail.potongan,
                            materials.kode as material_kode,
                            materials.name as material_name,
                            currency.kode as kode_currency,
                            currency.nama as nama_currency,
                            currency.rate, materials_detail.hscode,satuan.kode as satuan, satuan.nama as nama_satuan'
                            )
                ->join("pembelian","pembelian.id = pembelian_detail.id_pembelian", 'left')
                ->join('supplier', 'supplier.id = pembelian.id_supplier', 'left')
                ->join("materials","materials.id = pembelian_detail.id_material", 'left')
                ->join("currency","pembelian_detail.id_currency = currency.id", 'left')
                ->join("materials_detail","materials_detail.material_id = pembelian_detail.id_material", 'left')
                ->join("satuan","satuan.id = materials_detail.satuan_id", 'left')
                ->where('pembelian_detail.id_pembelian', $id)->get()->getResultArray();
        // return json_encode($data);
        $options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);


    
        // Data untuk tampilan
        $data['title'] = 'Proforma Invoice';
        $html = view('admin/content/printGRN', $data); 
    
        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);
    
        // Atur ukuran kertas A4 dan orientasi landscape
        $dompdf->setPaper('A4', 'landscape');
    
        // Render PDF
        $dompdf->render();
    
        // Output PDF ke browser tanpa mengunduh otomatis
        $dompdf->stream("GRN_{$id}.pdf", ["Attachment" => false]);

    }
}
