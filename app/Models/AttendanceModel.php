<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'att_log';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sn', 'scan_date', 'pin', 'verifymode','inoutmode','reserved','work_code','att_id'];

        // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
public function getAttendanceData($pin, $id, $startDate, $endDate)
{
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));

    return $this->where('pin', $pin)
                ->where('scan_date >=', $startDate)
                ->where('scan_date <=', $endDate)
                ->findAll();
}
}
