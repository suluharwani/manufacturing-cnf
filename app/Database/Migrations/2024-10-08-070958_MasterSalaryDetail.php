<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterSalaryDetail extends Migration
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
        'id_master_salary' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
         'id_employee' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
        'id_salary_det' => [
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
      $this->forge->createTable('master_salary_detail');
    }
  
    public function down()
    {
      $this->forge->dropTable('master_salary_detail');
    }
  }