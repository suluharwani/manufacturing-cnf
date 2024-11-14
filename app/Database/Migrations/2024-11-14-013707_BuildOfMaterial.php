<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuildOfMaterial extends Migration
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
      'id_product' => [
        'type' => 'INT',
        'constraint' => 10,
        'null' => true,
      ],
      'id_material' => [
        'type' => 'INT',
        'constraint' => 10,
        'null' => true,
      ],
      'penggunaan' => [
        'type' => 'FLOAT',
        'constraint' => 10.2,
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
    $this->forge->createTable('buildofmaterial');
  }

  public function down()
  {
    $this->forge->dropTable('buildofmaterial');
  }
}