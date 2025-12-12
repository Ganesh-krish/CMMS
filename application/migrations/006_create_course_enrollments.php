<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_course_enrollments extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('course_enrollments')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'course_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'student_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'status' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('course_enrollments', TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists('course_enrollments')) {
            $this->dbforge->drop_table('course_enrollments', TRUE);
        }
    }
}

