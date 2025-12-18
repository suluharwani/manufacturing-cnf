<?php namespace App\Models;

use CodeIgniter\Model;

class BcBarangTarifModel extends Model
{
    protected $table = 'bc_i_barang_tarif';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'kode_pungutan', 'kode_tarif',
        'tarif', 'kode_fasilitas', 'tarif_fasilitas', 'nilai_bayar',
        'nilai_fasilitas', 'kode_satuan', 'jumlah_satuan'
    ];
    
    protected $useTimestamps = true;
}