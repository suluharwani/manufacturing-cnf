<?php namespace App\Models;

use CodeIgniter\Model;

class BcPungutanModel extends Model
{
    protected $table = 'bc_i_pungutan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'kode_fasilitas_tarif', 'kode_jenis_pungutan',
        'nilai_pungutan', 'npwp_billing'
    ];
    
    protected $useTimestamps = true;
}