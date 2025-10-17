<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;



class MaterialRequestList extends Migration
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
            'id_mr' => [
               'type'       => 'INT',
                'constraint' => '10',
                'null' => true,
            ],
            'id_pi' => [
                'type'       => 'INT',
                'constraint' => '10',
                'null' => true,
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
        $this->forge->createTable('material_request_list');
    }

    public function down()
    {
        $this->forge->dropTable('material_request_list');
    }
}
