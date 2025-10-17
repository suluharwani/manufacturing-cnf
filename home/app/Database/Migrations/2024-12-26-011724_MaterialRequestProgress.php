<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MaterialRequestProgress extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_material_requisition_list' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'id_material' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'id_currency' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'jumlah' => [
                'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
            ],
            'harga' => [
                'type' => 'FLOAT',
                'constraint' => 10.2,
                'null' => true,
            ],

            'created_at datetime default current_timestamp',
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Set primary key
        $this->forge->createTable('material_requisition_progress'); // Create the table
    }

    public function down()
    {
        $this->forge->dropTable('material_requisition_progress'); // Drop the table if exists
    }
}
