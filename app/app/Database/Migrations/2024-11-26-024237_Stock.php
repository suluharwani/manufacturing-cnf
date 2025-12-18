<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stock extends Migration
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
            'id_material' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'stock_awal' => [
                'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
              ],
            'stock_masuk' => [
                'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
              ],
            'stock_keluar' => [
               'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
              ],
            'price' => [
               'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
              ],
            'id_currency' => [
               'type' => 'INT',
                'constraint' => 10,
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
        $this->forge->createTable('stock'); // Create the table
    }

    public function down()
    {
        $this->forge->dropTable('stock'); // Drop the table if exists
    }
}
