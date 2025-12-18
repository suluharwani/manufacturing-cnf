<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StockOpnameList extends Migration
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
        'id_stock_opname' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'id_material' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'jumlah_awal' => [
            'type' => 'FLOAT',
            'constraint' => 10.2,
            'null' => true,
        ],
        'jumlah_akhir' => [
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
    $this->forge->createTable('stock_opname_list'); // Create the table
}

public function down()
{
    $this->forge->dropTable('stock_opname_list'); // Drop the table if exists
}
}
