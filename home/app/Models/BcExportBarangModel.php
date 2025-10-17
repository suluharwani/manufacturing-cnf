<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportBarangModel extends Model
{
    protected $table = 'bc_e_barang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'hs', 'uraian', 'kode_satuan',
        'jumlah_satuan', 'netto', 'fob', 'kode_negara_tujuan'
    ];
    
    protected $useTimestamps = true;
    
    // Get goods by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri_barang', 'ASC')
                   ->findAll();
    }
    
    // Format FOB value with thousand separators
    public function formatFob($value)
    {
        return number_format($value, 0, ',', '.');
    }
    
    // Get total FOB for a shipment
    public function getTotalFob($nomorAju)
    {
        return $this->selectSum('fob')
                   ->where('nomor_aju', $nomorAju)
                   ->get()
                   ->getRow()->fob;
    }
}