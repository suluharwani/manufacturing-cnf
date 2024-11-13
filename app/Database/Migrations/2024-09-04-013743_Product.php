<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Product extends Migration
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
        'id_product_cat' => [
          'type' => 'INT',
          'constraint' => 10,
          'null' => true,
        ],
        'nama' => [
          'type' => 'VARCHAR',
          'constraint' => 200,
          'null' => true,
        ],  
        'picture' => [
            'type' => 'VARCHAR',
            'constraint' => 2000,
            'null' => true,
          ],
          'text' => [
        'type' => 'VARCHAR',
        'constraint' => 50000,
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
      $this->forge->createTable('product');
    }
  
    public function down()
    {
      $this->forge->dropTable('product');
    }
  }