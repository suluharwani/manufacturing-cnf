<?php namespace App\Models;

use CodeIgniter\Model;

class BcHeaderModel extends Model
{
    protected $table = 'bc_i_header';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'kode_dokumen', 'kode_kantor', 'kode_kantor_bongkar', 
        'kode_kantor_periksa', 'kode_kantor_tujuan', 'kode_jenis_impor', 
        'kode_jenis_tpb', 'kode_jenis_prosedur', 'kode_cara_bayar', 
        'kode_pelabuhan_muat', 'kode_pelabuhan_tujuan', 'kode_tps', 
        'nomor_bc11', 'tanggal_bc11', 'nomor_pos', 'nomor_sub_pos', 
        'tanggal_tiba', 'nilai_barang', 'nilai_incoterm', 'asuransi', 
        'freight', 'fob', 'cif', 'ndpbm', 'bruto', 'netto', 'kode_valuta', 
        'kode_incoterm', 'kota_pernyataan', 'tanggal_pernyataan', 
        'nama_pernyataan', 'jabatan_pernyataan', 'nomor_daftar', 'tanggal_daftar'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}