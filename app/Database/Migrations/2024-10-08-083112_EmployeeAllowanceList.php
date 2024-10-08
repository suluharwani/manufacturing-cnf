<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EmployeeAllowanceList extends Migration
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
        'employee_id' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => true,
        ],
        'allowance_id' => [
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
      $this->forge->createTable('employee_allowance_list');
    }
  
    public function down()
    {
      $this->forge->dropTable('employee_allowance_list');
    }
  }