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
            
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            
            'supplier_id' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'null'       => true,
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

        $this->forge->addKey('id', true);
        $this->forge->createTable('materials');
    }

    public function down()
    {
        $this->forge->dropTable('materials');
    }
}
