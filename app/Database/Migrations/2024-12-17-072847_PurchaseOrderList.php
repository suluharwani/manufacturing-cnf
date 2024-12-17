<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseOrderList extends Migration
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
            'id_material' => [
                'type'       => 'INT',
                'constraint' => '10',
            ],
            'quantity' => [
                'type'       => 'float',
                'constraint' => '10.2',
            ],
            'price' => [
                'type'       => 'float',
                'constraint' => '10.2',
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
        $this->forge->createTable('purchase_order_list');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order_list');
    }
}
