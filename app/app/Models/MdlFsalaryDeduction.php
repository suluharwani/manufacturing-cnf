<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryDeduction extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'salary_deduction';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','Kode', 'Nama','Status', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
