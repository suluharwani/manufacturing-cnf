<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'att_log';

public function getAttendanceData($pin, $id, $startDate, $endDate)
{
    return $this->where('pin', $pin)
                ->where('scan_date >=', $startDate)
                ->where('scan_date <=', $endDate)
                ->findAll();
}
}
