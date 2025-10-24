<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportPungutanModel extends Model
{
    protected $table = 'bc_e_pungutan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'kode_fasilitas_tarif', 'kode_jenis_pungutan', 
        'nilai_pungutan', 'npwp_billing'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get payments by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)->findAll();
    }
    
    // Get payment type name
    public function getPaymentType($code)
    {
        $types = [
            '100' => 'Bea Masuk',
            '200' => 'PPN',
            '300' => 'PPH',
            '400' => 'PPNBM'
        ];
        
        return $types[$code] ?? $code;
    }
}