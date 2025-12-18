<?php namespace App\Models;

use CodeIgniter\Model;

class BcDokumenModel extends Model
{
    protected $table = 'bc_i_dokumen';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri', 'kode_dokumen', 'nomor_dokumen',
        'tanggal_dokumen', 'kode_fasilitas', 'kode_ijin'
    ];
    
    protected $useTimestamps = true;
}