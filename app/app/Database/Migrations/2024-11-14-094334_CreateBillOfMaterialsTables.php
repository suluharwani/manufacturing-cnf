<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillOfMaterialsTables extends Migration
{
    public function up()
    {
        // Table for product details
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'issue_date'        => ['type' => 'DATE', 'null' => true],
            'dept'              => ['type' => 'VARCHAR', 'constraint' => 50],
            'collection_code'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'description'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'customer'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'markup_material'   => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'length_mm'         => ['type' => 'INT'],
            'height_mm'         => ['type' => 'INT'],
            'width_mm'          => ['type' => 'INT'],
            'nw_kg'             => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'gw_kg'             => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'cbm'               => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('product_details');

        // Table for raw materials
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'product_id'            => ['type' => 'INT'],
            'material_type'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'material'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'module'                => ['type' => 'VARCHAR', 'constraint' => 100],
            'component'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'dimensions'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'quantity'              => ['type' => 'INT'],
            'consumption_actual'    => ['type' => 'DECIMAL', 'constraint' => '10,4'],
            'waste'                 => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'total_consumption'     => ['type' => 'DECIMAL', 'constraint' => '10,4'],
            'unit'                  => ['type' => 'VARCHAR', 'constraint' => 10],
            'unit_cost'             => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'total_cost_idr'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'cost_usd'              => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'product_details', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('raw_materials');

        // Table for fitting and components
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'product_id'            => ['type' => 'INT'],
            'material'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'length_mm'             => ['type' => 'INT'],
            'width_mm'              => ['type' => 'INT'],
            'thickness'             => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'quantity'              => ['type' => 'INT'],
            'consumption'           => ['type' => 'DECIMAL', 'constraint' => '10,4'],
            'waste'                 => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'total_consumption'     => ['type' => 'DECIMAL', 'constraint' => '10,4'],
            'unit'                  => ['type' => 'VARCHAR', 'constraint' => 10],
            'unit_cost'             => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'total_cost_idr'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'cost_usd'              => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'product_details', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fitting_components');

        // Table for labor costs
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'product_id'            => ['type' => 'INT'],
            'material'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'process'               => ['type' => 'VARCHAR', 'constraint' => 255],
            'time_minutes'          => ['type' => 'INT'],
            'wage_per_minute'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'total_cost_idr'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'cost_usd'              => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'product_details', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('labor_costs');

        // Table for machine cost
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'product_id'            => ['type' => 'INT'],
            'machine'               => ['type' => 'VARCHAR', 'constraint' => 100],
            'hours'                 => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'rate_per_hour'         => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'total_cost_idr'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'cost_usd'              => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'product_details', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('machine_costs');

        // Table for summary costs
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'product_id'            => ['type' => 'INT'],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'total_cost_idr'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'cost_usd'              => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'product_details', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('summary_costs');
    }

    public function down()
    {
        $this->forge->dropTable('summary_costs');
        $this->forge->dropTable('machine_costs');
        $this->forge->dropTable('labor_costs');
        $this->forge->dropTable('fitting_components');
        $this->forge->dropTable('raw_materials');
        $this->forge->dropTable('product_details');
    }
}
