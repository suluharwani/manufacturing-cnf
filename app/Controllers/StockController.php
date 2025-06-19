<?php namespace App\Controllers;

use App\Models\StProductModel;
use App\Models\StMovementModel;
use App\Models\StInitialModel;
use App\Models\MdlProduct;
use App\Models\LocationModel;
use App\Models\ProformaInvoice;

class StockController extends BaseController
{
    protected $stProductModel;
    protected $stMovementModel;
    protected $stInitialModel;
    protected $productModel;
    protected $locationModel;
    protected $PiModel;

    public function __construct()
    {
        $this->stProductModel = new StProductModel();
        $this->stMovementModel = new StMovementModel();
        $this->stInitialModel = new StInitialModel();
        $this->productModel = new MdlProduct();
        $this->locationModel = new LocationModel();
        $this->PiModel = new ProformaInvoice();
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
            'validation' => \Config\Services::validation()
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
}