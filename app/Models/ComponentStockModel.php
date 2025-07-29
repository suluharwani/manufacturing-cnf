<?php

namespace App\Models;

use CodeIgniter\Model;

class ComponentStockModel extends Model
{
    protected $table = 'component_stocks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['component_id', 'quantity', 'minimum_stock'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}