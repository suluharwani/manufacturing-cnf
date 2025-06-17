<?php namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    // Tidak perlu definisi table karena kita menggunakan builder langsung
    protected $DBGroup = 'default';
    
    public function getBuilder($table)
    {
        return $this->db->table($table);
    }
}