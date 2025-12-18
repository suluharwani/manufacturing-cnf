<?php namespace App\Models;

use CodeIgniter\Model;

class BcKontainerModel extends Model
{
    protected $table = 'bc_i_kontainer';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'nomor_kontainer', 'kode_ukuran_kontainer',
        'kode_jenis_kontainer', 'kode_tipe_kontainer'
    ];
    
    protected $useTimestamps = true;
}