<?php namespace App\Models;

use CodeIgniter\Model;

class BcBarangVdModel extends Model
{
    protected $table = 'bc_i_barang_vd'; // Anda perlu membuat tabel ini
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'kode_vd', 'nilai_barang',
        'biaya_tambahan', 'biaya_pengurang', 'jatuh_tempo'
    ];
    
    protected $useTimestamps = true;
}