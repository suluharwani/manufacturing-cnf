<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use AllowDynamicProperties; 
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
  function access($page){
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'],$page);
  }
    public function index($param)
    {
         $this->access('operator');
        $d = new WarehouseController();
        $data['group'] = 'Admin';
        $data['title'] = 'Breakdown BoM';
        echo('ok');
        return  view('admin/content/breakdownMaterial',$data);
    }
           public function listdataProdukJoin()
       {
           $this->access('operator');
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
    $this->access('operator');
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

    $material = $MdlMaterial->select('materials.*, satuan.kode as kode_satuan, satuan.nama as nama_satuan')
                ->join('materials_detail', 'materials.id = materials_detail.material_id' )
                ->join('satuan', 'materials_detail.satuan_id = satuan.id' )
                ->findAll();

    return $this->response->setJSON(['material' => $material]);
}
    public function saveBom()
{
    $idProduct = $this->request->getPost('idProduct');
    $dataPost = $this->request->getPost('data');
    $model = new \App\Models\MdlBillOfMaterial();

    // Simpan setiap produk yang dipilih ke tabel order_list
    if ($model->where('id_product', $idProduct)->countAllResults()>0) {
    $model->where('id_product', $idProduct)->delete();
        
    }
    // var_dump($dataPost);
    // die();
    
    for ($i = 0; $i < count($dataPost['id_material']); $i++) {
        $data = [
            'id_product'   => $idProduct,
            'id_material' => $dataPost['id_material'][$i],
            'penggunaan'      => $dataPost['penggunaan'][$i],  // Menyimpan harga manual
        ];

        $model->insert($data);
    }

    return $this->response->setJSON(['message' => 'Produk berhasil ditambahkan ke order']);
}
public function getBom(){
    $model = new \App\Models\MdlBillOfMaterial();

    $idProduct = $this->request->getPost('idProduct');
    return json_encode($model->where('id_product', $idProduct)->findAll());
}
}
