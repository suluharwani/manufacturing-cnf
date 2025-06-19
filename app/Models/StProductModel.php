<?php namespace App\Models;

use CodeIgniter\Model;

class StProductModel extends Model
{
    protected $table      = 'st_product';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['product_id', 'quantity', 'location_id', 'label_code', 'status', 'pi_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getAvailableStock($productId)
    {
        $initial = $this->db->table('st_initial')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->get()
        ->getRow()->quantity;

    // Get total stock in (initial stock + stock in adjustments)
    $stockIn = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'in')
        ->get()
        ->getRow()->quantity;

    // Get total stock out (stock out adjustments)
    $stockOut = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'out')
        ->get()
        ->getRow()->quantity;

    // Get total booked stock
    $bookedStock = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('status', 'booked')
        ->get()
        ->getRow()->quantity;

    // Calculate available stock
    $availableStock =($initial ?? 0)+ ($stockIn ?? 0) - ($stockOut ?? 0) - ($bookedStock ?? 0);

    return max(0, $availableStock);
    }
    
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
}