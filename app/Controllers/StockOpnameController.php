<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\StockModel;
use App\Models\StockOpnameModel;
use App\Models\StockOpnameListModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockOpnameController extends BaseController
{
    protected $stockOpnameModel;
    protected $stockOpnameListModel;
    protected $materialModel;
    protected $stockModel;

    public function __construct()
    {
        $this->session = session();
        $this->stockOpnameModel = new StockOpnameModel();
        $this->stockOpnameListModel = new StockOpnameListModel();
        $this->materialModel = new MaterialModel();
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Stock Opname',
            'opnames' => $this->stockOpnameModel->getOpname(),
        ];

        return view('admin/content/stock_opname/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Stock Opname',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/content/stock_opname/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'code' => 'required|is_unique[stock_opname.code]',
            'remarks' => 'required',
            
        ])) {
            return redirect()->to('/stock-opname/create')->withInput();
        }

        $this->stockOpnameModel->save([
            'code' => $this->request->getVar('code'),
            'id_dept' => session()->get('id_dept'),
            'id_user'=> $_SESSION['auth']['id'],
            'remarks' => $this->request->getVar('remarks'),
            'status' => 0 // 0 = draft, 1 = completed
        ]);

        session()->setFlashdata('message', 'Stock Opname created successfully');
        return redirect()->to('/stock-opname');
    }

    public function detail($id)
    {
        $opname = $this->stockOpnameModel->getOpname($id);
        if (!$opname) {
            return redirect()->to('/stock-opname')->with('error', 'Stock Opname not found');
        }

        $data = [
            'title' => 'Stock Opname Detail',
            'opname' => $opname,
            'items' => $this->stockOpnameListModel->getOpnameItems($id),
            'materials' => $this->materialModel->getActiveMaterials()
        ];

        return view('admin/content/stock_opname/detail', $data);
    }

    public function addItem()
    {
        $opnameId = $this->request->getVar('opname_id');
        $materialId = $this->request->getVar('material_id');
        $jumlahAkhir = $this->request->getVar('jumlah_akhir');

        $currentStock = $this->stockModel->getCurrentStock($materialId);

        $this->stockOpnameListModel->save([
            'id_stock_opname' => $opnameId,
            'id_material' => $materialId,
            'jumlah_awal' => $currentStock,
            'jumlah_akhir' => $jumlahAkhir
        ]);

        return redirect()->to("/stock-opname/detail/$opnameId")->with('message', 'Item added successfully');
    }

