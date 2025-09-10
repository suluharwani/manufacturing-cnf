<?php

namespace App\Controllers;

use App\Models\ComponentModel;
use App\Models\ComponentCategoryModel;
use App\Models\ComponentStockModel;
use App\Models\ComponentTransactionModel;
use App\Models\MdlProduct;

class Component extends BaseController
{
    protected $componentModel;
    protected $categoryModel;
    protected $stockModel;
    protected $transactionModel;
    protected $productModel;

    public function __construct()
    {
        $this->componentModel = new ComponentModel();
        $this->categoryModel = new ComponentCategoryModel();
        $this->stockModel = new ComponentStockModel();
        $this->transactionModel = new ComponentTransactionModel();
        $this->productModel = new MdlProduct();
    }

   // app/Controllers/Component.php

public function index()
{
    $data['components'] = $this->componentModel
    ->select('c.*, CONCAT(p.kode, " - ", p.nama) as product_name, s.quantity, s.minimum_stock')
    ->from('component_components c')
    ->join('product p', 'p.id = c.product_id', 'left')
    ->join('component_stocks s', 's.component_id = c.id', 'left')
    ->orderBy('c.id', 'ASC')
    ->groupBy('c.id')
    ->findAll();

    $data['products'] = $this->productModel->findAll();
    $data['group'] = 'Component';
    $data['title'] = 'Component Management';
    
    $data['content'] = view('admin/content/component', $data);
    return view('admin/index', $data);
}
 
public function get($id)
{
    $component = $this->componentModel
        ->select('c.*, s.minimum_stock')
        ->from('component_components c')
        ->join('component_stocks s', 's.component_id = c.id', 'left')
        ->find($id);

    if(!$component) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Component not found'
        ]);
    }

    return $this->response->setJSON([
        'status' => true,
        'data' => $component
    ]);
}

