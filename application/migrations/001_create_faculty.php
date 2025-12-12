<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_faculty extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists(TABLE_FACULTY)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE, 'unique' => TRUE],
                'phone_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'role' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => FALSE],
                'department' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
                'joining_date' => ['type' => 'DATE', 'null' => FALSE],
                'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table(TABLE_FACULTY, TRUE);

            // Seed super admin
            $email = 'admin@cmms.in';
            $exists = $this->db->get_where(TABLE_FACULTY, ['email' => $email])->row_array();
            if (!$exists) {
                $this->db->insert(TABLE_FACULTY, [
                    'name' => 'Super Admin',
                    'email' => $email,
                    'phone_number' => '0000000000',
                    'password' => password_hash('Admin@123', PASSWORD_BCRYPT),
                    'role' => ROLE_SUPERADMIN,
                    'department' => null,
                    'joining_date' => date('Y-m-d'),
                    'file_path' => null,
                    'is_active' => 1,
                    'created_by' => 0,
                    'updated_by' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_FACULTY)) {
            $this->dbforge->drop_table(TABLE_FACULTY, TRUE);
        }
    }
}

