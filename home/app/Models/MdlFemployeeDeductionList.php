<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFemployeeDeductionList extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'employee_deduction_list';
    protected $primaryKey = 'id';
    protected $allowedFields = ['amount','employee_id', 'deduction_id', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
