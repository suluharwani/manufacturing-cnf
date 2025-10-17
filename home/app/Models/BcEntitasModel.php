<?php namespace App\Models;

use CodeIgniter\Model;

class BcEntitasModel extends Model
{
    protected $table = 'bc_i_entitas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_entitas', 'kode_jenis_identitas',
        'nomor_identitas', 'nama_entitas', 'alamat_entitas', 'nib_entitas',
        'kode_jenis_api', 'kode_status', 'kode_negara'
    ];
    
    protected $useTimestamps = true;
}