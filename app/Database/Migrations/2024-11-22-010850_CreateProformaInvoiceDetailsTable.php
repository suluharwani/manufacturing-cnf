<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProformaInvoiceDetailsTable extends Migration
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
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'item_description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'hs_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'unit_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'total_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
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
        $this->forge->createTable('proforma_invoice_details');
    }

    public function down()
    {
        $this->forge->dropTable('proforma_invoice_details');
    }
}
