<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCmsTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [  
                'type' => 'INT',  
                'unsigned' => true,  
                'auto_increment' => true,  
            ],
            'internal_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => ''
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => ''
            ],
            'start_date' => [
                'type' => 'DATETIME'
            ],
            'end_date' => [
                'type' => 'DATETIME'
            ],
            'branches' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'link_url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'prod_codes' => [
                'type' => 'TEXT',
                'default' => ''
            ],
            'dwell_time' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'sequence' => [
                'type' => 'INT'
            ],
            'ga_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'image_web' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'image_mobile' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'ribbon' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ],
            'data' => [
                'type' => 'TEXT',
                'default' => ''
            ],
            'editor' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => ''
            ],
            'created_at' => [
                'type' => 'DATETIME',  
                'default' => 'CURRENT_TIMESTAMP', 
            ],
            'updated_at' => [
                'type' => 'DATETIME',  
                'default' => 'CURRENT_TIMESTAMP', 
            ]
        ]);

        $this->forge->addPrimaryKey('id');  
        $this->forge->createTable('epos_cms');
    }

    public function down()
    {
        $this->forge->dropTable('epos_cms'); 
    }
}
