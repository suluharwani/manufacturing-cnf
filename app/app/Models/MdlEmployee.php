<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlEmployee extends Model
{
    protected $DBGroup          = 'tests';
    protected $table            = 'pegawai';
    protected $primaryKey       = 'pegawai_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [ 'pegawai_id',  'pegawai_pin',  'pegawai_nip',  'pegawai_nama',  'pegawai_alias',  'pegawai_pwd',  'pegawai_rfid',  'pegawai_privilege',  'pegawai_telp',  'pegawai_status',  'tempat_lahir',  'tgl_lahir',  'pembagian1_id',  'pembagian2_id',  'pembagian3_id',  'tgl_mulai_kerja',  'tgl_resign',  'gender',  'tgl_masuk_pertama',  'photo_path', 'tmp_img' , 'nama_bank',  'nama_rek',  'no_rek',  'password_fio_desktop',  'status_login_fio_desktop',  'new_pegawai_id' ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    public function getDet($karyawan_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->join('informasi_pegawai', 'pegawai.pegawai_pin = informasi_pegawai.pin', 'left');
        $builder->where('pegawai.pegawai_id', $karyawan_id);
        return $builder->get()->getRowArray();
    }
}