public function complete($id)
{
    $opname = $this->stockOpnameModel->getOpname($id);
    if (!$opname) {
        return redirect()->to('/stock-opname')->with('error', 'Stock Opname not found');
    }

    // Update stock based on opname results
    $items = $this->stockOpnameListModel->getOpnameItems($id);
    foreach ($items as $item) {
        $selisih = $item['jumlah_akhir'] - $item['jumlah_awal'];
        
        // Update stock dengan selisih stock opname
        $this->stockModel->updateStockAfterOpname($item['id_material'], $selisih);
    }

    // Update opname status to completed
    $this->stockOpnameModel->save([
        'id' => $id,
        'status' => 1
    ]);

    return redirect()->to("/stock-opname/detail/$id")->with('message', 'Stock Opname completed successfully');
}
public function exportExcel($opnameId)
{
    $opname = $this->stockOpnameModel->getOpname($opnameId);
    $items = $this->stockOpnameListModel->getOpnameItems($opnameId);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'STOCK OPNAME FINAL REPORT');
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A2', 'Opname Code:');
    $sheet->setCellValue('B2', $opname['code']);
    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d F Y H:i', strtotime($opname['created_at'])));
    $sheet->setCellValue('A4', 'Status:');
    $sheet->setCellValue('B4', $opname['status'] == 0 ? 'Draft' : 'Completed');
    $sheet->setCellValue('A5', 'Remarks:');
    $sheet->setCellValue('B5', $opname['remarks']);
    $sheet->mergeCells('B5:H5');

    // Column headers
    $headers = [
        'No', 
        'ID Material', 
        'Material Code', 
        'Material Name', 
        'System Stock', 
        'Physical Count', 
        'Difference', 
        'Variance Value'
    ];
    $sheet->fromArray($headers, null, 'A7');
    
    // Data
    $row = 8;
    foreach ($items as $index => $item) {
        $currentPrice = $this->stockModel->where('id_material', $item['id_material'])
                                      ->orderBy('created_at', 'desc')
                                      ->first()['price'] ?? 0;
        
        $difference = $item['jumlah_akhir'] - $item['jumlah_awal'];
        $varianceValue = $difference * $currentPrice;

        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $item['id_material']);
        $sheet->setCellValue('C' . $row, $item['material_code']);
        $sheet->setCellValue('D' . $row, $item['material_name']);
        $sheet->setCellValue('E' . $row, $item['jumlah_awal']);
        $sheet->setCellValue('F' . $row, $item['jumlah_akhir']);
        $sheet->setCellValue('G' . $row, $difference);
        $sheet->setCellValue('H' . $row, $varianceValue);
        
        $row++;
    }

    // Styling
    $sheet->getStyle('A7:H7')->getFont()->setBold(true);
    $sheet->getStyle('A7:H7')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFDDDDDD');
    
    // Format numbers
    $sheet->getStyle("E8:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("F8:F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("G8:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00;-#,##0.00');
    $sheet->getStyle("H8:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Auto-size columns
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'stock_opname_report_' . $opname['code'] . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}
public function importExcel($opnameId)
{
    $file = $this->request->getFile('excel_file');
    
    if (!$file->isValid()) {
        return redirect()->to("/stock-opname/detail/$opnameId")->with('error', 'Invalid file upload');
    }

    $extension = $file->getClientExtension();
    if (!in_array($extension, ['xlsx', 'xls'])) {
        return redirect()->to("/stock-opname/detail/$opnameId")->with('error', 'Only Excel files are allowed');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Skip header rows (assuming first 7 rows are headers)
    $rows = array_slice($rows, 7);

    // Initialize counters
    $successCount = 0;
    $errorCount = 0;
    $errorMessages = [];

    foreach ($rows as $rowIndex => $row) {
        try {
            // Skip empty rows
            if (empty($row[1])) continue;

            $idMaterial = (int)$row[1];
            $materialCode = trim($row[2] ?? '');
            $materialName = trim($row[3] ?? '');
            
            // Validate material exists
            $material = $this->materialModel->find($idMaterial);
            if (!$material) {
                $errorCount++;
                $errorMessages[] = "Row " . ($rowIndex + 8) . ": Material with ID {$idMaterial} not found";
                continue;
            }
            
            // Cross-check with code if provided
            if (!empty($materialCode) && $material['kode'] !== $materialCode) {
                $errorCount++;
                $errorMessages[] = "Row " . ($rowIndex + 8) . ": Material ID {$idMaterial} does not match code {$materialCode}";
                continue;
            }

            // Parse physical count - handle null/empty/zero values
            $countedStock = $this->parseExcelNumber($row[6] ?? null);
            
            // Set to 0 if null or empty string
            if ($countedStock === null || $countedStock === '') {
                $countedStock = 0;
            }
            
            // Ensure it's a numeric value after conversion
            if (!is_numeric($countedStock)) {
                $errorCount++;
                $errorMessages[] = "Row " . ($rowIndex + 8) . ": Invalid quantity format for material ID {$idMaterial}";
                continue;
            }

            // Convert to float and ensure positive value
            $countedStock = abs((float)$countedStock);
            
            $currentStock = $this->stockModel->getCurrentStock($idMaterial);
            $notes = $row[7] ?? '';

            // Check if item already exists in opname
            $existingItem = $this->stockOpnameListModel->where([
                'id_stock_opname' => $opnameId,
                'id_material' => $idMaterial
            ])->first();

            $now = date('Y-m-d H:i:s');
            
            if ($existingItem) {
                // Update existing item
                $this->stockOpnameListModel->update($existingItem['id'], [
                    'jumlah_akhir' => $countedStock,
                    'updated_at' => $now
                ]);
            } else {
                // Add new item
                $this->stockOpnameListModel->insert([
                    'id_stock_opname' => $opnameId,
                    'id_material' => $idMaterial,
                    'jumlah_awal' => $currentStock,
                    'jumlah_akhir' => $countedStock,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }

            $successCount++;
        } catch (\Exception $e) {
            $errorCount++;
            $errorMessages[] = "Row " . ($rowIndex + 8) . ": " . $e->getMessage();
            log_message('error', 'Excel import error: ' . $e->getMessage());
        }
    }

    // Prepare response
    $message = "Import completed with {$successCount} successful records";
    if ($errorCount > 0) {
        $message .= " and {$errorCount} errors";
        session()->setFlashdata('import_errors', $errorMessages);
    }

    return redirect()->to("/stock-opname/detail/$opnameId")
        ->with('message', $message)
        ->with('import_stats', [
            'success' => $successCount,
            'errors' => $errorCount
        ]);
}

/**
 * Enhanced number parser that handles null/zero values
 */

/**
 * Enhanced number parser with ID material fallback
 */
protected function parseExcelNumber($value, $idMaterial = null)
{
    if ($value === null || $value === '') {
        return null;
    }

    // If it's already a number
    if (is_numeric($value)) {
        return (float)$value;
    }

    // Handle formatted numbers (with thousand separators)
    $cleanedValue = preg_replace('/[^\d.,-]/', '', $value);
    
    // Handle European format (1.234,56)
    if (preg_match('/^\d{1,3}(\.\d{3})*(,\d+)?$/', $cleanedValue)) {
        $cleanedValue = str_replace('.', '', $cleanedValue);
        $cleanedValue = str_replace(',', '.', $cleanedValue);
    }
    // Handle American format (1,234.56)
    else if (preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $cleanedValue)) {
        $cleanedValue = str_replace(',', '', $cleanedValue);
    }

    if (is_numeric($cleanedValue)) {
        return (float)$cleanedValue;
    }

    return null;
}


  public function downloadTemplate()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'STOCK OPNAME TEMPLATE');
    $sheet->setCellValue('A2', 'Instructions:');
    $sheet->setCellValue('A3', '1. Fill only columns B and E (Material Code and Physical Count)');
    $sheet->setCellValue('A4', '2. Do not modify the header structure');
    $sheet->setCellValue('A5', '3. System will automatically calculate differences');

    // Column headers
    $sheet->setCellValue('A7', 'No');
    $sheet->setCellValue('B7', 'Material Code');
    $sheet->setCellValue('C7', 'Material Name');
    $sheet->setCellValue('D7', 'System Stock');
    $sheet->setCellValue('E7', 'Physical Count');
    $sheet->setCellValue('F7', 'Difference');
    $sheet->setCellValue('G7', 'Variance Value');
    $sheet->setCellValue('H7', 'Notes');

    // Sample data
    $materials = $this->materialModel->getActiveMaterials();
    $row = 8;
    $no = 1;
    foreach ($materials as $material) {
        $currentStock = $this->stockModel->getCurrentStock($material['id']);
        $currentPrice = $this->stockModel->where('id_material', $material['id'])
                                      ->orderBy('created_at', 'desc')
                                      ->first()['price'] ?? 0;

        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $material['kode']);
        $sheet->setCellValue('C' . $row, $material['name']);
        $sheet->setCellValue('D' . $row, $currentStock);
        $sheet->setCellValue('E' . $row, '');
        $sheet->setCellValue('F' . $row, '=E' . $row . '-D' . $row);
        $sheet->setCellValue('G' . $row, '=F' . $row . '*' . $currentPrice);
        $sheet->setCellValue('H' . $row, '');
        $row++;
    }

    // Style
    $sheet->getStyle('A7:H7')->getFont()->setBold(true);
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Lock header rows and columns
    $sheet->freezePane('A8');

    // Protect formulas
    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('A7:H7')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
    $sheet->getStyle('D8:D' . ($row-1))->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
    $sheet->getStyle('F8:G' . ($row-1))->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

    // Only allow editing in specific columns
    $unlockedCells = $sheet->getStyle('B8:C' . ($row-1));
    $unlockedCells->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
    $unlockedCells = $sheet->getStyle('E8:E' . ($row-1));
    $unlockedCells->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
    $unlockedCells = $sheet->getStyle('H8:H' . ($row-1));
    $unlockedCells->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

    $writer = new Xlsx($spreadsheet);
    $filename = 'stock_opname_template.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}
public function exportTemplateWithData($opnameId)
{
    $opname = $this->stockOpnameModel->getOpname($opnameId);
    $items = $this->stockOpnameListModel->getOpnameItems($opnameId);
    
    // Get all active materials if no items exist yet
    if (empty($items)) {
        $materials = $this->materialModel->getActiveMaterials();
        foreach ($materials as $material) {
            $items[] = [
                'id_material' => $material['id'],
                'material_code' => $material['kode'],
                'material_name' => $material['name'],
                'jumlah_awal' => $this->stockModel->getCurrentStock($material['id']),
                'jumlah_akhir' => null
            ];
        }
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Header
    $sheet->setCellValue('A1', 'STOCK OPNAME WORKSHEET');
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A2', 'Opname Code:');
    $sheet->setCellValue('B2', $opname['code']);
    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d F Y'));
    $sheet->setCellValue('A4', 'Status:');
    $sheet->setCellValue('B4', $opname['status'] == 0 ? 'Draft' : 'Completed');
    $sheet->setCellValue('A5', 'Remarks:');
    $sheet->setCellValue('B5', $opname['remarks']);
    $sheet->mergeCells('B5:H5');

    // Column headers
    $headers = [
        'No', 
        'ID Material', 
        'Material Code', 
        'Material Name', 
        'Unit', 
        'System Stock', 
        'Physical Count', 
        'Notes'
    ];
    $sheet->fromArray($headers, null, 'A7');
    
    // Data
    $row = 8;
    foreach ($items as $index => $item) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $item['id_material']);
        $sheet->setCellValue('C' . $row, $item['material_code']);
        $sheet->setCellValue('D' . $row, $item['material_name']);
        $sheet->setCellValue('E' . $row, 'PCS'); // Adjust unit as needed
        $sheet->setCellValue('F' . $row, $item['jumlah_awal']);
        $sheet->setCellValue('G' . $row, $item['jumlah_akhir'] ?? '');
        $sheet->setCellValue('H' . $row, '');
        
        $row++;
    }

    // Formatting
    $lastRow = $row - 1;
    
    // Format numbers
    $sheet->getStyle("F8:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("G8:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Protection and locking
    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('A7:H7')->getProtection()->setLocked(true);
    $sheet->getStyle("B8:B{$lastRow}")->getProtection()->setLocked(true);
    $sheet->getStyle("C8:D{$lastRow}")->getProtection()->setLocked(true);
    $sheet->getStyle("E8:E{$lastRow}")->getProtection()->setLocked(true);
    $sheet->getStyle("F8:F{$lastRow}")->getProtection()->setLocked(true);
    
    // Only allow editing in Physical Count and Notes columns
    $sheet->getStyle("G8:G{$lastRow}")->getProtection()->setLocked(false);
    $sheet->getStyle("H8:H{$lastRow}")->getProtection()->setLocked(false);
    
    // Freeze header row
    $sheet->freezePane('A8');
    
    // Auto-size columns
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'stock_opname_input_' . $opname['code'] . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}
 public function deleteItem($itemId)
{
    // Validasi apakah item exist
    $item = $this->stockOpnameListModel->find($itemId);
    if (!$item) {
        return redirect()->back()->with('error', 'Item not found');
    }

    // Dapatkan opname ID untuk redirect
    $opnameId = $item['id_stock_opname'];

    // Validasi status opname (hanya boleh delete jika status draft)
    $opname = $this->stockOpnameModel->find($opnameId);
    if ($opname['status'] != 0) {
        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('error', 'Cannot delete item from completed stock opname');
    }

    try {
        // Soft delete item
        $this->stockOpnameListModel->delete($itemId);

        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('message', 'Item deleted successfully');
    } catch (\Exception $e) {
        log_message('error', 'Failed to delete stock opname item: ' . $e->getMessage());
        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('error', 'Failed to delete item');
    }
}
public function deleteAllItems($opnameId)
{
    // Validasi CSRF token
    if (!$this->request->is('delete')) {
        return redirect()->back()->with('error', 'Invalid request method');
    }

    // Validasi apakah opname exist
    $opname = $this->stockOpnameModel->find($opnameId);
    if (!$opname) {
        return redirect()->to('/stock-opname')->with('error', 'Stock opname not found');
    }

    // Validasi status opname (hanya boleh delete jika status draft)
    if ($opname['status'] != 0) {
        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('error', 'Cannot delete items from completed stock opname');
    }

    try {
        // Dapatkan semua item opname
        $items = $this->stockOpnameListModel->where('id_stock_opname', $opnameId)->findAll();
        $itemCount = count($items);

        if ($itemCount === 0) {
            return redirect()->to("/stock-opname/detail/$opnameId")
                ->with('info', 'No items to delete');
        }

        // Soft delete semua item
        $this->stockOpnameListModel->where('id_stock_opname', $opnameId)->delete();

        // Log activity
        $user = session()->get('username') ?? 'System';
        log_message('info', "User $user deleted all $itemCount items from opname $opnameId");

        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('message', "Successfully deleted all $itemCount items");
    } catch (\Exception $e) {
        log_message('error', "Failed to delete all items from opname $opnameId: " . $e->getMessage());
        return redirect()->to("/stock-opname/detail/$opnameId")
            ->with('error', 'Failed to delete all items');
    }
}
}