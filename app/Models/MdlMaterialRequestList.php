<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlMaterialRequestList extends Model
{
    protected $table            = 'material_request_list';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','id_mr',
    'id_mr',
    'id_material',
    'id_dept',
    'id_pi',
    'quantity',
    'remarks','created_at', 'updated_at', 'deleted_at'];

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

    public function importMaterialUsage($id_mr, $id_pi, $idDept)
    {
        $builder = $this->db->table($this->table);
    
        // Query untuk mengambil data material_id dan total_penggunaan
        $query = $this->db->query("
            SELECT 
                m.id AS material_id,
                ROUND(SUM(COALESCE(bom.penggunaan, 0)) * pid.quantity, 2) AS total_penggunaan
            FROM 
                proforma_invoice_details pid
            LEFT JOIN 
                billofmaterial bom ON pid.id_product = bom.id_product
            JOIN 
                materials m ON bom.id_material = m.id
            JOIN 
                product p ON pid.id_product = p.id
            JOIN 
                modul ON bom.id_modul = modul.id
            WHERE 
                pid.invoice_id = ?
            GROUP BY 
                m.id
            ORDER BY
                modul.id, p.id
        ", [$id_pi]);
    
        $results = $query->getResultArray();
    
        // Simpan data ke dalam tabel material_request_list
        foreach ($results as $row) {
            $data = [
                'id_mr'        => $id_mr,
                'id_pi'        => $id_pi,
                'id_dept'      => $idDept,
                'id_material'  => $row['material_id'],
                'quantity'    => $row['total_penggunaan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
    
            // Cek apakah data sudah ada
            $existingData = $builder
                ->where('id_mr', $id_mr)
                ->where('id_material', $row['material_id'])
                ->get()
                ->getRowArray();
    
            if ($existingData) {
                // Jika data sudah ada, update quantity
                $newQuantity = $existingData['quantity'] + $row['total_penggunaan'];
                $builder
                    ->where('id_mr', $id_mr)
                    ->where('id_material', $row['material_id'])
                    ->update(['quantity' => $newQuantity, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                // Jika data belum ada, insert baru
                $builder->insert($data);
            }
        }
    
        return "Data berhasil diimpor.";
    }
}
