<?php namespace App\Models;

use CodeIgniter\Model;

class BcPengangkutModel extends Model
{
    protected $table = 'bc_i_pengangkut';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_cara_angkut', 'nama_pengangkut',
        'nomor_pengangkut', 'kode_bendera', 'call_sign'
    ];
    
    protected $useTimestamps = true;
}