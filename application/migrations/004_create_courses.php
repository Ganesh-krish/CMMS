<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_courses extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists(TABLE_COURCES)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'course_code' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => TRUE],
                'start_date' => ['type' => 'DATETIME', 'null' => TRUE],
                'end_date' => ['type' => 'DATETIME', 'null' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'tag' => ['type' => 'TEXT', 'null' => TRUE],
                'description' => ['type' => 'TEXT', 'null' => TRUE],
                'course_expiry' => ['type' => 'DATE', 'null' => TRUE],
                'department' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table(TABLE_COURCES, TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_COURCES)) {
            $this->dbforge->drop_table(TABLE_COURCES, TRUE);
        }
    }
}







