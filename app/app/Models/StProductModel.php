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

// Di StProductModel.php - Optimasi getAvailableStock
public function getAvailableStock($productId, $finishingId = null)
{
    // Single query approach
    $query = "
        SELECT 
            COALESCE(initial.initial_qty, 0) + 
            COALESCE(movement_in.in_qty, 0) - 
            COALESCE(movement_out.out_qty, 0) - 
            COALESCE(booked.booked_qty, 0) as available_stock
        FROM (SELECT ? as product_id) as p
        LEFT JOIN (
            SELECT product_id, finishing_id, SUM(quantity) as initial_qty
            FROM st_initial 
            WHERE product_id = ?
            " . ($finishingId ? " AND finishing_id = ?" : " AND finishing_id IS NULL") . "
        ) initial ON initial.product_id = p.product_id
        LEFT JOIN (
            SELECT product_id, finishing_id, SUM(quantity) as in_qty
            FROM st_movement 
            WHERE product_id = ? AND movement_type = 'in'
            " . ($finishingId ? " AND finishing_id = ?" : " AND finishing_id IS NULL") . "
        ) movement_in ON movement_in.product_id = p.product_id
        LEFT JOIN (
            SELECT product_id, finishing_id, SUM(quantity) as out_qty
            FROM st_movement 
            WHERE product_id = ? AND movement_type = 'out'
            " . ($finishingId ? " AND finishing_id = ?" : " AND finishing_id IS NULL") . "
        ) movement_out ON movement_out.product_id = p.product_id
        LEFT JOIN (
            SELECT product_id, finishing_id, SUM(quantity) as booked_qty
            FROM st_movement 
            WHERE product_id = ? AND status = 'booked'
            " . ($finishingId ? " AND finishing_id = ?" : " AND finishing_id IS NULL") . "
        ) booked ON booked.product_id = p.product_id
    ";

    $params = [$productId, $productId];
    if ($finishingId) {
        $params = array_merge($params, [$finishingId, $productId, $finishingId, $productId, $finishingId, $productId, $finishingId]);
    } else {
        $params = array_merge($params, [$productId, $productId, $productId]);
    }

    $result = $this->db->query($query, $params)->getRow();
    return max(0, $result->available_stock ?? 0);
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