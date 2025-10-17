<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlStock extends Model
{
    protected $table            = 'stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','id_material','stock_awal','stock_masuk','stock_keluar','selisih_stock_opname','price','id_currency','created_at','updated_at','deleted_at'];

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
     public function getCurrentStock($materialId)
    {
        $result = $this->selectSum('stock_masuk', 'total_masuk')
            ->selectSum('stock_keluar', 'total_keluar')
            ->selectSum('selisih_stock_opname', 'total_selisih')
            ->where('id_material', $materialId)
            ->where('deleted_at', null)
            ->first();

        $stockAwal = $this->select('stock_awal')
            ->where('id_material', $materialId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'asc')
            ->first();

        $currentStock = ($stockAwal['stock_awal'] ?? 0) + 
                       ($result['total_masuk'] ?? 0) - 
                       ($result['total_keluar'] ?? 0) + 
                       ($result['total_selisih'] ?? 0);

        return $currentStock;
    }

    public function updateStockAfterOpname($materialId, $selisih)
    {
        $this->save([
            'id_material' => $materialId,
            'selisih_stock_opname' => $selisih,
            'stock_masuk' => 0,
            'stock_keluar' => 0,
            'stock_awal' => 0
        ]);
    }
}
