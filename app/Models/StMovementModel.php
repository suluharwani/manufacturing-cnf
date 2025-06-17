<?php namespace App\Models;

use CodeIgniter\Model;

class StMovementModel extends Model
{
    protected $table      = 'st_movement';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['product_id', 'quantity', 'movement_type', 'reference_id', 
                              'reference_type', 'from_location', 'to_location', 'notes', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function logMovement($data)
    {
        return $this->insert($data);
    }
    
    public function getProductHistory($productId, $limit = 100)
    {
        return $this->select('st_movement.*, from_loc.name as from_location_name, to_loc.name as to_location_name, users.username')
                   ->join('locations as from_loc', 'from_loc.id = st_movement.from_location', 'left')
                   ->join('locations as to_loc', 'to_loc.id = st_movement.to_location', 'left')
                   ->join('users', 'users.id = st_movement.created_by', 'left')
                   ->where('product_id', $productId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}