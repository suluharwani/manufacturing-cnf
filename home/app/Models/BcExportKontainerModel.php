<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportKontainerModel extends Model
{
    protected $table = 'bc_e_kontainer';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'nomor_kontainer', 'kode_ukuran_kontainer',
        'kode_jenis_kontainer', 'kode_tipe_kontainer'
    ];
    
    protected $useTimestamps = true;
    
    // Get containers by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri', 'ASC')
                   ->findAll();
    }
    
    // Get container type name
    public function getContainerType($code)
    {
        $types = [
            '1' => 'Dry Container',
            '2' => 'Reefer Container',
            '3' => 'Open Top',
            '4' => 'Flat Rack'
        ];
        
        return $types[$code] ?? 'Lainnya';
    }
}