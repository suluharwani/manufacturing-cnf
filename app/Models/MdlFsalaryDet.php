<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryDet extends Model
{
    protected $table = 'salary_det';
    protected $primaryKey = 'id';
    protected $allowedFields = ['day', 'payperhour', 'basic_salary', 'work_start', 'work_end', 'overtime_start', 'overtime_end', 'work_break', 'overtime_break', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false; // Set to true if you want automatic timestamps
}
