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
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
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
            '4' => 'Flat Rack',
            '8' => 'Standard Container'
        ];
        
        return $types[$code] ?? 'Lainnya';
    }
    
    // Get container size
    public function getContainerSize($code)
    {
        $sizes = [
            '20' => '20 Feet',
            '40' => '40 Feet',
            '45' => '45 Feet'
        ];
        
        return $sizes[$code] ?? $code . ' Feet';
    }
}