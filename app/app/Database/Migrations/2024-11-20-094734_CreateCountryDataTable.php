<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCountryDataTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_country' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'country_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'code1' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'code2' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'flag' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_country', true); // Primary Key
        $this->forge->createTable('country_data');
    }

    public function down()
    {
        $this->forge->dropTable('country_data');
    }
}
