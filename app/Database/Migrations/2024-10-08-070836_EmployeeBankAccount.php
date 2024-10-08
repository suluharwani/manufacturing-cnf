<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EmployeeBankAccount extends Migration
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
        'employee_name' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'bank' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
          'null' => true,
        ],
        'account_number' => [
          'type' => 'VARCHAR',
          'constraint' => 50,
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
      $this->forge->createTable('bank_account');
    }
  
    public function down()
    {
      $this->forge->dropTable('bank_account');
    }
  }