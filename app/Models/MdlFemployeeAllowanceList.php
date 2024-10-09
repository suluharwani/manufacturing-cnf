<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFemployeeAllowanceList extends Model
{
    protected $table = 'employee_allowance_list';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'allowance_id', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
