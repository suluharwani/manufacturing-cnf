<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlProductionProgress extends Model
{
    protected $table            = 'production_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','warehouse_id','production_id','wo_id','product_id','quantity','created_at','deleted_at','updated_at'];

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

      public function transferQuantity($prodIdAwal, $prodIdTujuan, $quantity)
    {

        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        // Cek apakah data dengan production_id awal dan produk sudah ada
        $existingData = $builder->where('id', $prodIdAwal)
                                ->get()
                                ->getRowArray();
        // var_dump($existingData['wo_id']);
        // die();                      
        // Jika data ditemukan, update quantity
        if ($existingData) {
            // Update quantity di data awal
            $updatedQuantity = $existingData['quantity'] - $quantity;
            if ($updatedQuantity < 0) {
                return false;  // Tidak cukup quantity untuk dipindahkan
            }

            $builder->where('id', $prodIdAwal);
            $builder->update(['quantity' => $updatedQuantity, 'updated_at' => date('Y-m-d H:i:s')]);

            // Cek apakah data untuk production_id tujuan sudah ada
            $existingDataTujuan = $builder->where(array('wo_id' => $existingData['wo_id'],'product_id'=>$existingData['product_id'],'production_id'=>$prodIdTujuan))->get()->getRowArray();

            // Jika data untuk tujuan ada, update quantity
            if ($existingDataTujuan) {
                $updatedQuantityTujuan = $existingDataTujuan['quantity'] + $quantity;
                $builder->where(array('wo_id' => $existingData['wo_id'],'product_id'=>$existingData['product_id'],'production_id'=>$prodIdTujuan));
                $builder->update(['quantity' => $updatedQuantityTujuan, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                // Jika data untuk tujuan tidak ada, insert data baru
                $builder->insert([
                    'production_id' => $prodIdTujuan,
                    'quantity'      => $quantity,
                    'product_id'      => $existingData['product_id'],
                    'wo_id'      => $existingData['wo_id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }

            return true;  // Berhasil memindahkan produk
        }

        return false;  // Data tidak ditemukan
    }
          public function transferFinish($prodIdAwal, $prodIdTujuan, $quantity)
    {

        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        // Cek apakah data dengan production_id awal dan produk sudah ada
        $existingData = $builder->where('id', $prodIdAwal)
                                ->get()
                                ->getRowArray();
        // var_dump($existingData['wo_id']);
        // die();                      
        // Jika data ditemukan, update quantity
        if ($existingData) {
            // Update quantity di data awal
            $updatedQuantity = $existingData['quantity'] - $quantity;
            if ($updatedQuantity < 0) {
                return false;  // Tidak cukup quantity untuk dipindahkan
            }

            $builder->where('id', $prodIdAwal);
            $builder->update(['quantity' => $updatedQuantity, 'updated_at' => date('Y-m-d H:i:s')]);

            // Cek apakah data untuk production_id tujuan sudah ada
            $existingDataTujuan = $builder->where(array('wo_id' => $existingData['wo_id'],'product_id'=>$existingData['product_id'],'warehouse_id'=>$prodIdTujuan))->get()->getRowArray();

            // Jika data untuk tujuan ada, update quantity
            if ($existingDataTujuan) {
                $updatedQuantityTujuan = $existingDataTujuan['quantity'] + $quantity;
                $builder->where(array('wo_id' => $existingData['wo_id'],'product_id'=>$existingData['product_id'],'warehouse_id'=>$prodIdTujuan));
                $builder->update(['quantity' => $updatedQuantityTujuan, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                // Jika data untuk tujuan tidak ada, insert data baru
                $builder->insert([
                    'warehouse_id' => $prodIdTujuan,
                    'quantity'      => $quantity,
                    'product_id'      => $existingData['product_id'],
                    'wo_id'      => $existingData['wo_id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }

            return true;  // Berhasil memindahkan produk
        }

        return false;  // Data tidak ditemukan
    }
    public function finish($id)
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Soft delete all entries in production_progress where work_order.invoice_id matches the provided id
            $sqlDelete = "
                UPDATE production_progress pp
                JOIN work_order wo ON pp.wo_id = wo.id
                SET pp.deleted_at = NOW()
                WHERE wo.invoice_id = ? AND pp.deleted_at IS NULL
            ";
            $db->query($sqlDelete, [$id]);

            // Update the status field in proforma_invoice to 1 for the record with the given id
            $sqlUpdate = "
                UPDATE proforma_invoice
                SET status = 1
                WHERE id = ?
            ";
            $db->query($sqlUpdate, [$id]);

            // Commit the transaction
            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            $db->transRollback();
            return false;
        }
    }
    public function batalFinish($id)
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Reset the deleted_at column in production_progress where work_order.invoice_id matches the provided id
            $sqlReset = "
                UPDATE production_progress pp
                JOIN work_order wo ON pp.wo_id = wo.id
                SET pp.deleted_at = NULL
                WHERE wo.invoice_id = ? AND pp.deleted_at IS NOT NULL
            ";
            $db->query($sqlReset, [$id]);

            // Reset the status field in proforma_invoice to NULL for the record with the given id
            $sqlUpdateStatus = "
                UPDATE proforma_invoice
                SET status = NULL
                WHERE id = ?
            ";
            $db->query($sqlUpdateStatus, [$id]);

            // Commit the transaction
            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            $db->transRollback();
            return false;
        }
    }
}
