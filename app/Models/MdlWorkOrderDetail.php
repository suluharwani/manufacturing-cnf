<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlWorkOrderDetail extends Model
{
    protected $table            = 'work_order_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','wo_id','finishing_id','product_id','quantity','updated_at','deleted_at','created_at'];

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

public function getMaterialRequisitionData($idMR)
{
    $builder = $this->db->table('work_order_detail wod');
    
    $builder->select("
        m.id AS material_id,
        m.name AS material_name,
        s.kode AS c_satuan,
        s.nama AS satuan,
        COALESCE(bom.penggunaan, bomf.penggunaan) AS penggunaan,
        wod.quantity,
        COALESCE(
            (SELECT SUM(DISTINCT mrp.jumlah)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrp.id_material = m.id AND mr.id = $idMR
            ), 
            0
        ) AS terpenuhi,
        ROUND(
            (COALESCE(bom.penggunaan, bomf.penggunaan) * wod.quantity - 
            COALESCE(
                (SELECT SUM(DISTINCT mrp.jumlah)
                 FROM material_requisition_progress mrp
                 JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
                 JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
                 WHERE mrp.id_material = m.id AND mr.id = $idMR
                ), 
                0
            )),2) AS remaining_quantity,
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 2)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status = 1 AND mr.id = $idMR
            ), 
            0
        ) AS total_requisition,
        /* Perbaikan untuk total_requisition_unposting (status ≠ 1) */
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 2)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status != 1 AND mr.id = $idMR
            ), 
            0
        ) AS total_requisition_unposting,
        /* Tambahan field untuk total progress per material_requisition_list */
        COALESCE(
            (SELECT ROUND(SUM(mrp.jumlah), 2)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             WHERE mrl.id_material = m.id AND mrl.id_material_requisition = $idMR
            ), 
            0
        ) AS total_progress,
        CASE WHEN bom.id IS NOT NULL THEN 'regular' ELSE 'finishing' END AS material_type,
        (
            SELECT COALESCE(stock.stock_awal, 0) 
            FROM stock 
            WHERE stock.id_material = m.id 
            LIMIT 1
        ) AS stock_awal,
        (
            SELECT COALESCE(
                (SELECT SUM(pd.jumlah) FROM pembelian_detail pd
                 JOIN pembelian p ON p.id = pd.id_pembelian
                 WHERE p.posting = 1 AND pd.id_material = m.id) +
                (SELECT SUM(mrl.jumlah) FROM material_return_list mrl
                 JOIN material_return mr ON mr.id = mrl.id_material_return
                 WHERE mr.status = 1 AND mrl.id_material = m.id), 
            0)
        ) AS total_in,
        (
            SELECT COALESCE(
                (SELECT SUM(-mdl.jumlah) FROM material_destruction_list mdl
                 JOIN material_destruction md ON md.id = mdl.id_material_destruction
                 WHERE md.status = 1 AND mdl.id_material = m.id) +
                (SELECT SUM(-mrp.jumlah) FROM material_requisition_progress mrp
                 JOIN material_requisition_list mrl ON mrl.id = mrp.id_material_requisition_list
                 JOIN material_requisition mr ON mr.id = mrl.id_material_requisition
                 WHERE mr.status = 1 AND mrp.id_material = m.id), 
            0)
        ) AS total_out,
        (
            SELECT COALESCE(
                (SELECT SUM(sol.jumlah_akhir - sol.jumlah_awal) FROM stock_opname_list sol
                 JOIN stock_opname so ON so.id = sol.id_stock_opname
                 WHERE so.status = 1 AND sol.id_material = m.id), 
            0)
        ) AS so,
        (
            SELECT COALESCE(stock.stock_awal, 0) + 
            COALESCE(
                (SELECT SUM(pd.jumlah) FROM pembelian_detail pd
                 JOIN pembelian p ON p.id = pd.id_pembelian
                 WHERE p.posting = 1 AND pd.id_material = m.id) +
                (SELECT SUM(mrl.jumlah) FROM material_return_list mrl
                 JOIN material_return mr ON mr.id = mrl.id_material_return
                 WHERE mr.status = 1 AND mrl.id_material = m.id) +
                (SELECT SUM(-mdl.jumlah) FROM material_destruction_list mdl
                 JOIN material_destruction md ON md.id = mdl.id_material_destruction
                 WHERE md.status = 1 AND mdl.id_material = m.id) +
                (SELECT SUM(-mrp.jumlah) FROM material_requisition_progress mrp
                 JOIN material_requisition_list mrl ON mrl.id = mrp.id_material_requisition_list
                 JOIN material_requisition mr ON mr.id = mrl.id_material_requisition
                 WHERE mr.status = 1 AND mrp.id_material = m.id) +
                (SELECT SUM(sol.jumlah_akhir - sol.jumlah_awal) FROM stock_opname_list sol
                 JOIN stock_opname so ON so.id = sol.id_stock_opname
                 WHERE so.status = 1 AND sol.id_material = m.id), 
            0)
            FROM stock 
            WHERE stock.id_material = m.id 
            LIMIT 1
        ) AS total_stock
    ");
    
    $builder->join('billofmaterial bom', 'wod.product_id = bom.id_product', 'left');
    $builder->join('billofmaterialfinishing bomf', 'wod.product_id = bomf.id_product', 'left');
    $builder->join('materials m', 'm.id = COALESCE(bom.id_material, bomf.id_material)');
    $builder->join('materials_detail md', 'm.id = md.material_id');
    $builder->join('satuan s', 's.id = md.satuan_id');
    $builder->join('material_requisition mr', 'mr.id_wo = wod.wo_id');
    $builder->where('mr.id', $idMR);
    $builder->groupBy('m.id, m.name, s.kode, s.nama, COALESCE(bom.penggunaan, bomf.penggunaan), wod.quantity');
    $builder->orderBy('m.name');
    
    return $builder->get()->getResultArray();
}
}
