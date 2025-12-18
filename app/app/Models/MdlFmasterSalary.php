<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFmasterSalary extends Model
{
    protected $table = 'master_salary';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'start_date', 'end_date', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
