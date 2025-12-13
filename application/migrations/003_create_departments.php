<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_departments extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists(TABLE_DEPARTMENT)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('name');
            $this->dbforge->create_table(TABLE_DEPARTMENT, TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_DEPARTMENT)) {
            $this->dbforge->drop_table(TABLE_DEPARTMENT, TRUE);
        }
    }
}







