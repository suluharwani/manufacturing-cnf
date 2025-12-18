<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlAccess extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'access';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','id_admin','page','access','updated_at','deleted_at','created_at'];

    // Dates
    protected $useTimestamps = true;
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

    function getAccess($id,$page){
       $db = \Config\Database::connect();
       $builder = $db->table('access');
       
        if ($data[] = $builder->where(array('id_admin'=> $id, 'page' => $page))->get()->getResultArray()) {
            if ($data[0][0]['access']==1) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
