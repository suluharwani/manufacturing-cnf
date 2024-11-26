<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PembelianDetail extends Migration
{
    public function up()
    {
       $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pembelian' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'id_material' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'jumlah' => [
              'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
                    ],
            'harga' => [
               'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
              ],
            'status_pembayaran' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Set primary key
        $this->forge->createTable('pembelian_detail'); // Create the table
    }

    public function down()
    {
        $this->forge->dropTable('pembelian_detail'); // Drop the table if exists
    }
}
