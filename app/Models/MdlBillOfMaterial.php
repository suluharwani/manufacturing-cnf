<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlBillOfMaterial extends Model
{
    protected $table            = 'billofmaterial';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'id_modul','id_product', 'id_material','penggunaan','created_at','updated_at','deleted_at'];

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
    
      public function getCombinedBOM($id_product, $id_modul)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                bom.id,
                bom.id_product,
                bom.id_material,
                m.kode,
                m.name,
                md.kite,
                s.nama as satuan_nama,
                bom.penggunaan,
                bom.updated_at,
                bom.deleted_at,
                bom.created_at,
                bom.id_modul
            FROM 
                billofmaterial bom
            LEFT JOIN materials m ON bom.id_material = m.id
            LEFT JOIN materials_detail md ON m.id = md.material_id
            LEFT JOIN satuan s ON md.satuan_id = s.id
            WHERE 
                bom.id_product = ?
                AND bom.deleted_at IS NULL

            UNION ALL

            SELECT 
                bom.id,
                bom.id_product,
                bom.id_material,
                m.kode,
                m.name,
                md.kite,
                s.nama as satuan_nama,
                bom.penggunaan,
                bom.updated_at,
                bom.deleted_at,
                bom.created_at,
                bom.id_modul
            FROM 
                billofmaterialfinishing bom
            LEFT JOIN materials m ON bom.id_material = m.id
            LEFT JOIN materials_detail md ON m.id = md.material_id
            LEFT JOIN satuan s ON md.satuan_id = s.id
            WHERE 
                bom.id_modul = ?
                AND bom.deleted_at IS NULL
        ";

        $query = $db->query($sql, [$id_product, $id_modul]);
        return $query->getResultArray();
    }
}
