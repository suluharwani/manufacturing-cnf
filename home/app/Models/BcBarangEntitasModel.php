<?php namespace App\Models;

use CodeIgniter\Model;

class BcBarangEntitasModel extends Model
{
    protected $table = 'bc_i_barang_entitas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'seri_entitas'
    ];
    
    protected $useTimestamps = true;
}