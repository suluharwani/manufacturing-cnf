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
        return $this->where('product_id', $productId)
                   ->where('status', 'available')
                   ->selectSum('quantity')
                   ->get()
                   ->getRow()->quantity ?? 0;
    }
    
    public function getBookedStock($productId)
    {
        return $this->where('product_id', $productId)
                   ->where('status', 'booked')
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
        return $this->select('st_product.*, locations.name as location_name, proforma_invoice.pi_number')
                   ->join('locations', 'locations.id = st_product.location_id', 'left')
                   ->join('proforma_invoice', 'proforma_invoice.id = st_product.pi_id', 'left')
                   ->where('product_id', $productId)
                   ->findAll();
    }
}