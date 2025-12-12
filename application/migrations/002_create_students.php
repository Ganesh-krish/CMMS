<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_students extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists(TABLE_STUDENT)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'registration_number' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE, 'unique' => TRUE],
                'phone_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'department' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
                'joining_date' => ['type' => 'DATE', 'null' => FALSE],
                'batch' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
                'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table(TABLE_STUDENT, TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_STUDENT)) {
            $this->dbforge->drop_table(TABLE_STUDENT, TRUE);
        }
    }
}

