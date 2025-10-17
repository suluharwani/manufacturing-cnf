<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportModel extends Model
{
    protected $table = 'bc_e_header';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'kode_dokumen', 'kode_kantor', 'kode_jenis_ekspor',
        'kode_jenis_tpb', 'kode_jenis_prosedur', 'kode_pelabuhan_muat',
        'kode_pelabuhan_tujuan', 'nomor_bc11', 'tanggal_bc11', 'tanggal_ekspor',
        'nilai_barang', 'nilai_incoterm', 'fob', 'kode_valuta', 'kode_incoterm',
        'bruto', 'netto', 'kota_pernyataan', 'tanggal_pernyataan', 'nama_pernyataan'
    ];
    
    protected $useTimestamps = true;
}