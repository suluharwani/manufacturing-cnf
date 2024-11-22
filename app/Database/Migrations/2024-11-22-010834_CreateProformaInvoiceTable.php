<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProformaInvoiceTable extends Migration
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
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'invoice_date' => [
                'type' => 'DATE',
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'customer_address' => [
                'type' => 'TEXT',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'payment_terms' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
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
        $this->forge->createTable('proforma_invoice');
    }

    public function down()
    {
        $this->forge->dropTable('proforma_invoice');
    }
}
