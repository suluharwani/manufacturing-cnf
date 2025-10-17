<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Changelog extends Migration
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
        'nama_admin' => [
          'type' => 'VARCHAR',
          'constraint' => '200',
          'null' => true
        ],
        'id_google' => [
          'type' => 'VARCHAR',
          'constraint' => '500',
          'null' => true
        ],
        'ip' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'riwayat' => [
          'type' => 'VARCHAR',
          'constraint' => 1000,
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
      $this->forge->createTable('changelog');
    }

    public function down()
    {
        $this->forge->dropTable('changelog');
    }
}
