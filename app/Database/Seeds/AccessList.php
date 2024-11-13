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

                $data = [
            ['nama' => 'Meter', 'kode' => 'm', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['nama' => 'Centimeter', 'kode' => 'cm', 'base_unit_id' => 1, 'conversion_factor' => 0.01],
            ['nama' => 'Millimeter', 'kode' => 'mm', 'base_unit_id' => 1, 'conversion_factor' => 0.001],
            ['nama' => 'Kilometer', 'kode' => 'km', 'base_unit_id' => 1, 'conversion_factor' => 1000],
            ['nama' => 'Liter', 'kode' => 'L', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['nama' => 'Mililiter', 'kode' => 'mL', 'base_unit_id' => 5, 'conversion_factor' => 0.001],
            ['nama' => 'Cubic Meter', 'kode' => 'm³', 'base_unit_id' => 5, 'conversion_factor' => 1000],
            ['nama' => 'Piece', 'kode' => 'pcs', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['nama' => 'Dozen', 'kode' => 'dz', 'base_unit_id' => 8, 'conversion_factor' => 12],
        ];

        $this->db->table('satuan')->insertBatch($data);
       
    }
}
