<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseOrder extends Migration
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
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'supplier_id' => [
                'type'       => 'INT',
                'constraint' => '10',
            ],
            'vat' => [
                'type'       => 'float',
                'constraint' => '10.2',
            ],
            'arrival_target' => [
                'type' => 'DATE',
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
        $this->forge->createTable('purchase_order');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order');
    }
}
