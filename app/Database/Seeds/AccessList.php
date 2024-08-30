<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccessList extends Seeder
{
    public function run()
    {
        $this->db->query("INSERT INTO `access` (`id`, `page`) VALUES
      (1,'administrator'),
      (2,'client')
      ");
       
    }
}
