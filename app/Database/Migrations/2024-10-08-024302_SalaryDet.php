<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalaryDet extends Migration
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
        'day' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'payperhour' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
         'basic_salary' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
          'work_start' => [
          'type' => 'time',
          'null' => true,
        ],  'work_end' => [
          'type' => 'work_end',
          'null' => true,
        ],  'overtime_start' => [
          'type' => 'time',
          'null' => true,
        ],
         'overtime_start' => [
          'type' => 'time',
          'null' => true,
        ],
         'overtime_end' => [
          'type' => 'time',
          'null' => true,
        ],
        'work_break' => [
          'type' => 'time',
          'null' => true,
        ],
        'overtime_break' => [
          'type' => 'time',
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
      $this->forge->createTable('salary_det');
    }
  
    public function down()
    {
      $this->forge->dropTable('salary_det');
    }
  }