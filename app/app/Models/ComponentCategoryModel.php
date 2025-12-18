<?php

namespace App\Models;

use CodeIgniter\Model;

class ComponentCategoryModel extends Model
{
    protected $table = 'component_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'description'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
}