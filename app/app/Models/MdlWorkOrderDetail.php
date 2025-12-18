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
    // First, get the WO ID from material_requisition
    $mrData = $this->db->table('material_requisition')
        ->select('id_wo')
        ->where('id', $idMR)
        ->get()
        ->getRowArray();
    
    if (!$mrData) {
        return [];
    }
    
    $woId = $mrData['id_wo'];
    
    // Get all products from work order detail for this WO
    $products = $this->db->table('work_order_detail wod')
        ->select('wod.product_id, wod.finishing_id, wod.quantity, p.kode as product_code, p.nama as product_name, f.name as finishing_name')
        ->join('product p', 'wod.product_id = p.id')
        ->join('finishing f', 'wod.finishing_id = f.id', 'left')
        ->where('wod.wo_id', $woId)
        ->get()
        ->getResultArray();
    
    $result = [];
    
    foreach ($products as $product) {
        $productId = $product['product_id'];
        $finishingId = $product['finishing_id'];
        $quantity = $product['quantity'];
        $finishingName = $product['finishing_name'];
        
        // QUERY 1: Get materials from regular BOM (TANPA memperhatikan id_modul)
        $regularBOM = $this->getRegularBOMData($productId, $quantity, $idMR);
        
        // QUERY 2: Get materials from finishing BOM
        $finishingBOM = $this->getFinishingBOMData($productId, $finishingId, $quantity, $finishingName, $idMR);
        
        // Combine both results
        $result = array_merge($result, $regularBOM, $finishingBOM);
    }
    
    return $result;
}

// QUERY 1: Get Regular BOM Data (TANPA filter id_modul)
private function getRegularBOMData($productId, $quantity, $idMR)
{
    $builder = $this->db->table('billofmaterial bom');
    
    $builder->select("
        m.id AS material_id,
        m.name AS material_name,
        m.kode AS material_code,
        s.nama AS satuan,
        s.kode AS c_satuan,
        t.nama AS type,
        bom.penggunaan AS penggunaan,
        {$quantity} AS quantity,
        FORMAT(bom.penggunaan * {$quantity}, 3) AS total_penggunaan,
        NULL AS finishing_name,
        NULL AS finishing_id,
        NULL AS modul_id,
        md.kite AS kite,
        COALESCE(
            (SELECT SUM(DISTINCT mrp.jumlah)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrp.id_material = m.id AND mr.id = {$idMR}
            ), 
            0
        ) AS terpenuhi,
        ROUND(
            (bom.penggunaan * {$quantity} - 
            COALESCE(
                (SELECT SUM(DISTINCT mrp.jumlah)
                 FROM material_requisition_progress mrp
                 JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
                 JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
                 WHERE mrp.id_material = m.id AND mr.id = {$idMR}
                ), 
                0
            )), 3) AS remaining_quantity,
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 3)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status = 1 AND mr.id = {$idMR}
            ), 
            0
        ) AS total_requisition,
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 3)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status != 1 AND mr.id = {$idMR}
            ), 
            0
        ) AS total_requisition_unposting,
        COALESCE(
            (SELECT ROUND(SUM(mrp.jumlah), 3)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             WHERE mrl.id_material = m.id AND mrl.id_material_requisition = {$idMR}
            ), 
            0
        ) AS total_progress,
        'regular' AS material_type,
        -- Data Stock
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
    
    $builder->join('materials m', 'bom.id_material = m.id');
    $builder->join('materials_detail md', 'm.id = md.material_id', 'left');
    $builder->join('satuan s', 's.id = md.satuan_id', 'left');
    $builder->join('type t', 't.id = md.type_id', 'left');
    
    // PERBAIKAN: Hanya filter by product_id, TANPA kondisi id_modul
    $builder->where('bom.id_product', $productId);
    
    return $builder->get()->getResultArray();
}

// QUERY 2: Get Finishing BOM Data (tetap sama)
private function getFinishingBOMData($productId, $finishingId, $quantity, $finishingName, $idMR)
{
    // Jika tidak ada finishing_id, return array kosong
    if (!$finishingId) {
        return [];
    }
    
    $builder = $this->db->table('billofmaterialfinishing bomf');
    
    $builder->select("
        m.id AS material_id,
        m.name AS material_name,
        m.kode AS material_code,
        s.nama AS satuan,
        s.kode AS c_satuan,
        t.nama AS type,
        bomf.penggunaan AS penggunaan,
        {$quantity} AS quantity,
        FORMAT(bomf.penggunaan * {$quantity}, 3) AS total_penggunaan,
        '{$finishingName}' AS finishing_name,
        {$finishingId} AS finishing_id,
        {$finishingId} AS modul_id,
        md.kite AS kite,
        COALESCE(
            (SELECT SUM(DISTINCT mrp.jumlah)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrp.id_material = m.id AND mr.id = {$idMR}
            ), 
            0
        ) AS terpenuhi,
        ROUND(
            (bomf.penggunaan * {$quantity} - 
            COALESCE(
                (SELECT SUM(DISTINCT mrp.jumlah)
                 FROM material_requisition_progress mrp
                 JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
                 JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
                 WHERE mrp.id_material = m.id AND mr.id = {$idMR}
                ), 
                0
            )), 3) AS remaining_quantity,
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 3)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status = 1 AND mr.id = {$idMR}
            ), 
            0
        ) AS total_requisition,
        COALESCE(
            (SELECT ROUND(SUM(mrl.jumlah), 3)
             FROM material_requisition_list mrl
             JOIN material_requisition mr ON mrl.id_material_requisition = mr.id
             WHERE mrl.id_material = m.id AND mr.status != 1 AND mr.id = {$idMR}
            ), 
            0
        ) AS total_requisition_unposting,
        COALESCE(
            (SELECT ROUND(SUM(mrp.jumlah), 3)
             FROM material_requisition_progress mrp
             JOIN material_requisition_list mrl ON mrp.id_material_requisition_list = mrl.id
             WHERE mrl.id_material = m.id AND mrl.id_material_requisition = {$idMR}
            ), 
            0
        ) AS total_progress,
        'finishing' AS material_type,
        -- Data Stock
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
    
    $builder->join('materials m', 'bomf.id_material = m.id');
    $builder->join('materials_detail md', 'm.id = md.material_id', 'left');
    $builder->join('satuan s', 's.id = md.satuan_id', 'left');
    $builder->join('type t', 't.id = md.type_id', 'left');
    
    // Conditions for finishing BOM
    $builder->where('bomf.id_product', $productId);
    $builder->where('bomf.id_modul', $finishingId);
    
    return $builder->get()->getResultArray();
}
}
