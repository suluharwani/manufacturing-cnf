<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportEntitasModel extends Model
{
    protected $table = 'bc_e_entitas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_entitas', 'kode_jenis_identitas',
        'nomor_identitas', 'nama_entitas', 'alamat_entitas', 'kode_negara'
    ];
    
    protected $useTimestamps = true;
    
    // Get entities by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri', 'ASC')
                   ->findAll();
    }
    
    // Get exporter data (kode_entitas = 1)
    public function getExporter($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->where('kode_entitas', 1)
                   ->first();
    }
}