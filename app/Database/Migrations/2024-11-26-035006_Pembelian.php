<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pembelian extends Migration
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
            'id_po' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'id_supplier' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'invoice' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'tanggal_nota' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_jatuh_tempo' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status_pembayaran' => [
                'type' => 'INT',
                'null' => true,
            ],
            'pajak' => [
               'type' => 'FLOAT',
                'constraint' => 10.2,
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
        $this->forge->createTable('pembelian'); // Create the table
    }

    public function down()
    {
        $this->forge->dropTable('pembelian'); // Drop the table if exists
    }
}
