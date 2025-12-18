<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlFbankAccount extends Model
{
    protected $table = 'bank_account';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_name', 'bank', 'account_number', 'updated_at', 'deleted_at', 'created_at'];
    protected $useTimestamps = false;
}
