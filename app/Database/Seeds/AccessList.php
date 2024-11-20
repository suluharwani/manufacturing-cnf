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
    // Satuan Panjang
    ['nama' => 'Meter', 'kode' => 'm', 'base_unit_id' => null, 'conversion_factor' => 1],
    ['nama' => 'Centimeter', 'kode' => 'cm', 'base_unit_id' => 1, 'conversion_factor' => 0.01],
    ['nama' => 'Millimeter', 'kode' => 'mm', 'base_unit_id' => 1, 'conversion_factor' => 0.001],
    ['nama' => 'Kilometer', 'kode' => 'km', 'base_unit_id' => 1, 'conversion_factor' => 1000],

    // Satuan Volume
    ['nama' => 'Liter', 'kode' => 'L', 'base_unit_id' => null, 'conversion_factor' => 1],
    ['nama' => 'Mililiter', 'kode' => 'mL', 'base_unit_id' => 5, 'conversion_factor' => 0.001],
    ['nama' => 'Cubic Meter', 'kode' => 'm³', 'base_unit_id' => 5, 'conversion_factor' => 1000],

    // Satuan Unit
    ['nama' => 'Piece', 'kode' => 'pcs', 'base_unit_id' => null, 'conversion_factor' => 1],
    ['nama' => 'Dozen', 'kode' => 'dz', 'base_unit_id' => 8, 'conversion_factor' => 12],

    // Satuan Luas
    ['nama' => 'Square Meter', 'kode' => 'm²', 'base_unit_id' => 1, 'conversion_factor' => 1], // 1 m² = 1 m * 1 m
    ['nama' => 'Square Centimeter', 'kode' => 'cm²', 'base_unit_id' => 2, 'conversion_factor' => 0.0001], // 1 cm² = 0.01 m * 0.01 m
    ['nama' => 'Square Millimeter', 'kode' => 'mm²', 'base_unit_id' => 3, 'conversion_factor' => 0.000001], // 1 mm² = 0.001 m * 0.001 m
    ['nama' => 'Square Kilometer', 'kode' => 'km²', 'base_unit_id' => 4, 'conversion_factor' => 1000000], // 1 km² = 1000 m * 1000 m
    ['nama' => 'Hectare', 'kode' => 'ha', 'base_unit_id' => 1, 'conversion_factor' => 10000], // 1 ha = 10,000 m²
    ['nama' => 'Are', 'kode' => 'a', 'base_unit_id' => 1, 'conversion_factor' => 100], // 1 are = 100 m²
];

        $this->db->table('satuan')->insertBatch($data);
       
    }
}
