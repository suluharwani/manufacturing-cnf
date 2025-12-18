<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlMaterialRequest extends Model
{
    protected $table            = 'material_request';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode','dept_id', 'id_pi', 'status', 'remarks', 'created_at', 'updated_at', 'deleted_at'];

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
    // In MdlMaterialRequest model
public function generateCode()
{
    $prefix = 'PR' . date('Y-m-d') . '-';
    
    // Cari nomor urut terakhir hari ini
    $lastCode = $this->select('kode')
                    ->like('kode', $prefix, 'after')
                    ->orderBy('kode', 'DESC')
                    ->first();
    
    if ($lastCode) {
        // Ambil nomor urut dan increment
        $lastNumber = (int) substr($lastCode['kode'], strlen($prefix));
        $sequential = $lastNumber + 1;
    } else {
        // Jika belum ada kode hari ini
        $sequential = 1;
    }
    
    // Format nomor urut dengan leading zero
    $sequentialNumber = str_pad($sequential, 3, '0', STR_PAD_LEFT);
    
    return $prefix . $sequentialNumber;
}
}
