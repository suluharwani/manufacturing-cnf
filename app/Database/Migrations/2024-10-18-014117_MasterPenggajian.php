<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterPenggajian extends Migration
{
        public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_penggajian' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'tanggal_awal_penggajian' => [
                'type' => 'DATETIME',
            ],
            'group' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'creator' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'tanggal_akhir_enggajian' => [
                'type' => 'DATETIME',
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
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
        $this->forge->createTable('master_penggajian');
    }

    public function down()
    {
        $this->forge->dropTable('master_penggajian');
    }
}