<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PartisiProduct extends Migration
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
        'code' => [
            'type'       => 'VARCHAR',
            'constraint' => '50',
        ],
        'id_product' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'desc' => [
            'type' => 'VARCHAR',
            'constraint' => '255',
            'null' => true,
        ],
        'picture' => [
            'type' => 'VARCHAR',
            'constraint' => '255',
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
    $this->forge->createTable('modul'); // Create the table
}

public function down()
{
    $this->forge->dropTable('modul'); // Drop the table if exists
}
}
