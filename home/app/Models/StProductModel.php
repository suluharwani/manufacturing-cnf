<?php namespace App\Models;

use CodeIgniter\Model;

class StProductModel extends Model
{
    protected $table      = 'st_product';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['product_id', 'quantity', 'location_id', 'label_code', 'status', 'pi_id','finishing_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

public function getAvailableStock($productId, $finishingId = null)
{
    $builder = $this->db->table('st_initial')
        ->selectSum('quantity')
        ->where('product_id', $productId);
    
    if ($finishingId) {
        $builder->where('finishing_id', $finishingId);
    }
    
    $initial = $builder->get()->getRow()->quantity;

    // Get total stock in
    $builder = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'in');
    
    if ($finishingId) {
        $builder->where('finishing_id', $finishingId);
    }
    
    $stockIn = $builder->get()->getRow()->quantity;

    // Get total stock out
    $builder = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'out');
    
    if ($finishingId) {
        $builder->where('finishing_id', $finishingId);
    }
    
    $stockOut = $builder->get()->getRow()->quantity;

    // Get total booked stock
    $builder = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('status', 'booked');
    
    if ($finishingId) {
        $builder->where('finishing_id', $finishingId);
    }
    
    $bookedStock = $builder->get()->getRow()->quantity;

    return max(0, ($initial ?? 0) + ($stockIn ?? 0) - ($stockOut ?? 0) - ($bookedStock ?? 0));
}

// Similarly update getBookedStock(), getStockByLocation(), etc.
    
    public function getBookedStock($productId)
    {
        return $this->db->table('st_movement')->where('product_id', $productId)
                   ->where('movement_type', 'booked')
                   ->selectSum('quantity')
                   ->get()
                   ->getRow()->quantity ?? 0;
    }
    
    public function getStockByLocation($productId, $locationId)
    {
        return $this->where('product_id', $productId)
                   ->where('location_id', $locationId)
                   ->where('status', 'available')
                   ->selectSum('quantity')
                   ->get()
                   ->getRow()->quantity ?? 0;
    }

    public function getStockDetails($productId)
    {
        return $this->select('st_product.*, locations.name as location_name, proforma_invoice.invoice_number')
                   ->join('locations', 'locations.id = st_product.location_id', 'left')
                   ->join('proforma_invoice', 'proforma_invoice.id = st_product.pi_id', 'left')
                   ->where('product_id', $productId)
                   ->findAll();
    }
    public function getStockAtLocation($productId, $locationId, $finishingId = null)
{
    $builder = $this->builder();
    
    $builder->select([
        'COALESCE(SUM(quantity), 0) as total_stock',
        'COALESCE(SUM(CASE WHEN status = "available" THEN quantity ELSE 0 END), 0) as available_stock',
        'COALESCE(SUM(CASE WHEN status = "booked" THEN quantity ELSE 0 END), 0) as booked_stock'
    ]);
    
    $builder->where('product_id', $productId);
    $builder->where('location_id', $locationId);
    
    if ($finishingId !== null) {
        $builder->where('finishing_id', $finishingId);
    } else {
        $builder->where('finishing_id IS NULL');
    }
    
    $result = $builder->get()->getRowArray();
    
    return [
        'total_stock' => (int)$result['total_stock'] ?? 0,
        'available_stock' => (int)$result['available_stock'] ?? 0,
        'booked_stock' => (int)$result['booked_stock'] ?? 0
    ];
}
}