public function save()
{
    $validation = \Config\Services::validation();
    $rules = [
        'kode' => [
            'rules' => 'required|is_unique[component_components.kode]',
            'errors' => [
                'required' => 'Component code is required',
                'is_unique' => 'This component code already exists'
            ]
        ],
        'nama' => 'required',
        'satuan' => 'required'
    ];

    if(!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    $data = $this->request->getPost();
    $id = $this->request->getPost('id');

    try {
        if(empty($id)) {
            // Insert new component
            $this->componentModel->insert($data);
            $componentId = $this->componentModel->getInsertID();
            
            // Initialize stock
            $this->stockModel->insert([
                'component_id' => $componentId,
                'quantity' => 0,
                'minimum_stock' => $data['minimum_stock'] ?? 0
            ]);
            
            $message = 'Component added successfully';
        } else {
            // Update existing component
            $this->componentModel->update($id, $data);
            
            // Update minimum stock
            $this->stockModel->where('component_id', $id)
                           ->set('minimum_stock', $data['minimum_stock'] ?? 0)
                           ->update();
            
            $message = 'Component updated successfully';
        }

        return redirect()->to('/component')->with('success', $message);

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: '.$e->getMessage());
    }
}

public function delete($id)
{
    try {
        $this->componentModel->delete($id);
        return redirect()->to('/component')->with('success', 'Component deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: '.$e->getMessage());
    }
}

    public function getTransactions($componentId)
    {
        $transactions = $this->transactionModel->where('component_id', $componentId)
                                             ->orderBy('created_at', 'DESC')
                                             ->findAll();
        
        return $this->response->setJSON([
            'status' => true,
            'data' => $transactions
        ]);
    }

    public function addTransaction()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'component_id' => 'required|numeric',
            'type' => 'required|in_list[in,out]',
            'quantity' => 'required|numeric|greater_than[0]',
            'reference' => 'permit_empty|max_length[100]',
            'notes' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();
        $data['created_by'] = session()->get('user_id');

        try {
            $this->transactionModel->insert($data);
            
            // Update stock
            $stock = $this->stockModel->where('component_id', $data['component_id'])->first();
            $newQuantity = ($data['type'] == 'in') 
                ? $stock['quantity'] + $data['quantity']
                : $stock['quantity'] - $data['quantity'];
            
            $this->stockModel->where('component_id', $data['component_id'])
                            ->set('quantity', $newQuantity)
                            ->update();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Transaction recorded successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    // In your Component controller
public function getAllComponents()
{
    $components = $this->componentModel->select('c.*, p.nama as product_name, pc.nama as parent_name, s.quantity, s.minimum_stock')
        ->from('component_components c')
        ->join('product p', 'p.id = c.product_id', 'left')
        ->join('component_components pc', 'pc.id = c.parent_id', 'left')
        ->join('component_stocks s', 's.component_id = c.id', 'left')
        ->orderBy('c.id', 'ASC')
        ->findAll();

    return $this->response->setJSON([
        'status' => true,
        'data' => $components
    ]);
}
// In your Component controller
public function getStock($id)
{
    $stockModel = new \App\Models\ComponentStockModel();
    $stock = $stockModel->where('component_id', $id)->first();
    
    if (!$stock) {
        // If no stock record exists, create one with default values
        $stockModel->insert([
            'component_id' => $id,
            'quantity' => 0,
            'minimum_stock' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $stock = $stockModel->where('component_id', $id)->first();
    }
    
    return $this->response->setJSON([
        'status' => true,
        'data' => (array)$stock // Ensure we're returning an array
    ]);
}



public function saveTransaction()
{
    $validation = \Config\Services::validation();
    $validation->setRules([
        'component_id' => 'required|numeric',
        'type' => 'required|in_list[in,out]',
        'quantity' => 'required|decimal',
        'minimum_stock' => 'permit_empty|decimal',
        'reference' => 'permit_empty|max_length[100]',
        'notes' => 'permit_empty'
    ]);
    
    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validation->getErrors()
        ]);
    }
    
    $data = $this->request->getPost();
    
    // Start transaction
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        $stockModel = new \App\Models\ComponentStockModel();
        $transactionModel = new \App\Models\ComponentTransactionModel();
        
        // Get stock record (ensure we get it as array)
        $stock = $stockModel->where('component_id', $data['component_id'])->first();
        
        if (!$stock) {
            // Create stock record if it doesn't exist
            $stockId = $stockModel->insert([
                'component_id' => $data['component_id'],
                'quantity' => 0,
                'minimum_stock' => $data['minimum_stock'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $stock = $stockModel->find($stockId);
        }
        
        // Convert to array if it's an object
        $stockArray = (array)$stock;
        
        // Get current quantity (with null coalescing as fallback)
        $currentQuantity = $stockArray['quantity'] ?? 0;
        $newQuantity = $currentQuantity;
        $transactionQuantity = $data['quantity'];
        
        // Update quantity based on transaction type
        if ($data['type'] === 'in') {
            $newQuantity = $currentQuantity + $transactionQuantity;
        } else {
            $newQuantity = $currentQuantity - $transactionQuantity;
            if ($newQuantity < 0) {
                throw new \RuntimeException('Insufficient stock');
            }
        }
        
        // Update stock record
        $stockModel->update($stockArray['id'], [
            'quantity' => $newQuantity,
            'minimum_stock' => $data['minimum_stock'] ?? ($stockArray['minimum_stock'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Create transaction record
        $transactionModel->insert([
            'component_id' => $data['component_id'],
            'type' => $data['type'],
            'quantity' => $transactionQuantity,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $_SESSION['auth']['id'] ?? null,
        ]);
        
        $db->transComplete();
        
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Transaction saved successfully'
        ]);
        
    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function deleteTransaction()
{
    $transactionId = $this->request->getPost('id');
    $componentId = $this->request->getPost('component_id');
    
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        // Get transaction details
        $transaction = $this->transactionModel->find($transactionId);
        
        if (!$transaction) {
            throw new \Exception('Transaction not found');
        }
        
        // Get current stock
        $stock = $this->stockModel->where('component_id', $componentId)->first();
        
        if (!$stock) {
            throw new \Exception('Stock record not found');
        }
        
        // Reverse the transaction effect
        $newQuantity = ($transaction['type'] == 'in') 
            ? $stock['quantity'] - $transaction['quantity']
            : $stock['quantity'] + $transaction['quantity'];
        
        // Update stock
        $this->stockModel->where('component_id', $componentId)
                        ->set('quantity', $newQuantity)
                        ->update();
        
        // Delete transaction
        $this->transactionModel->delete($transactionId);
        
        $db->transComplete();
        
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Transaction deleted successfully'
        ]);
        
    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function exportExcel()
{
    $startDate = $this->request->getPost('start_date');
    $endDate = $this->request->getPost('end_date');
    $includeTransactions = $this->request->getPost('include_transactions');
    
    // Load PHPExcel library
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    
    // Sheet 1: Component Summary
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Component Summary');
    
    // Header for summary sheet
    $sheet->setCellValue('A1', 'Component Summary - ' . date('Y-m-d'));
    $sheet->setCellValue('A2', 'Code');
    $sheet->setCellValue('B2', 'Name');
    $sheet->setCellValue('C2', 'Product');
    $sheet->setCellValue('D2', 'Current Stock');
    $sheet->setCellValue('E2', 'Minimum Stock');
    $sheet->setCellValue('F2', 'Unit');
    $sheet->setCellValue('G2', 'Status');
    
    // Get all components with stock
    $components = $this->componentModel
        ->select('c.*, CONCAT(p.kode, " - ", p.nama) as product_name, s.quantity, s.minimum_stock')
        ->from('component_components c')
        ->join('product p', 'p.id = c.product_id', 'left')
        ->join('component_stocks s', 's.component_id = c.id', 'left')
        ->orderBy('c.id', 'ASC')
        ->groupBy('c.id')
        ->findAll();
    
    // Populate data
    $row = 3;
    foreach ($components as $component) {
        $sheet->setCellValue('A' . $row, $component['kode']);
        $sheet->setCellValue('B' . $row, $component['nama']);
        $sheet->setCellValue('C' . $row, $component['product_name'] ?? '-');
        $sheet->setCellValue('D' . $row, $component['quantity'] ?? 0);
        $sheet->setCellValue('E' . $row, $component['minimum_stock'] ?? 0);
        $sheet->setCellValue('F' . $row, $component['satuan']);
        $sheet->setCellValue('G' . $row, $component['aktif'] == 1 ? 'Active' : 'Inactive');
        $row++;
    }
    
    // Auto size columns
    foreach (range('A', 'G') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Add styling to header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFE0E0E0']
        ]
    ];
    $sheet->getStyle('A2:G2')->applyFromArray($headerStyle);
    
    if ($includeTransactions) {
        // Sheet 2: Transaction History
        $transactionSheet = $spreadsheet->createSheet();
        $transactionSheet->setTitle('Transaction History');
        
        // Header for transaction sheet
        $transactionSheet->setCellValue('A1', 'Transaction History - ' . $startDate . ' to ' . $endDate);
        $transactionSheet->setCellValue('A2', 'Date');
        $transactionSheet->setCellValue('B2', 'Component Code');
        $transactionSheet->setCellValue('C2', 'Component Name');
        $transactionSheet->setCellValue('D2', 'Type');
        $transactionSheet->setCellValue('E2', 'Quantity');
        $transactionSheet->setCellValue('F2', 'Reference');
        $transactionSheet->setCellValue('G2', 'Notes');
        $transactionSheet->setCellValue('H2', 'Created By');
        
        // Get transactions within date range
        $transactions = $this->transactionModel
                        ->select('ct.*, c.kode as component_code, c.nama as component_name, CONCAT(u.nama_depan, " ", u.nama_belakang) as username')
                        ->from('component_transactions ct')
                        ->join('component_components c', 'c.id = ct.component_id')
                        ->join('users u', 'u.id = ct.created_by', 'left')
                        ->where('DATE(ct.created_at) >=', $startDate)
                        ->where('DATE(ct.created_at) <=', $endDate)
                        ->groupBy('ct.id') // Ganti dengan kolom primary key yang sesuai
                        ->orderBy('ct.created_at', 'DESC')
                        ->findAll();
        // Populate transaction data
        $tRow = 3;
        foreach ($transactions as $transaction) {
            $transactionSheet->setCellValue('A' . $tRow, $transaction['created_at']);
            $transactionSheet->setCellValue('B' . $tRow, $transaction['component_code']);
            $transactionSheet->setCellValue('C' . $tRow, $transaction['component_name']);
            $transactionSheet->setCellValue('D' . $tRow, strtoupper($transaction['type']));
            $transactionSheet->setCellValue('E' . $tRow, $transaction['quantity']);
            $transactionSheet->setCellValue('F' . $tRow, $transaction['reference'] ?? '-');
            $transactionSheet->setCellValue('G' . $tRow, $transaction['notes'] ?? '-');
            $transactionSheet->setCellValue('H' . $tRow, $transaction['username'] ?? 'System');
            $tRow++;
        }
        
        // Auto size columns
        foreach (range('A', 'H') as $column) {
            $transactionSheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add styling to header
        $transactionSheet->getStyle('A2:H2')->applyFromArray($headerStyle);
    }
    
    // Set the active sheet back to the first one
    $spreadsheet->setActiveSheetIndex(0);
    
    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="component_stock_report_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}
}