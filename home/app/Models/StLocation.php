<?php namespace App\Models;

use CodeIgniter\Model;

class StLocation extends Model
{
    protected $table      = 'locations';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['code', 'name', 'type', 'description', 'parent_id', 'is_active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'code' => 'required|max_length[20]|is_unique[locations.code,id,{id}]',
        'name' => 'required|max_length[100]',
        'type' => 'required|in_list[warehouse,production,display,quarantine,other]',
        'parent_id' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'code' => [
            'required' => 'Location code is required',
            'is_unique' => 'This location code already exists',
            'max_length' => 'Code cannot exceed 20 characters'
        ],
        'name' => [
            'required' => 'Location name is required',
            'max_length' => 'Name cannot exceed 100 characters'
        ],
        'type' => [
            'required' => 'Location type is required',
            'in_list' => 'Please select a valid location type'
        ]
    ];
    
    protected $skipValidation = false;
    
    public function getActiveLocations($type = null)
    {
        $builder = $this->where('is_active', 1);
        if ($type) {
            $builder->where('type', $type);
        }
        return $builder->findAll();
    }
    
    public function getParentLocations($excludeId = null)
    {
        $builder = $this->where('is_active', 1);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->findAll();
    }
    
    public function getLocationTypes()
    {
        return [
            'warehouse' => 'Warehouse',
            'production' => 'Production',
            'display' => 'Display',
            'quarantine' => 'Quarantine',
            'other' => 'Other'
        ];
    }
       public function getStockInLocation($locationId)
{
    return $this->db->table('st_movement')
        ->select('product.id, product.kode as product_code, product.nama as product_name, 
                 SUM(st_movement.quantity) + COALESCE(SUM(st_initial.quantity), 0) as total_quantity')
        ->join('product', 'product.id = st_movement.product_id')
        ->join('st_initial', 'st_initial.product_id = product.id AND st_initial.location_id = '.$locationId, 'left')
        ->where('st_movement.to_location', $locationId)
        ->where('st_movement.movement_type', 'in')
        ->groupBy('product.id, product.kode, product.nama')
        ->get()
        ->getResultArray();
}
public function getAvailableStockForTransfer($productId, $fromLocationId)
{
    return $this->db->table('stock_movements')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('to_location_id', $fromLocationId)
        ->where('status', 'available')
        ->get()
        ->getRow()->quantity;
}
}