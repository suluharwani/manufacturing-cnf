<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialDetTable extends Migration
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
            'material_id' => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'type_id' => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'satuan_id' => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'kite' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'dimension' => [
                'type'       => 'VARCHAR',
                'constraint'     => 11,
                'null'       => true,
            ],
            'grade' => [
                'type'       => 'VARCHAR',
                'constraint'     => 11,
                'null'       => true,
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint'     => 11,
                'null'       => true,
            ],
            'texture' => [
                'type'       => 'VARCHAR',
                'constraint'     => 11,
                'null'       => true,
            ],
            'source' => [
                'type'       => 'VARCHAR',
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
            'deleted_at' => [
          'type' => 'datetime',
          'null' => true,
        ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('materials_detail');
    }

    public function down()
    {
        $this->forge->dropTable('materials_detail');
    }
}
