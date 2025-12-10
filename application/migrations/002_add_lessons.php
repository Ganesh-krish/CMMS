<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_lessons extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('lessons')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'module_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'body_text' => ['type' => 'TEXT', 'null' => TRUE],
                'video_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
                'attachment_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
                'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('module_id');
            $this->dbforge->create_table('lessons', TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists('lessons')) {
            $this->dbforge->drop_table('lessons', TRUE);
        }
    }
}

