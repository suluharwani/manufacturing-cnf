<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterPenggajianDetail extends Migration
{
   public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penggajian_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'karyawan_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('master_penggajian_detail');
    }

    public function down()
    {
        $this->forge->dropTable('master_penggajian_detail');
    }
}