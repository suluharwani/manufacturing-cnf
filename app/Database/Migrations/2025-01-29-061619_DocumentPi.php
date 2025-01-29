<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocumentPi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'document' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('documentpi');
    }

    public function down()
    {
        $this->forge->dropTable('documentpi');
    }
}
