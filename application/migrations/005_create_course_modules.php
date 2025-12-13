<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_course_modules extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('course_modules')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'course_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'description' => ['type' => 'TEXT', 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('course_modules', TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists('course_modules')) {
            $this->dbforge->drop_table('course_modules', TRUE);
        }
    }
}







