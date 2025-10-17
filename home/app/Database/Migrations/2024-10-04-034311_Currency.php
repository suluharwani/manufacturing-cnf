<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Currency extends Migration
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
        'rate' => [
          'type' => 'FLOAT',
          'constraint' => 15,
          'null' => true,
        ], 
         'oldrate' => [
          'type' => 'FLOAT',
          'constraint' => 15,
          'null' => true,
        ], 
         'update' => [
          'type' => 'datetime',
          'null' => true,
        ], 
        'olddate' => [
          'type' => 'datetime',
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
      $this->forge->createTable('currency');
    }
  
    public function down()
    {
      $this->forge->dropTable('currency');
    }
  }