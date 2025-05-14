<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MdlProduct;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 
use Dompdf\Dompdf;
use Dompdf\Options;
class ProductController extends BaseController
{
      protected $changelog;
  public function __construct()
  {
    //   parent::__construct();
    $this->db      = \Config\Database::connect();
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

  }

    public function breakdownBom($id)
    {
         
        $d = new WarehouseController();
        $data['group'] = 'Admin';
        $data['title'] = 'Breakdown BoM';
        $data['content'] = view('admin/content/breakdown_bom', $data);
        return view('admin/index', $data);
    }
           public function listdataProdukJoin()
       {
           
           $serverside_model = new \App\Models\MdlDatatableJoin();
           $request = \Config\Services::request();
           
           // Define the columns to select
           $select_columns = 'product.*,product_category.nama as category';
           
           // Define the joins (you can add more joins as needed)
           $joins = [
                 ['product_category', 'product_category.id = product.id_product_cat', 'left'],
           ];
   
           $where = ['product.id !=' => 0, 'product.deleted_at' => NULL];
   
           // Column Order Must Match Header Columns in View
           $column_order = array(
               NULL, 
               'product.name', 
               'product.kode', 
               'product.id',
               'product.id',
               'product.id',
               'product.id',
               'product.id',
           );
           $column_search = array(
               'product.nama', 
               'product.kode', 
          
           );
           $order = array('product.id' => 'desc');
   
           // Call the method to get data with dynamic joins and select fields
           $list = $serverside_model->get_datatables('product', $select_columns, $joins, $column_order, $column_search, $order, $where);
           
           $data = array();
           $no = $request->getPost("start");
           foreach ($list as $lists) {
               $no++;
               $row = array();
               $row[] = $no;
               $row[] = $lists->id;
               $row[] = $lists->nama;
               $row[] = $lists->kode;
               $row[] = $lists->picture;
               $row[] = $lists->text;
               $row[] = $lists->category;

 // From joined suppliers table
               $data[] = $row;
           }
   
           $output = array(
               "draw" => $request->getPost("draw"),
               "recordsTotal" => $serverside_model->count_all('product', $where),
               "recordsFiltered" => $serverside_model->count_filtered('product', $select_columns, $joins, $column_order, $column_search, $order, $where),
               "data" => $data,
           );
          

           return $this->response->setJSON($output);
       }
    function upload(){
  $mdl = new \App\Models\MdlProduct();
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
      $response = [
        'success' => true,
        'picture' => $fileName,
        'msg' => "Gambar: {$imageFile->getClientName()} berhasil diupload"
      ];
      if (isset($_POST['id'])) {
        $param = array('id'=>$_POST['id']);
      // var_dump($param);
      // die();
        $oldRecord = $mdl->where($param)->get()->getResultArray()[0];
        
        $OldFilepath = [];
        $OldFilepath[0] = FCPATH . "assets/upload/1000/{$oldRecord['picture']}";
        $OldFilepath[1] = FCPATH . "assets/upload/image/{$oldRecord['picture']}";
        $OldFilepath[2] = FCPATH . "assets/upload/low/{$oldRecord['picture']}";
        $OldFilepath[3] = FCPATH . "assets/upload/thumb/{$oldRecord['picture']}";
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
        $mdl->set('picture',$fileName);
        $mdl->where($param);
        $mdl->update();
        if ($mdl->affectedRows()>0) {
          $riwayat = "Mengubah gambar produk";
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
       function addcat() {
    
    $userInfo = $_SESSION['auth'];
    $Mdl = new \App\Models\MdlProductCat();

    // Cek jika kode sudah ada
    $existingCat = $Mdl->where('nama', $_POST["nama"])->first();
    if ($existingCat) {
        $riwayat = "User " . $userInfo['nama_depan'] . " gagal menambahkan kategori product: " . $_POST['kode'] . " sudah ada.";
        header('HTTP/1.1 409 Conflict');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Nama sudah ada, gagal menambahkan data.', 'code' => 4)));
    }

    $data = [
        "nama" => $_POST["nama"]
    ];

    if ($Mdl->insert($data)) {
        $query = $Mdl->orderBy('id', 'DESC')->first();
            $riwayat = "User " . $userInfo['nama_depan'] . " " . $userInfo['nama_belakang'] . " menambahkan kategori produk: " . $_POST['nama'] . "";
            header('HTTP/1.1 200 OK');

    } else {
        $riwayat = "User " . $userInfo['nama_depan'] . " gagal menambahkan kategori: " . $_POST['nama'];
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Gagal menambahkan data.', 'code' => 3)));
    }
    $this->changelog->riwayat($riwayat);
}

    public function getcat($id)
    {
        $model = new \App\Models\MdlProductCat();
        $cat = $model->where('id',$id)->find();

        if ($cat) {
            return $this->response->setJSON($cat);
        } else {
            return $this->response->setJSON(['message' => 'category not found'], 404);
        }
    }
        public function cat_list()
    {
        $model = new \App\Models\MdlProductCat();
        $cat = $model->findAll();

        if ($cat) {
            return json_encode($cat);
        } else {
            return $this->response->setJSON(['message' => 'category not found'], 404);
        }
    }

     function create(){
      $mdl = new \App\Models\MdlProduct();
      $data = $_POST['data'];
       $mdl->insert($data);
        if ($mdl->affectedRows()>0) {
          $riwayat = "insert produk {$data['nama']}";
          $this->changelog->riwayat($riwayat);
          header('HTTP/1.1 200 OK');
        }else {
          header('HTTP/1.1 500 Internal Server Error');
          header('Content-Type: application/json; charset=UTF-8');
          die(json_encode(array('message' => 'Tidak ada perubahan pada data', 'code' => 1)));
        }
     }
public function getMaterial()
{
    $MdlMaterial = new \App\Models\MdlMaterial();

    $material = $MdlMaterial->select('materials.*, satuan.kode as kode_satuan, satuan.nama as nama_satuan, stock.price as price, stock.id_currency as id_currency, currency.kode as curr_code, currency.nama as curr_name' )
                ->join('materials_detail', 'materials.id = materials_detail.material_id' )
                ->join('satuan', 'materials_detail.satuan_id = satuan.id' )
                ->join('stock', 'stock.id_material = materials.id' ,'left')
                ->join('currency', 'currency.id = stock.id_currency','left')    
                ->orderBy('materials.name', 'ASC')
                ->findAll();

    return $this->response->setJSON(['material' => $material]);
}
    public function saveBom()
{
    $idProduct = $this->request->getPost('idProduct');
    $idModul = $this->request->getPost('idModul');
    $dataPost = $this->request->getPost('data');
    $model = new \App\Models\MdlBillOfMaterial();

    // Simpan setiap produk yang dipilih ke tabel order_list
    if ($model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->countAllResults()>0) {
    $model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->delete();
        
    }
    // var_dump($dataPost);
    // die();
    
    for ($i = 0; $i < count($dataPost['id_material']); $i++) {
        $data = [
            'id_product'   => $idProduct,
            'id_modul'   => $idModul,
            'id_material' => $dataPost['id_material'][$i],
            'penggunaan'      => $dataPost['penggunaan'][$i],  // Menyimpan harga manual
        ];

        $model->insert(row: $data);
    }

    return $this->response->setJSON(['message' => 'Produk berhasil ditambahkan ke order']);
}
public function saveBomFinishing()
{
    $idProduct = $this->request->getPost('idProduct');
    $idModul = $this->request->getPost('idModul');
    $dataPost = $this->request->getPost('data');
    $model = new \App\Models\MdlBillOfMaterialFinishing();

    // Simpan setiap produk yang dipilih ke tabel order_list
    if ($model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->countAllResults()>0) {
    $model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->delete();
        
    }
    // var_dump($dataPost);
    // die();
    
    for ($i = 0; $i < count($dataPost['id_material']); $i++) {
        $data = [
            'id_product'   => $idProduct,
            'id_modul'   => $idModul,
            'id_material' => $dataPost['id_material'][$i],
            'penggunaan'      => $dataPost['penggunaan'][$i],  // Menyimpan harga manual
        ];

        $model->insert(row: $data);
    }

    return $this->response->setJSON(['message' => 'Produk berhasil ditambahkan ke order']);
}
public function getBom(){
    $model = new \App\Models\MdlBillOfMaterial();

    $idProduct = $this->request->getPost('idProduct');
    $idModul = $this->request->getPost('idModul');
    return json_encode($model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->findAll());
}
public function getBomFinishing(){
  $model = new \App\Models\MdlBillOfMaterialFinishing();

  $idProduct = $this->request->getPost('idProduct');
  $idModul = $this->request->getPost('idModul');
  return json_encode($model->where(array('id_product'=>$idProduct, 'id_modul'=>$idModul))->findAll());
}
    public function searchMaterial()
    {
        // Pastikan permintaan ini adalah AJAX
        if ($this->request->isAJAX()) {
            $query = $this->request->getVar('query');

            // Membuat instance model material
            $materialModel =new \App\Models\MdlMaterial();

            // Mencari material yang cocok dengan query
            $results = $materialModel->like('kode', $query)
                                     ->orLike('name', $query)
                                     ->findAll(10); // Batasi hasil pencarian agar tidak terlalu banyak

            // Mengembalikan hasil dalam format JSON
            return $this->response->setJSON($results);
        } else {
            // Jika bukan AJAX request, kembalikan 404
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
    }
    public function getProduct()
    {
           $MdlProduct = new \App\Models\MdlProduct();

    $product = $MdlProduct->findAll();

    return $this->response->setJSON(['product' => $product]);
    }
    public function getProductData($id)
    {
        $MdlProduct = new \App\Models\MdlProduct();
        $product = $MdlProduct->select('product.*, product_category.nama as category')->join('product_category', 'product_category.id = product.id_product_cat')->where('product.id', $id)->first();
        return $this->response->setJSON(['product' => $product]);
    }
    public function modul($id_product){
      $serverside_model = new \App\Models\MdlDatatableJoin();
                $request = \Config\Services::request();
                
                // Define the columns to select
                $select_columns = 'product.id as id_product, product.kode as code, product.nama as nama, modul.*';
                
                // Define the joins (you can add more joins as needed)
                $joins = [
                    ['product', 'modul.id_product = product.id', 'left'],
      
                ];
        
                $where = ['modul.deleted_at' => NULL, 'modul.id_product'=>$id_product,];
        
                // Column Order Must Match Header Columns in View
                $column_order = array(
                    NULL, 
                    'product.id', 
                    'product.id', 
                    'product.id',
                    'product.id', 
      
                );
                $column_search = array(
                    'modul.desc'
             
                );
                $order = array('modul.id' => 'desc');
        
                // Call the method to get data with dynamic joins and select fields
                $list = $serverside_model->get_datatables('modul', $select_columns, $joins, $column_order, $column_search, $order, $where);
                
                $data = array();
                $no = $request->getPost("start");
                foreach ($list as $lists) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $lists->id;
                    $row[] = $lists->code;
                    $row[] = $lists->id_product;
                    $row[] = $lists->name;
                    $row[] = $lists->desc;
                    $row[] = $lists->picture;
                    $data[] = $row;
                }
        
                $output = array(
                    "draw" => $request->getPost("draw"),
                    "recordsTotal" => $serverside_model->count_all('modul', $where),
                    "recordsFiltered" => $serverside_model->count_filtered('modul', $select_columns, $joins, $column_order, $column_search, $order, $where),
                    "data" => $data,
                );
        
              //   return $this->response->setJSON($output);
              
                return json_encode($output);
          }

          public function createModul($id)
{
    $validation = \Config\Services::validation();
    $rules = [
        'name' => 'required',
        'desc' => 'required',
        'picture' => 'uploaded[picture]|is_image[picture]|max_size[picture,2048]',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $validation->getErrors(),
        ]);
    }

    $file = $this->request->getFile('picture');
    $fileName = $file->getRandomName();
    $file->move('uploads/modul', $fileName);

    $data = [
        'name' => $this->request->getPost('name'),
        'desc' => $this->request->getPost('desc'),
        'picture' => $fileName,
        'id_product' => $id,
    ];

    $model = new \App\Models\MdlModul();
    $model->insert($data);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Item added successfully',
    ]);
}
public function updatePicture()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $model = new \App\Models\MdlModul();
    $item = $model->find($id);

    if (!$item) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Item not found.',
        ]);
    }

    if (!$this->validate(['picture' => 'uploaded[picture]|is_image[picture]|max_size[picture,2048]'])) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $this->validator->getErrors(),
        ]);
    }

    $file = $this->request->getFile('picture');
    $fileName = $file->getRandomName();
    $file->move('uploads/modul', $fileName);

    if ($item['picture']) {
        unlink('uploads/modul/' . $item['picture']); // Menghapus gambar lama
    }

    $model->update($id, ['picture' => $fileName]);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Picture updated successfully.',
    ]);
}
public function get()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $model = new \App\Models\MdlModul();
    $item = $model->find($id); // Mencari data berdasarkan ID

    if ($item) {
        return $this->response->setJSON([
            'status' => true,
            'data' => $item,
        ]);
    }

    return $this->response->setJSON([
        'status' => false,
        'message' => 'Item not found.',
    ]);
}
public function updateData()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST
    $data = $this->request->getPost();

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $validation = \Config\Services::validation();
    $rules = [
        'name' => 'required',
        'desc' => 'required',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $validation->getErrors(),
        ]);
    }

    $model = new \App\Models\MdlModul();
    $item = $model->find($id);

    if (!$item) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Item not found.',
        ]);
    }

    $model->update($id, $data);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Item updated successfully.',
    ]);
}

