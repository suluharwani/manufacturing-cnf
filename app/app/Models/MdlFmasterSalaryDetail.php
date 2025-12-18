<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFmasterSalaryDetail extends Model
{
    protected $table = 'master_salary_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_master_salary', 'id_employee', 'id_salary_det', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
