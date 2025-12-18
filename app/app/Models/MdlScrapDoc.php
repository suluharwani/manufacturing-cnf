<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlScrapDoc extends Model
{
    protected $table            = 'scrap_doc';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["id","code","document_bc","id_dept","id_wo","id_user","remarks","created_at","updated_at","deleted_at","status"];

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

    public function generateCode()
{
    $prefix = 'SC' . date('Ymd') . '-';
    
    // Get the last code with today's prefix
    $lastCode = $this->select('code')
                     ->like('code', $prefix, 'after')
                     ->orderBy('code', 'DESC')
                     ->first();
    
    if ($lastCode) {
        // Extract the sequential number and increment
        $lastNumber = (int) substr($lastCode['code'], strlen($prefix));
        $sequential = $lastNumber + 1;
    } else {
        // First code of the day
        $sequential = 1;
    }
    
    // Format the sequential number with leading zeros
    $sequentialNumber = str_pad($sequential, 2, '0', STR_PAD_LEFT);
    
    return $prefix . $sequentialNumber;
}
}
