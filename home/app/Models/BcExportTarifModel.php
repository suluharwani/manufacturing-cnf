<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportTarifModel extends Model
{
    protected $table = 'bc_e_tarif';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'kode_pungutan', 'kode_tarif', 'tarif',
        'kode_fasilitas', 'tarif_fasilitas', 'nilai_bayar', 'nilai_fasilitas',
        'kode_satuan', 'jumlah_satuan'
    ];
    
    protected $useTimestamps = true;
    
    // Get tariffs by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri_barang', 'ASC')
                   ->findAll();
    }
    
    // Get payment type name
    public function getPaymentType($code)
    {
        $types = [
            'PPN' => 'PPN Ekspor',
            'PPH' => 'PPh Ekspor',
            'BM' => 'Bea Masuk'
        ];
        
        return $types[$code] ?? 'Lainnya';
    }
}