<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlAttLog extends Model
{
    protected $table = 'att_log';
    // protected $primaryKey = 'att_id';
    protected $allowedFields = ['sn', 'scan_date', 'pin', 'verifymode', 'inoutmode', 'reserved', 'work_code', 'att_id'];
    protected $useTimestamps = false;
}
