<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlMaterial extends Model
{
    protected $table            = 'materials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','kode','name','picture','updated_at','deleted_at','created_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    public function getMaterialWithDetails($id)
    {
        $builder = $this->db->table($this->table);
        
        // Define the joins
        $builder->select('materials.*,materials_detail.kite as kite, materials_detail.type_id, materials_detail.satuan_id, type.nama as nama_type, satuan.kode as kode_satuan, satuan.nama as satuan, materials_detail.hscode as hscode');
        $builder->join('materials_detail', 'materials_detail.material_id = materials.id', 'left');
        $builder->join('type', 'type.id = materials_detail.type_id', 'left');
        $builder->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left');
        
        // Apply the where condition
        $builder->where('materials.id', $id);
        
        // Execute the query and return the result
        return $builder->get()->getRowArray();
    }
}
