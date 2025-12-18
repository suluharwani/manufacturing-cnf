<?php namespace App\Models;

use CodeIgniter\Model;

class BcBarangDokumenModel extends Model
{
    protected $table = 'bc_i_barang_dokumen';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'seri_dokumen', 'seri_izin'
    ];
    
    protected $useTimestamps = true;
}