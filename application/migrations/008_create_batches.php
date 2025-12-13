<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_batches extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists(TABLE_BATCHES)) {
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => FALSE
                ),
                'year' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '10',
                    'null' => TRUE
                ),
                'college_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ),
                'is_active' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => FALSE
                ),
                'created_by' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                    'default' => 'CURRENT_TIMESTAMP'
                ),
                'updated_by' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ),
                'updated_at' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                    'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                )
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table(TABLE_BATCHES, TRUE);
        }
    }

    public function down()
    {
        if ($this->db->table_exists(TABLE_BATCHES)) {
            $this->dbforge->drop_table(TABLE_BATCHES, TRUE);
        }
    }
}