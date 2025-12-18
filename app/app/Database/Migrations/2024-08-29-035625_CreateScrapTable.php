<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScrapTable extends Migration
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
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'reason' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'scrap_date' => [
                'type' => 'DATETIME',
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
        $this->forge->createTable('scrap');
    }

    public function down()
    {
        $this->forge->dropTable('scrap');
    }
}
