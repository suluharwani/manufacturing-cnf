<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouses';           // Nama tabel di database
    protected $primaryKey = 'id';              // Primary key tabel
    protected $allowedFields = ['name', 'location', 'capacity', 'current_stock']; // Field yang dapat diisi melalui metode insert/update

    // Method untuk memperbarui stok barang di gudang
    public function updateStock($id, $stock)
    {
        $warehouse = $this->find($id);

        if ($warehouse) {
            $newStock = $warehouse['current_stock'] + $stock;
            $this->update($id, ['current_stock' => $newStock]);
            return true;
        }

        return false;
    }

    // Method untuk mengurangi stok barang di gudang
    public function removeStock($id, $stock)
    {
        $warehouse = $this->find($id);

        if ($warehouse) {
            $newStock = $warehouse['current_stock'] - $stock;

            // Pastikan stok tidak kurang dari 0
            if ($newStock >= 0) {
                $this->update($id, ['current_stock' => $newStock]);
                return true;
            }
        }

        return false;
    }

    // Method untuk melacak pergerakan stok antar gudang (misalnya)
    public function getStockMovements()
    {
        // Contoh sederhana, Anda bisa menyesuaikan dengan struktur tabel yang Anda gunakan untuk pergerakan stok
        return $this->db->table('stock_movements')
                        ->select('stock_movements.*, warehouses.name as warehouse_name')
                        ->join('warehouses', 'warehouses.id = stock_movements.warehouse_id')
                        ->get()
                        ->getResultArray();
    }

    // Method untuk menghasilkan laporan gudang (misalnya laporan stok)
    public function getWarehouseReport()
    {
        // Contoh sederhana, menampilkan semua gudang dan stok saat ini
        return $this->select('name, location, capacity, current_stock')
                    ->findAll();
    }
}
