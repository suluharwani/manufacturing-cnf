<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionTable extends Migration
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
            'design_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'material_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity_required' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'production_date' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pending',
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
        $this->forge->createTable('production');
    }

    public function down()
    {
        $this->forge->dropTable('production');
    }
}
