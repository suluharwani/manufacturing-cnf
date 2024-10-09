<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryAllowance extends Model
{
    protected $table = 'salary_allowance';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'allowance', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
