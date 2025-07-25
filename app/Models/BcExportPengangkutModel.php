<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportPengangkutModel extends Model
{
    protected $table = 'bc_e_pengangkut';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_cara_angkut', 'nama_pengangkut',
        'nomor_pengangkut', 'kode_bendera'
    ];
    
    protected $useTimestamps = true;
    
    // Get transport data by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        return $this->where('nomor_aju', $nomorAju)->first();
    }
    
    // Get transport method name
    public function getTransportMethod($code)
    {
        $methods = [
            '1' => 'Kapal Laut',
            '2' => 'Pesawat Udara',
            '3' => 'Kereta Api',
            '4' => 'Truk'
        ];
        
        return $methods[$code] ?? 'Lainnya';
    }
}