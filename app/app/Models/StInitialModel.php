<?php namespace App\Models;

use CodeIgniter\Model;

class StInitialModel extends Model
{
    protected $table      = 'st_initial';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['product_id', 'quantity', 'location_id','finishing_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getInitialStock($productId)
    {
        return $this->where('product_id', $productId)
                   ->first();
    }
}