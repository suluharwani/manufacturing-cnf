<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalaryPattern extends Migration
{
    protected $DBGroup = 'tests';

   public function up()
    {
       $this->forge->addField([
        'id' => [
          'type' => 'INT',
          'constraint' => 10,
          'unsigned' => true,
          'auto_increment' => true,
        ],
         'pattern_code' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'employee_cat_id' => [
          'type' => 'INT',
          'constraint' => 10,
          'null' => true,
        ],
        'pattern_name' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'salary_det_id' => [
          'type' => 'INT',
          'constraint' => 11,
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
      $this->forge->createTable('salary_pattern');
    }
  
    public function down()
    {
      $this->forge->dropTable('salary_pattern');
    }
  }