<?php namespace App\Controllers;

use App\Models\StProductModel;
use App\Models\StMovementModel;
use App\Models\StInitialModel;
use App\Models\MdlProduct;
use App\Models\LocationModel;
use App\Models\ProformaInvoice;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\MdlMaterial;
use App\Models\MdlStock;
use App\Models\MdlCurrency;
class StockController extends BaseController
{
    protected $stProductModel;
    protected $stMovementModel;
    protected $stInitialModel;
    protected $productModel;
    protected $locationModel;
    protected $PiModel;
    protected $db;

    protected $materialModel;
    protected $stockModel;
    protected $currencyModel;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->stProductModel = new StProductModel();
        $this->stMovementModel = new StMovementModel();
        $this->stInitialModel = new StInitialModel();
        $this->productModel = new MdlProduct();
        $this->locationModel = new LocationModel();
        $this->PiModel = new ProformaInvoice();

         $this->materialModel = new MdlMaterial();
        $this->stockModel = new MdlStock();
        $this->currencyModel = new MdlCurrency();
    }
public function exportExcel()
    {
        // Ambil data stock opname
        $stockData = $this->getStockOpnameData();
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul dokumen
        $spreadsheet->getProperties()
            ->setCreator("Your System")
            ->setTitle("Laporan Stock Opname")
            ->setSubject("Data Stock Gudang");
            
        // Set header tabel
        $sheet->setCellValue('A1', 'LAPORAN STOCK');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set tanggal generate
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('right');
        
        // Header kolom
        $headers = [
            'No',
            'Kode Produk',
            'Nama Produk', 
            'Kode Gudang',
            'Nama Gudang',
            'Stok Awal',
            'Pemasukan',
            'Pengeluaran',
            'Stok Akhir'
        ];
        
        $sheet->fromArray($headers, NULL, 'A4');
        
        // Isi data
        $row = 5;
        $no = 1;
        
        foreach ($stockData as $item) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $item['kode_produk']);
            $sheet->setCellValue('C'.$row, $item['nama_produk']);
            $sheet->setCellValue('D'.$row, $item['kode_gudang']);
            $sheet->setCellValue('E'.$row, $item['nama_gudang']);
            $sheet->setCellValue('F'.$row, $item['stok_awal']);
            $sheet->setCellValue('G'.$row, $item['total_pemasukan']);
            $sheet->setCellValue('H'.$row, $item['total_pengeluaran']);
            $sheet->setCellValue('I'.$row, $item['stok_akhir']);
            $row++;
        }
        
        // Style untuk header kolom
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDDDDD');
        
        // Auto size kolom
        foreach(range('A','I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Border untuk data
        $sheet->getStyle('A4:I'.($row-1))->getBorders()
            ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Format angka
        $sheet->getStyle('F5:I'.($row-1))->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
        
        // Download file
        $filename = 'Stock_'.date('Ymd_His').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    protected function getStockOpnameData()
    {
        $builder = $this->db->table('product p');
        $builder->select("
            p.id AS product_id,
            p.kode AS kode_produk,
            p.nama AS nama_produk,
            l.id AS location_id,
            l.code AS kode_gudang,
            l.name AS nama_gudang,
            (SELECT COALESCE(SUM(si.quantity), 0) 
                FROM st_initial si 
                WHERE si.product_id = p.id AND si.location_id = l.id) AS stok_awal,
            (SELECT COALESCE(SUM(sm.quantity), 0) 
                FROM st_movement sm 
                WHERE sm.product_id = p.id AND sm.to_location = l.id 
                AND sm.movement_type IN ('in', 'transfer')) AS total_pemasukan,
            (SELECT COALESCE(SUM(sm.quantity), 0) 
                FROM st_movement sm 
                WHERE sm.product_id = p.id AND sm.from_location = l.id 
                AND sm.movement_type IN ('out', 'transfer')) AS total_pengeluaran,
            ((SELECT COALESCE(SUM(si.quantity), 0) 
                FROM st_initial si 
                WHERE si.product_id = p.id AND si.location_id = l.id) +
            (SELECT COALESCE(SUM(sm.quantity), 0) 
                FROM st_movement sm 
                WHERE sm.product_id = p.id AND sm.to_location = l.id 
                AND sm.movement_type IN ('in', 'transfer')) -
            (SELECT COALESCE(SUM(sm.quantity), 0) 
                FROM st_movement sm 
                WHERE sm.product_id = p.id AND sm.from_location = l.id 
                AND sm.movement_type IN ('out', 'transfer'))) AS stok_akhir
        ");
        
        $builder->join('locations l', 'l.type = "warehouse" AND l.deleted_at IS NULL AND l.is_active = 1', 'CROSS');
        $builder->where('p.deleted_at IS NULL');
        $builder->groupStart()
            ->where('EXISTS (SELECT 1 FROM st_initial si WHERE si.product_id = p.id AND si.location_id = l.id)')
            ->orWhere('EXISTS (SELECT 1 FROM st_movement sm WHERE sm.product_id = p.id AND (sm.from_location = l.id OR sm.to_location = l.id))')
        ->groupEnd();
        $builder->orderBy('p.kode, l.code');
        
        return $builder->get()->getResultArray();
    }
    public function index() {
    $products = $this->productModel->findAll();
    
    // Prepare stock data for each product
    $productsWithStock = [];
    foreach ($products as $product) {
        $productsWithStock[] = [
            'id' => $product['id'],
            'code' => $product['kode'],
            'name' => $product['nama'],
            'available' => $this->stProductModel->getAvailableStock($product['id']),
            'booked' => $this->stProductModel->getBookedStock($product['id']),
            'total' => $this->stProductModel->getAvailableStock($product['id']) + $this->stProductModel->getBookedStock($product['id'])
        ];
    }

    $data = [
        'title' => 'Stock Management',
        'products' => $productsWithStock
    ];

   $data['content'] = view('admin/content/product_stock',$data);
        return view('admin/index', $data);
}
    // public function index()
    // {
    //     $data['title'] = 'Stock Management';
    //     $data['products'] = $this->productModel->findAll();
    //     $data['content'] = view('admin/content/product_stock',$data);
    //     return view('admin/index', $data);
    // }

    public function view($productId)
    {
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $initialStock = $this->stInitialModel->getInitialStock($productId);
        $available = $this->stProductModel->getAvailableStock($productId);
        $booked = $this->stProductModel->getBookedStock($productId);

        $data = [
            'title' => 'Stock Detail - ' . $product['nama'],
            'product' => $product,
            'productId' => $productId,
            'initial_stock' => $initialStock ? $initialStock['quantity'] : 0,
            'available' => $available,
            'booked' => $booked,
            'total' =>  $available + $booked,
            'stock_details' => $this->stProductModel->getStockDetails($productId),
            'movement_history' => $this->stMovementModel->getProductHistory($productId),
            'stockData' => $this->stMovementModel->getStockByProduct($productId),
            'locations' => $this->locationModel->findAll()
        ];

        $data['content'] = view('admin/content/product_stock_view',$data);
        return view('admin/index', $data);
    }

    public function setInitialStock($productId)
    {
        $rules = [
            'quantity' => 'required|numeric|greater_than[0]',
            'location_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'product_id' => $productId,
            'quantity' => $this->request->getPost('quantity'),
            'location_id' => $this->request->getPost('location_id')
        ];

        // Check if initial stock exists
        $existing = $this->stInitialModel->where('product_id', $productId)->first();
        
        if ($existing) {
            $this->stInitialModel->where('id',$existing['id'])->set( $data)->update();
        } else {
            $this->stInitialModel->insert($data);
        }

        // Add to current stock


        return redirect()->to("/productstock/view/$productId")->with('message', 'Initial stock set successfully');
    }

    public function adjustStock($productId)
    {
        $rules = [
            'adjustment_type' => 'required|in_list[in,out]',
            'quantity' => 'required|numeric|greater_than[0]',
            'location_id' => 'permit_empty|numeric',
            'notes' => 'permit_empty|string',
            'code' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = $this->request->getPost('quantity');
        $locationId = $this->request->getPost('location_id');
        $notes = $this->request->getPost('notes');
        $code = $this->request->getPost('code');

        if ($adjustmentType === 'in') {
            $stockData = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'location_id' => $locationId,
                'label_code' => 'ADJ-' . date('YmdHis'),
                'status' => 'available',
                
            ];
            
            $this->stProductModel->insert($stockData);
        } else {
            // For stock out, deduct from available stock
            $available = $this->stProductModel->getAvailableStock($productId);
            if ($available < $quantity) {
                return redirect()->back()->with('error', 'Insufficient stock available');
            }

            // Deduct from oldest stock first (FIFO)
            $batches = $this->stProductModel
                ->where('product_id', $productId)
                ->where('status', 'available')
                ->orderBy('created_at', 'ASC')
                ->findAll();

            $remaining = $quantity;
            
            foreach ($batches as $batch) {
                if ($remaining <= 0) break;
                
                $deduct = min($batch['quantity'], $remaining);
                
                $newQuantity = $batch['quantity'] - $deduct;
                
                if ($newQuantity > 0) {
                    $this->stProductModel->update($batch['id'], ['quantity' => $newQuantity]);
                } else {
                    $this->stProductModel->delete($batch['id']);
                }
                
                $remaining -= $deduct;
            }
        }

        // Log movement
        $movementData = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'movement_type' => $adjustmentType === 'in' ? 'in' : 'out',
            'reference_type' => 'adjustment',
            'to_location' => $adjustmentType === 'in' ? $locationId : null,
            'from_location' => $adjustmentType === 'out' ? $locationId : null,
            'notes' => $notes ?? 'Manual adjustment',
            'code' => $code,
            'created_by' => $_SESSION['auth']['id']
        ];
        
        $this->stMovementModel->logMovement($movementData);

        return redirect()->to("/productstock/view/$productId")->with('message', 'Stock adjusted successfully');
    }
      public function bookStock($productId)
    {
        $data = [
            'title' => 'Book Stock',
            'product' => $this->productModel->find($productId),
            // 'available' => $this->stMovementModel->getAvailableStock($productId),
            'proformaInvoices' => $this->PiModel->findAll(), 
            'validation' => \Config\Services::validation(),
            'locations' => $this->locationModel->findAll(),
            'stockData' => $this->stMovementModel->getStockByProduct($productId),
        ];
        $data['content'] = view('admin/content/product_stock_book',$data);
        return view('admin/index', $data);
    }

    // Process Booking
public function processBooking($productId)
{
    $rules = [
        'quantity' => 'required|numeric|greater_than[0]',
        'pi_id' => 'required|numeric',
        'location_id' => 'required|numeric', // Added location validation
        'notes' => 'permit_empty|string|max_length[500]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $quantity = $this->request->getPost('quantity');
    $piId = $this->request->getPost('pi_id');
    $locationId = $this->request->getPost('location_id'); // Get location ID
    $notes = $this->request->getPost('notes');

    // Check available stock at SPECIFIC location
    $available = $this->stMovementModel->getAvailableStockAtLocation($productId, $locationId);
    
    if ($available < $quantity) {
        return redirect()->back()->withInput()->with('error', 'Not enough available stock at selected location');
    }

    // Pass location ID to bookStock
    $this->stMovementModel->bookStock($productId, $quantity, $piId, $locationId, $notes);

    return redirect()->to("/productstock/view/$productId")->with('message', 'Stock booked successfully');
}
    // Release Booked Stock
    public function releaseBooking($bookingId)
    {
        $booking = $this->stMovementModel->find($bookingId);
        
        if (!$booking || $booking['status'] != 'booked') {
            return redirect()->back()->with('error', 'Invalid booking');
        }

        $this->stMovementModel->releaseBookedStock([$bookingId]);

        return redirect()->back()->with('message', 'Booking released successfully');
    }

    // Transfer Stock Form
    public function transferStock($productId)
    {
        $data = [
            'title' => 'Transfer Stock',
            'product' => $this->productModel->find($productId),
            'locations' => $this->locationModel->findAll(),
            'available' => $this->stMovementModel->getAvailableStock($productId),
            'validation' => \Config\Services::validation(),
            'stockData' => $this->stMovementModel->getStockByProduct($productId)
        ];

        $data['content'] = view('admin/content/product_stock_transfer',$data);
        return view('admin/index', $data);
    }

    // Process Transfer
public function processTransfer($productId)
{
    $rules = [
        'from_location_id' => 'required|numeric',
        'to_location_id' => 'required|numeric',
        'quantity' => 'required|numeric|greater_than[0]',
        'notes' => 'permit_empty|string|max_length[500]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Correct way to access POST data in CodeIgniter 4:
    $fromLocationId = $this->request->getPost('from_location_id');
    $toLocationId = $this->request->getPost('to_location_id');
    $quantity = $this->request->getPost('quantity');
    $notes = $this->request->getPost('notes');

    $available = $this->stMovementModel->getAvailableStockAtLocation($productId, $fromLocationId);
    
    if ($available < $quantity) {
        return redirect()->back()->withInput()->with('error', 'Not enough available stock at source location');
    }

    try {
        $transferIds = $this->stMovementModel->transferStock($productId, $fromLocationId, $toLocationId, $quantity, $notes);
        return redirect()->to("/productstock/view/$productId")->with('message', 'Stock transferred successfully');
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Transfer failed: ' . $e->getMessage());
    }
}
// Complete a booking
public function completeBooking()
{
    $movementId = $this->request->getPost('movement_id');
    $productId = $this->request->getPost('product_id');
    
    try {
        $this->stMovementModel->update($movementId, [
            'status' => 'completed',
            'movement_type' => 'out', // Change type to out when completed
            'updated_at' => date('Y-m-d H:i:s'),
            'to_location' => null, // Use the location from the form
        ]);
        
        return redirect()->to("/productstock/view/$productId")->with('message', 'Booking marked as completed');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to complete booking: ' . $e->getMessage());
    }
}

// Delete a movement record
public function deleteMovement($id)
{

    $productId = $this->request->getGet('product_id');
    
    try {
        
        // Only allow deletion of certain types
        
        $this->stMovementModel->delete($id);
        
        return redirect()->to("/productstock/view/$id")->with('message', 'Record deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete record: ' . $e->getMessage());
    }
}

 public function initExportExcel()
    {
            // Ambil data stock awal dengan join material dan currency
    $stocks = $this->stockModel->select('stock.*, materials.name as material_name, materials.kode as material_kode, currency.id as currency_id')
        ->join('materials', 'materials.id = stock.id_material')
        ->join('currency', 'currency.id = stock.id_currency', 'left')
        ->where('stock.deleted_at', null)
        ->findAll();

    // Ambil semua currency untuk sheet 2
    $currencies = $this->currencyModel->findAll();

    $spreadsheet = new Spreadsheet();

    // Sheet 1: Data Stock
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Data Stock');

    // Header Sheet 1
    $sheet1->setCellValue('A1', 'No');
    $sheet1->setCellValue('B1', 'Kode Material');
    $sheet1->setCellValue('C1', 'Nama Material');
    $sheet1->setCellValue('D1', 'Stock Awal');
    $sheet1->setCellValue('E1', 'Harga');
    $sheet1->setCellValue('F1', 'ID Currency');
    $sheet1->setCellValue('G1', 'Kode Currency');
    $sheet1->setCellValue('H1', 'Nama Currency');
    $sheet1->setCellValue('I1', 'Rate Currency');

    // Data Sheet 1
    $row = 2;
    foreach ($stocks as $index => $stock) {
        $sheet1->setCellValue('A' . $row, $index + 1);
        $sheet1->setCellValue('B' . $row, $stock['material_kode']);
        $sheet1->setCellValue('C' . $row, $stock['material_name']);
        $sheet1->setCellValue('D' . $row, $stock['stock_awal']);
        $sheet1->setCellValue('E' . $row, $stock['price']);
        $sheet1->setCellValue('F' . $row, $stock['currency_id']);
        
        // Tambahkan rumus VLOOKUP untuk mengambil data dari Sheet 2
        if (!empty($stock['currency_id'])) {
            $sheet1->setCellValue('G' . $row, '=VLOOKUP(F'.$row.',List_Currency!A:D,2,FALSE)');
            $sheet1->setCellValue('H' . $row, '=VLOOKUP(F'.$row.',List_Currency!A:D,3,FALSE)');
            $sheet1->setCellValue('I' . $row, '=VLOOKUP(F'.$row.',List_Currency!A:D,4,FALSE)');
        } else {
            $sheet1->setCellValue('G' . $row, '');
            $sheet1->setCellValue('H' . $row, '');
            $sheet1->setCellValue('I' . $row, '');
        }
        $row++;
    }

    // Sheet 2: List Currency (dengan nama yang diformat tanpa spasi)
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('List_Currency'); // Nama sheet tanpa spasi untuk memudahkan rumus
    
    // Header Sheet 2
    $sheet2->setCellValue('A1', 'ID');
    $sheet2->setCellValue('B1', 'Kode');
    $sheet2->setCellValue('C1', 'Nama');
    $sheet2->setCellValue('D1', 'Rate');

    // Data Sheet 2
    $row = 2;
    foreach ($currencies as $currency) {
        $sheet2->setCellValue('A' . $row, $currency['id']);
        $sheet2->setCellValue('B' . $row, $currency['kode']);
        $sheet2->setCellValue('C' . $row, $currency['nama']);
        $sheet2->setCellValue('D' . $row, $currency['rate']);
        $row++;
    }

    // Kembali ke Sheet 1 sebagai aktif
    $spreadsheet->setActiveSheetIndex(0);

    // Format file
    $writer = new Xlsx($spreadsheet);
    $filename = 'stock_awal_material_' . date('YmdHis') . '.xlsx';

    // Header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
    }

    public function initImportExcel()
   {
    $file = $this->request->getFile('excel_file');
    
    if (!$file->isValid()) {
        return redirect()->back()->with('error', 'File tidak valid');
    }

    $extension = $file->getClientExtension();
    if (!in_array($extension, ['xlsx', 'xls'])) {
        return redirect()->back()->with('error', 'Format file harus Excel (.xlsx atau .xls)');
    }

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getSheetByName('Data Stock');
        
        if (!$sheet) {
            return redirect()->back()->with('error', 'Sheet "Data Stock" tidak ditemukan dalam file Excel');
        }

        $rows = $sheet->toArray();

        // Skip header
        array_shift($rows);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $materialKode = $row[1] ?? null; // Kolom B: Kode Material
            $stockAwal = $row[3] ?? 0;      // Kolom D: Stock Awal
            $price = $row[4] ?? 0;          // Kolom E: Harga
            $currencyId = $row[5] ?? null;  // Kolom F: ID Currency

            // Validasi data kosong
            if (empty($materialKode)) {
                $errors[] = "Baris " . ($index + 2) . ": Kode Material kosong";
                $errorCount++;
                continue;
            }

            // Cari material
            $material = $this->materialModel->where('kode', $materialKode)->first();
            if (!$material) {
                $errors[] = "Baris " . ($index + 2) . ": Material dengan kode '$materialKode' tidak ditemukan";
                $errorCount++;
                continue;
            }

            // Handle ID Currency
            $currencyId = $this->parseCurrencyId($row[5] ?? null);
            
            // Validasi currency jika diisi
            if ($currencyId !== null && !is_numeric($currencyId)) {
                $errors[] = "Baris " . ($index + 2) . ": ID Currency harus angka";
                $errorCount++;
                continue;
            }

            if ($currencyId !== null) {
                $currency = $this->currencyModel->find($currencyId);
                if (!$currency) {
                    $errors[] = "Baris " . ($index + 2) . ": Currency dengan ID '$currencyId' tidak ditemukan";
                    $errorCount++;
                    continue;
                }
            }

            // Cek stock existing
            $existingStock = $this->stockModel
                ->where('id_material', $material['id'])
                ->first();

            $stockData = [
                'id_material' => $material['id'],
                'stock_awal' => $stockAwal,
                'price' => $price,
                'stock_masuk' => 0,
                'stock_keluar' => 0,
                'selisih_stock_opname' => 0,
                'id_currency' => $currencyId,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existingStock) {
                // Update existing
                $this->stockModel->update($existingStock['id'], $stockData);
            } else {
                // Insert new
                $stockData['created_at'] = date('Y-m-d H:i:s');
                $this->stockModel->insert($stockData);
            }

            $successCount++;
        }

        $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount";
        if ($errorCount > 0) {
            $message .= "<br>Error detail:<br>" . implode("<br>", $errors);
        }

        return redirect()->to('/stock')->with('success', $message);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

// Fungsi helper untuk parse ID Currency
private function parseCurrencyId($value)
{
    if ($value === null || $value === '') {
        return null;
    }

    // Jika nilai adalah rumus Excel yang menghasilkan error
    if (is_string($value) && strpos($value, '#') === 0) {
        return null;
    }

    // Coba konversi ke integer
    $intVal = (int)$value;
    return ($intVal > 0) ? $intVal : null;
}
}