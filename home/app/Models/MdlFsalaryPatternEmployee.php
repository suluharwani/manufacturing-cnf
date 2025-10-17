<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryPatternEmployee extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'salary_pattern_employee';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_salary_pattern', 'id_employee', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
