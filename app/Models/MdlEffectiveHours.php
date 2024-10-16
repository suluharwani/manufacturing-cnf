<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlEffectiveHours extends Model
{
    protected $DBGroup          = 'tests';
    protected $table            = 'effectivehours';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'day',
        'work_start',
        'work_end',
        'work_break',
        'work_break_end',
        'overtime_start_1',
        'overtime_end_1',
        'overtime_break_1',
        'overtime_break_end_1',
        'overtime_start_2',
        'overtime_end_2',
        'overtime_break_2',
        'overtime_break_end_2',
        'overtime_start_3',
        'overtime_end_3',
        'overtime_break_3',
        'overtime_break_end_3',
        'updated_at',
        'deleted_at',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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
}
