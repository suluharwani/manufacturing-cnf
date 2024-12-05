<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductionArea extends Migration
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
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('production_area');
    }

    public function down()
    {
        $this->forge->dropTable('production_area');
    }
}
