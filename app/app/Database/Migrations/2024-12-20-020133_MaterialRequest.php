<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MaterialRequest extends Migration
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
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'id_pi' => [
                'type'       => 'INT',
                'constraint' => '10',
                'null' => true,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => '10',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('material_request');
    }

    public function down()
    {
        $this->forge->dropTable('material_request');
    }
}
