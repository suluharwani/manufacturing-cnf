<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'type_id' => [
                'id_type'       => 'INT',
                'constraint'     => 11,
                'null'       => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'supplier_id' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('materials');
    }

    public function down()
    {
        $this->forge->dropTable('materials');
    }
}
