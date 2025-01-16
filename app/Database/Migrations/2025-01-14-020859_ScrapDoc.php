<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScrapDoc extends Migration
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
        'id_dept' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'id_wo' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'id_user' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'remarks' => [
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
    $this->forge->createTable('scrap_doc'); // Create the table
}

public function down()
{
    $this->forge->dropTable('scrap_doc'); // Drop the table if exists
}
}
