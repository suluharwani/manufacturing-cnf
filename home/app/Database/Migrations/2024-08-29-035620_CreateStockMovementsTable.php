<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockMovementsTable extends Migration
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
            'warehouse_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'stock_change' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'movement_date' => [
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
        $this->forge->createTable('stock_movements');
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements');
    }
}
