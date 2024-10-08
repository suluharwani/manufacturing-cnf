<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalaryPatternEmployee extends Migration
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
        'id_salary_pattern' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
         'id_employee' => [
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
      $this->forge->createTable('salary_pattern_employee');
    }
  
    public function down()
    {
      $this->forge->dropTable('salary_pattern_employee');
    }
  }