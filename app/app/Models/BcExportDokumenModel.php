<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportDokumenModel extends Model
{
    protected $table = 'bc_e_dokumen';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_dokumen', 'nomor_dokumen', 'tanggal_dokumen'
    ];
    
    protected $useTimestamps = true;
    
    // Get documents by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri', 'ASC')
                   ->findAll();
    }
    
    // Get main document (usually seri = 1)
    public function getMainDocument($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri', 'ASC')
                   ->first();
    }
}