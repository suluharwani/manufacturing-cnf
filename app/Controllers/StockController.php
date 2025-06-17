<?php namespace App\Controllers;

use App\Models\StProductModel;
use App\Models\StMovementModel;
use App\Models\StInitialModel;
use App\Models\MdlProduct;
use App\Models\LocationModel;

class StockController extends BaseController
{
    protected $stProductModel;
    protected $stMovementModel;
    protected $stInitialModel;
    protected $productModel;
    protected $locationModel;

    public function __construct()
    {
        $this->stProductModel = new StProductModel();
        $this->stMovementModel = new StMovementModel();
        $this->stInitialModel = new StInitialModel();
        $this->productModel = new MdlProduct();
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        $data['title'] = 'Stock Management';
        $data['products'] = $this->productModel->findAll();
        $data['content'] = view('admin/content/product_stock',$data);
        return view('admin/index', $data);
    }

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
            'title' => 'Stock Detail - ' . $product['name'],
            'product' => $product,
            'initial_stock' => $initialStock ? $initialStock['quantity'] : 0,
            'available' => $available,
            'booked' => $booked,
            'total' => $available + $booked,
            'stock_details' => $this->stProductModel->getStockDetails($productId),
            'movement_history' => $this->stMovementModel->getProductHistory($productId),
            'locations' => $this->locationModel->findAll()
        ];

        return view('stock/view', $data);
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
            $this->stInitialModel->update($existing['id'], $data);
        } else {
            $this->stInitialModel->insert($data);
        }

        // Add to current stock
        $stockData = [
            'product_id' => $productId,
            'quantity' => $data['quantity'],
            'location_id' => $data['location_id'],
            'label_code' => 'INITIAL',
            'status' => 'available'
        ];
        
        $this->stProductModel->insert($stockData);
        
        // Log movement
        $movementData = [
            'product_id' => $productId,
            'quantity' => $data['quantity'],
            'movement_type' => 'in',
            'reference_type' => 'initial_stock',
            'to_location' => $data['location_id'],
            'notes' => 'Initial stock setup',
            'created_by' => user_id()
        ];
        
        $this->stMovementModel->logMovement($movementData);

        return redirect()->to("/stock/view/$productId")->with('message', 'Initial stock set successfully');
    }

    public function adjustStock($productId)
    {
        $rules = [
            'adjustment_type' => 'required|in_list[in,out]',
            'quantity' => 'required|numeric|greater_than[0]',
            'location_id' => 'permit_empty|numeric',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = $this->request->getPost('quantity');
        $locationId = $this->request->getPost('location_id');
        $notes = $this->request->getPost('notes');

        if ($adjustmentType === 'in') {
            $stockData = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'location_id' => $locationId,
                'label_code' => 'ADJ-' . date('YmdHis'),
                'status' => 'available'
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
            'created_by' => user_id()
        ];
        
        $this->stMovementModel->logMovement($movementData);

        return redirect()->to("/stock/view/$productId")->with('message', 'Stock adjusted successfully');
    }
}