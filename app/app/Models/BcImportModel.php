<?php

namespace App\Models;

use CodeIgniter\Model;

class BcImportModel extends Model
{
    protected $table            = 'bc_i_header'; // sesuaikan dengan nama tabel
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_aju', 'tanggal_daftar', 'nomor_daftar', 'nomor_bc11', 
        'tanggal_bc11', 'kode_kantor', 'kode_jenis_impor', 'fob', 'kode_valuta',
        'nama_importir', 'alamat_importir', 'npwp_importir', 'kode_negara_importir',
        'kode_pelabuhan_muat', 'kode_pelabuhan_bongkar', 'kode_gudang',
        'kantor_pabean', 'asal_data', 'waktu_insert', 'waktu_update'
    ];

    protected $useTimestamps = false;
}