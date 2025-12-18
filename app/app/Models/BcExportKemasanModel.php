<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportKemasanModel extends Model
{
    protected $table = 'bc_e_kemasan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_kemasan', 'jumlah_kemasan', 'merek'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get packaging by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri', 'ASC')
                   ->findAll();
    }
    
    // Get packaging type name
    public function getPackagingType($code)
    {
        $types = [
            'PK' => 'Kemasan Kayu',
            'PX' => 'Kemasan Karton',
            'DR' => 'Drum',
            'BG' => 'Karung',
            'CT' => 'Karton',
            'CR' => 'Krat'
        ];
        
        return $types[$code] ?? $code;
    }
}