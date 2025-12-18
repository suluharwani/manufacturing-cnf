<?php namespace App\Models;

use CodeIgniter\Model;

class BcBarangModel extends Model
{
    protected $table = 'bc_i_barang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'hs', 'kode_barang', 'uraian',
        'merek', 'tipe', 'ukuran', 'spesifikasi_lain', 'kode_satuan',
        'jumlah_satuan', 'kode_kemasan', 'jumlah_kemasan', 'netto',
        'bruto', 'cif', 'cif_rupiah', 'ndpbm', 'fob', 'asuransi',
        'freight', 'kode_negara_asal', 'kode_jenis_nilai', 'kode_kondisi_barang'
    ];
    
    protected $useTimestamps = true;
}