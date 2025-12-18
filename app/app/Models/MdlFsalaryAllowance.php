<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFsalaryAllowance extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'salary_allowance';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','Kode', 'Nama','Status', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
