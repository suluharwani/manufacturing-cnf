<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MaterialSatuan extends Migration
{
    public function up()
    {
      $this->forge->addField([
        'id' => [
          'type' => 'INT',
          'constraint' => 10,
          'unsigned' => true,
          'auto_increment' => true,
        ],
        'kode' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'nama' => [
          'type' => 'VARCHAR',
          'constraint' => 200,
          'null' => true,
        ],
        'base_unit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'conversion_factor' => [
                'type' => 'FLOAT',
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
      $this->forge->createTable('satuan');
    }
  
    public function down()
    {
      $this->forge->dropTable('satuan');
    }
  }