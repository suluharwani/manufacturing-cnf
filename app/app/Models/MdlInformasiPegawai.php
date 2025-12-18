<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlInformasiPegawai extends Model
{
    protected $DBGroup          = 'tests';
    protected $table = 'informasi_pegawai';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'pin', 'id_pegawai', 'bank', 'bank_account', 'masuk_kerja', 'keluar_kerja', 'nik', 'foto', 'tgl_lahir', 'jumlah_tanggungan', 'status', 'no_bpjs', 'no_bpjstk', 'pemilik_rekening', 'alamat', 'posisi'];
    protected $useTimestamps = false;
}