public function deleteModul($id)
{
    $model = new \App\Models\MdlModul();
    $item = $model->find($id);

    if ($item) {
        if ($item['picture']) {
            unlink('uploads/modul/' . $item['picture']);
        }

        $model->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Item deleted successfully']);
    }

    return $this->response->setJSON(['status' => false, 'message' => 'Item not found']);
}
function updateDimension($id){
    $model = new MdlProduct();
    $model->update($id, $_POST);
    if ($model->affectedRows() !== 0) {
        $riwayat = "Mengubah Dimensi Product id: {$id} ";
        $this->changelog->riwayat($riwayat);
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['message' => 'Tidak ada perubahan pada data', 'code' => 1]));
    }
}
public function printBom($productId, $finishingId)   {
        // Load models

        $productModel = new MdlProduct();

        // Query 1: Ambil data produk dan quantity
        $query1 = $productModel
        ->select('* , finishing.name AS finishing')
        ->join('finishing', 'product.id = finishing.id_product', 'left')
        ->where('product.id', $productId)
        ->where('finishing.id', $finishingId)
        ->findAll();
        // $query1 = $proformaInvoiceDetailsModel
        //     ->select('product.nama,product.kode as kode, proforma_invoice_details.quantity')
        //     ->join('product', 'product.id = proforma_invoice_details.id_product')
        //     ->where('proforma_invoice_details.invoice_id', $invoice_id)
        //     ->findAll();

        // Query 2: Ambil data material dan penggunaan dari billofmaterialfinishing
        $query2 = $productModel
        ->select('
            m.id AS material_id, 
            m.name AS material_name, 
            m.kode AS material_code, 
            FORMAT(SUM(DISTINCT COALESCE(bom.penggunaan, 0)), 3) AS penggunaan, 
    
           p.id, 
            p.nama AS product, 
            FORMAT(SUM(DISTINCT COALESCE(bom.penggunaan, 0)) , 3) AS total_penggunaan,
            satuan.nama as satuan, 
            type.nama as type, 
            finishing.name AS finishing_name,
            finishing.id AS finishing_id, 
            finishing.id as modul_id, 
            materials_detail.kite as kite
        ')
        ->from('product p')
        ->join('billofmaterialfinishing bom', 'p.id = bom.id_product', 'left')
        ->join('materials m', 'bom.id_material = m.id')
        ->join('materials_detail', 'materials_detail.material_id = bom.id_material', 'left')
        ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
        ->join('type', 'type.id = materials_detail.type_id', 'left')
        ->join('finishing', 'bom.id_modul = finishing.id', 'left')
        ->where('p.id', $productId)
        ->where('finishing.id', $finishingId)
        ->groupBy('m.id, m.name, m.kode, p.id, finishing.id,p.id, satuan.nama, type.nama, finishing.name, materials_detail.kite')
        ->orderBy('finishing.id, p.id')
        ->findAll();

        // Query 3: Ambil data material dan penggunaan dari billofmaterial
        $query3 = $productModel
            ->select('m.id AS material_id, m.name AS material_name, m.kode AS material_code, 
                      FORMAT(SUM(COALESCE(bom.penggunaan, 0)), 3) AS penggunaan, 
                    p.id, p.nama AS product, 
                      FORMAT(SUM(COALESCE(bom.penggunaan, 0)) , 3) AS total_penggunaan,
                      satuan.nama as satuan, type.nama as type, modul.name AS modul_name,
                      modul.code AS modul_code, modul.id as modul_id ,materials_detail.kite as kite')
            ->from('product p', true)
            ->join('billofmaterial bom', 'p.id = bom.id_product', 'left')
            ->join('materials m', 'bom.id_material = m.id')
            ->join('materials_detail', 'materials_detail.material_id = bom.id_material', 'left')
            ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
            ->join('type', 'type.id = materials_detail.type_id', 'left')
            ->join('modul ', 'bom.id_modul  =  modul.id', 'left')
            ->where('p.id', $productId)
            ->groupBy('m.id, m.name, m.kode, p.id, modul.id')
            ->orderBy('modul.id,p.id')
            ->findAll();

        // Data untuk dikirim ke view
        $data = [
            'query1' => $query1,
            'query2' => $query2,
            'query3' => $query3,
        ];

        // Load view dengan data
        $html = view('admin/content/printBOM', $data);

        // Setup Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $dompdf->stream("BOM_{$productId}.pdf", ["Attachment" => false]);
    }
    function labourCost($id){
        $data['group'] = 'Admin';
        $data['title'] = 'Labour Cost';
        $data['content'] = view('admin/content/labour_cost', $data);
        return view('admin/index', $data);
    }
    public function labourCreate($id)
{
    $validation = \Config\Services::validation();
    $rules = [
        'process' => 'required',
    ];

    if (!$this->validate(rules: $rules)) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $validation->getErrors(),
        ]);
    }



    $data = $this->request->getPost();
    $data['product_id'] = $id;
    $data['total_cost_idr'] = ($this->request->getPost('wage_per_hours') * $this->request->getPost('time_hours') * $this->request->getPost('total_worker'))+$this->request->getPost('cost');
    $model = new \App\Models\MdlLabourCost();
    $model->insert($data);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Item added successfully',
    ]);
}
public function getLabour($id)
{
    $model = new \App\Models\MdlLabourCost();
    $data = $model->where('product_id',value: $id)->findAll();

    return $this->response->setJSON(['data' => $data]);
}
function deleteLabour($id)
{
    $model = new \App\Models\MdlLabourCost();
    $item = $model->find($id);

    if ($item) {
        $model->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Item deleted successfully']);
    }

    return $this->response->setJSON(['status' => false, 'message' => 'Item not found']);
}
public function inputbom($id, $id_modul)
{
    $data['content'] = view('admin/content/input_bom', ['id_product' => $id, 'id_modul' => $id_modul]);
        return view('admin/index', $data);
}
public function databom($id, $id_modul)
{
    $model = new \App\Models\MdlBillOfMaterial();
    $data = $model->join('materials', 'materials.id = billofmaterial.id_material')
                  ->join('materials_detail', 'materials_detail.material_id = billofmaterial.id_material')
                  ->join('satuan', 'satuan.id = materials_detail.satuan_id')
                  ->where(array('billofmaterial.id_product'=>$id, 'billofmaterial.id_modul'=>$id_modul))
                  ->findAll();
    if ($data) {
        return $this->response->setJSON($data);
    } else {
        return $this->response->setJSON(['message' => 'Data not found'], 404);

    }
}
}