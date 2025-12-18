<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFemployeeCategory extends Model
{
    protected $table = 'employee_category';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_name', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
