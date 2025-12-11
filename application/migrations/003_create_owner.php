<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_owner extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists(TABLE_OWNER)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => FALSE],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE],
                'profile_picture' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('email');
            $this->dbforge->create_table(TABLE_OWNER, TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_OWNER)) {
            $this->dbforge->drop_table(TABLE_OWNER, TRUE);
        }
    }
}

