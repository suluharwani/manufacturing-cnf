<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryPattern extends Model
{
    protected $table = 'salary_pattern';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pattern_code', 'employee_cat_id', 'pattern_name', 'salary_det_id', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
