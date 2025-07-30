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
        ->select('c.*, p.nama as product_name, s.quantity, s.minimum_stock')
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
}