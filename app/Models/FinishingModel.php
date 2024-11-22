<?php

namespace App\Models;

use CodeIgniter\Model;

class FinishingModel extends Model
{
    protected $table = 'finishing';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'name', 'description', 'picture'];
}
