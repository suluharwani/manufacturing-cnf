<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOpnameModel extends Model
{
    protected $table = 'stock_opname';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'id_dept', 'id_user', 'remarks', 'status', 'deleted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getOpname($id = false)
    {
        if ($id === false) {
            return $this->where('deleted_at', null)->findAll();
        }

        return $this->where(['id' => $id, 'deleted_at' => null])->first();
    }
}

class StockOpnameListModel extends Model
{
    protected $table = 'stock_opname_list';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_stock_opname', 'id_material', 'jumlah_awal', 'jumlah_akhir'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
    public function getOpnameItems($opnameId)
    {
        return $this->select('stock_opname_list.*, materials.name as material_name, materials.kode as material_code')
            ->join('materials', 'materials.id = stock_opname_list.id_material')
            ->where('stock_opname_list.id_stock_opname', $opnameId)
            ->where('stock_opname_list.deleted_at', null)
            ->findAll();
    }
}

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'kode', 'picture', 'supplier_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getActiveMaterials()
    {
        return $this->where('deleted_at', null)->findAll();
    }
}

class StockModel extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_material', 'stock_awal', 'stock_masuk', 'stock_keluar', 'price', 'id_currency'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getCurrentStock($materialId)
    {
        $result = $this->selectSum('stock_masuk', 'total_masuk')
            ->selectSum('stock_keluar', 'total_keluar')
            ->where('id_material', $materialId)
            ->where('deleted_at', null)
            ->first();

        $stockAwal = $this->select('stock_awal')
            ->where('id_material', $materialId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'asc')
            ->first();

        $currentStock = ($stockAwal['stock_awal'] ?? 0) + ($result['total_masuk'] ?? 0) - ($result['total_keluar'] ?? 0);

        return $currentStock;
    }
}