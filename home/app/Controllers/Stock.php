<?php
namespace App\Controllers;
use AllowDynamicProperties;
use CodeIgniter\Controller;
use Bcrypt\Bcrypt;
use google\apiclient;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Stock extends BaseController
{
  protected $bcrypt;
  protected $userValidation;
  protected $bcrypt_version;
  protected $session;
  protected $db;
  protected $uri;
  protected $form_validation;
  protected $changelog;

  public function __construct()
  {
    //   parent::__construct();
    $this->db = \Config\Database::connect();
    $this->session = session();
    $this->bcrypt = new Bcrypt();
    $this->bcrypt_version = '2a';
    $this->uri = service('uri');
    helper('form');
    $this->form_validation = \Config\Services::validation();
    $this->userValidation = new \App\Controllers\LoginValidation();
    $this->changelog = new \App\Controllers\Changelog();

    //if sesion habis

    $check = new \App\Controllers\CheckAccess();
    $check->logged();
  }
  public function index()
  {

  }

  // function logoutAdmin(){
  //     return "adad";
  // }
  function access($page)
  {
    $check = new \App\Controllers\CheckAccess();
    $check->access($_SESSION['auth']['id'], $page);
  }
  public function addStock()
  {
    $this->access('operator');
    return view('admin/content/inputStock');
  }
public function stockdata()
{
    $serverside_model = new \App\Models\MdlDatatableJoin();
    $request = \Config\Services::request();

    // Define the columns to select
    $select_columns = 'stock.*, materials.name as name, materials.kode as kode, satuan.nama as satuan, satuan.kode as kode_satuan, currency.rate as rate, currency.kode as kode_currency';

    // Define the joins with additional condition for non-empty material names
    $joins = [
        ['materials', 'stock.id_material = materials.id AND materials.name IS NOT NULL AND materials.name != ""', 'inner'],
        ['materials_detail', 'materials_detail.material_id = materials.id', 'left'],
        ['type', 'type.id = materials_detail.type_id', 'left'],
        ['satuan', 'satuan.id = materials_detail.satuan_id', 'left'],
        ['currency', 'currency.id = stock.id_currency', 'left'],
    ];

    $where = ['stock.deleted_at' => NULL];

    // Column Order Must Match Header Columns in View
    $column_order = array(
        NULL,
        'materials.kode',
        'materials.name',
        'type.id',
        'materials.id',
        'materials.id',
        'materials.id',
        'materials.id',
        'materials.id',
        'materials.id',
        'materials.id'
    );
    
    $column_search = array(
        'materials.name',
        'materials.kode',
    );
    
    $order = array('stock.id' => 'desc');

    // Call the method to get data with dynamic joins and select fields
    $list = $serverside_model->get_datatables('stock', $select_columns, $joins, $column_order, $column_search, $order, $where);

    $data = array();
    $no = $request->getPost("start");
    foreach ($list as $lists) {
        // Skip if material name is empty (though the join condition should already handle this)
        if (empty($lists->name)) {
            continue;
        }

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $lists->id;
        $row[] = $lists->name;
        $row[] = $lists->kode;
        $row[] = $lists->stock_awal;
        $row[] = $this->get_stock_in_out($lists->id_material)['total_in']; //in
        $row[] = $this->get_stock_in_out($lists->id_material)['total_out']; //out
        $row[] = $lists->satuan;
        $row[] = $lists->kode_satuan;
        $row[] = $lists->id_material;
        $row[] = $lists->price;
        $row[] = $lists->rate;
        $row[] = $lists->kode_currency;
        $row[] = $this->get_stock_in_out($lists->id_material)['so']; //stock opname
        $row[] = $lists->stock_awal + $this->get_stock_in_out($lists->id_material)['total']; //stock
        $data[] = $row;
    }

    $output = array(
        "draw" => $request->getPost("draw"),
        "recordsTotal" => $serverside_model->count_all('stock', $where),
        "recordsFiltered" => $serverside_model->count_filtered('stock', $select_columns, $joins, $column_order, $column_search, $order, $where),
        "data" => $data,
    );

    return json_encode($output);
}

  function get_stock_in_out($id)
  {
    $data['total_in'] = $this->materialPurchase($id) + $this->materialReturn($id);
    $data['total_out'] = $this->materialDestruction($id) + $this->materialRequisition($id);
    $data['so'] = $this->materialStockOpname($id);
    $data['total'] = $data['total_in'] + $data['total_out'] + $data['so'];
    // var_dump($data);
    return $data;

  }
  function get_stock($id)
  {
    $data['stock_awal'] = $this->getStockAwal($id);
    $data['total_in'] = $this->materialPurchase($id) + $this->materialReturn($id);
    $data['total_out'] = $this->materialDestruction($id) + $this->materialRequisition($id);
    $data['so'] = $this->materialStockOpname($id);
    $data['total'] = $data['total_in'] + $data['total_out'] + $data['so'] + $data['stock_awal'];
    // var_dump($data);
    return json_encode($data);

  }
  public function getStockAwal($id)
  {
    $mdl = new \App\Models\MdlStock();

    // Initialize the query
    $query = $mdl->select('stock_awal as jumlah')
      ->where('stock.id_material', $id);


    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }

  public function materialReturn($id)
  {
    $mdl = new \App\Models\MdlMaterialReturnList();

    // Initialize the query
    $query = $mdl->select(' 

        sum(material_return_list.jumlah) as jumlah, 
 ') // Select fields from both tables
      ->join('materials', 'materials.id = material_return_list.id_material') // Join with materials table
      ->join('material_return', 'material_return.id = material_return_list.id_material_return')
      ->where('material_return.status', 1)
      ->where('material_return_list.id_material', $id);


    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialRequisition($id)
  {
    $mdl = new \App\Models\MdlMaterialRequisitionProgress();


    $query = $mdl->select(' 
        sum(-(material_requisition_progress.jumlah)) as jumlah, 
       ')
      ->join('material_requisition_list', 'material_requisition_list.id = material_requisition_progress.id_material_requisition_list') // Join with materials table

      ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition') // Join with materials table
      ->where('material_requisition.status', 1)
      ->where('material_requisition_progress.id_material', $id);
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialPurchase($id)
  {
    $mdl = new \App\Models\MdlPembelianDetail();


    // Initialize the query
    $query = $mdl->select(' 
        sum(pembelian_detail.jumlah) as jumlah, 
') // Select fields from both tables
      ->join('pembelian', 'pembelian.id = pembelian_detail.id_pembelian') // Join with materials table
      ->where('pembelian.posting', 1)
      ->where('pembelian_detail.id_material', $id);

    // Add date range conditions if provided


    // Fetch the purchase details
    $data = $query->findAll();

    // Return the data as JSON or load a view as needed

    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialDestruction($id)
  {
    $mdl = new \App\Models\MdlMaterialDestructionList();


    // Initialize the query
    $query = $mdl->select(' 
       
        sum(-(material_destruction_list.jumlah)) as jumlah, 
         ') // Select fields from both tables
      ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction') // Join with materials table
      ->where('material_destruction.status', 1)
      ->where('material_destruction_list.id_material', $id);

    // Add date range conditions if provided


    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
  public function materialStockOpname($id)
  {
    $mdl = new \App\Models\MdlStockOpnameList();

    // Get the material ID and date range from the POST request


    // Initialize the query
    $query = $mdl->select(' 
       
        sum((stock_opname_list.jumlah_akhir - stock_opname_list.jumlah_awal)) as jumlah, 
         ') // Select fields from both tables
      ->join('stock_opname', 'stock_opname.id = stock_opname_list.id_stock_opname')
      ->where('stock_opname.status', 1)
      ->where('stock_opname_list.id_material', $id);

    // Add date range conditions if provided

    // Fetch the purchase details
    $data = $query->findAll();


    // Return the data as JSON or load a view as needed
    if (empty($data)) {
      return 0;
    }

    // Return the 'jumlah' value
    return $data[0]['jumlah'];
  }
 public function exportExcel()
{
    // Load model
    $stockModel = new \App\Models\MdlStock();
    
    // Define columns and joins
    $selectColumns = 'stock.*, 
        materials.name as name, 
        materials.kode as kode, 
        satuan.nama as satuan, 
        satuan.kode as kode_satuan, 
        currency.rate as rate, 
        currency.kode as kode_currency';
    
    $joins = [
        ['materials', 'stock.id_material = materials.id', 'left'],
        ['materials_detail', 'materials_detail.material_id = materials.id', 'left'],
        ['type', 'type.id = materials_detail.type_id', 'left'],
        ['satuan', 'satuan.id = materials_detail.satuan_id', 'left'],
        ['currency', 'currency.id = stock.id_currency', 'left'],
    ];
    
    // Get data with joins
    $builder = $stockModel->builder();
    $builder->select($selectColumns);
    foreach ($joins as $join) {
        $builder->join($join[0], $join[1], $join[2]);
    }
    
    // Filter data yang memiliki material name
    $builder->where('materials.name IS NOT NULL');
    $builder->where('materials.name !=', '');
    
    $data = $builder->get()->getResultArray();

    // Create spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers
    $headers = [
        'ID', 'Material ID', 'Stock Awal', 'Stock Masuk', 'Stock Keluar', 'Price', 'Currency ID',
        'Material Name', 'Material Code', 'Unit', 'Unit Code',
        'Rate', 'Currency Code'
    ];
    
    $sheet->fromArray($headers, null, 'A1');
    
    // Add data
    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['id']);
        $sheet->setCellValue('B' . $row, $item['id_material']);
        $sheet->setCellValue('C' . $row, $item['stock_awal']);
        $sheet->setCellValue('D' . $row, $item['stock_masuk'] ?? 0);
        $sheet->setCellValue('E' . $row, $item['stock_keluar'] ?? 0);
        $sheet->setCellValue('F' . $row, $item['price']);
        $sheet->setCellValue('G' . $row, $item['id_currency']);
        $sheet->setCellValue('H' . $row, $item['name']);
        $sheet->setCellValue('I' . $row, $item['kode']);
        $sheet->setCellValue('J' . $row, $item['satuan']);
        $sheet->setCellValue('K' . $row, $item['kode_satuan']);
        $sheet->setCellValue('L' . $row, $item['rate']);
        $sheet->setCellValue('M' . $row, $item['kode_currency']);
        $row++;
    }
    
    // Auto size columns
    foreach (range('A', 'M') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Output file
    $filename = 'stock_data_' . date('YmdHis') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

public function importExcel()
{
    // Validation rules
    $validationRules = [
        'excel_file' => [
            'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]',
            'errors' => [
                'uploaded' => 'Harap upload file Excel',
                'ext_in' => 'Hanya file Excel yang diperbolehkan'
            ]
        ]
    ];
    
    if (!$this->validate($validationRules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    $file = $this->request->getFile('excel_file');
    
    // Load spreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file->getPathname());
    $sheet = $spreadsheet->getActiveSheet();
    
    // Get data
    $rows = $sheet->toArray();
    array_shift($rows); // Remove header
    
    // Load models
    $stockModel = new \App\Models\MdlStock();
    $materialModel = new \App\Models\MdlMaterial();
    $currencyModel = new \App\Models\MdlCurrency();
    $unitModel = new \App\Models\MdlSatuan();
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($rows as $index => $row) {
        try {
            // Skip if no material code
            if (empty($row[8])) { // Column I (Material Code)
                continue;
            }
            
            // Material
            $material = $materialModel->where('kode', $row[8])->first();
            // if (!$material) {
            //     $materialId = $materialModel->insert([
            //         'name' => $row[7] ?? '', // Column H (Name)
            //         'kode' => $row[8]       // Column I (Code)
            //     ]);
            // } else {
                $materialId = $material['id'];
            // }
            
            // Currency
            $currency = $currencyModel->where('kode', $row[12])->first(); // Column M
            $currencyId = $currency ? $currency['id'] : null;
            
            // Unit
            $unit = $unitModel->where('kode', $row[10])->first(); // Column K
            $unitId = $unit ? $unit['id'] : null;
            
            // Prepare stock data
            $stockData = [
                'id_material' => $materialId,
                'stock_awal' => $row[2] ?? 0,    // Column C
                'stock_masuk' => $row[3] ?? 0,   // Column D
                'stock_keluar' => $row[4] ?? 0,  // Column E
                'price' => $row[5] ?? 0,         // Column F
                'id_currency' => $currencyId
            ];
            
            // Update or insert
            if (!empty($row[0])) { // Column A (ID)
                $stock = $stockModel->find($row[0]);
                if ($stock) {
                    $stockModel->update($row[0], $stockData);
                } else {
                    $stockModel->insert($stockData);
                }
            } else {
                $stockModel->insert($stockData);
            }
            
            $successCount++;
        } catch (\Exception $e) {
            $errorCount++;
            $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
        }
    }
    
    $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
    if ($errorCount > 0) {
        return redirect()->back()->with('message', $message)->with('import_errors', $errors);
    }
    
    return redirect()->back()->with('success', $message);
}
}