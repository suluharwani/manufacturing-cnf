<?php namespace App\Models;

use CodeIgniter\Model;

class BcKemasanModel extends Model
{
    protected $table = 'bc_i_kemasan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_kemasan', 'jumlah_kemasan', 'merek'
    ];
    
    protected $useTimestamps = true;
}