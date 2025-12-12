<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_colleges extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        // Drop legacy site_url column if it exists (for existing installs)
        if ($this->db->table_exists(TABLE_COLLEGE) && $this->db->field_exists('site_url', TABLE_COLLEGE)) {
            $this->dbforge->drop_column(TABLE_COLLEGE, 'site_url');
        }

        if (!$this->db->table_exists(TABLE_COLLEGE)) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'established_year' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
                'address' => ['type' => 'TEXT', 'null' => FALSE],
                'description' => ['type' => 'TEXT', 'null' => TRUE],
                'city' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
                'state' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
                'phone_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE],
                'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE, 'unique' => TRUE],
                'logo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'banner' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table(TABLE_COLLEGE, TRUE);
        }

        // Seed default single college if missing
        $existing = $this->db->get_where(TABLE_COLLEGE, ['id' => SINGLE_COLLEGE_ID])->row_array();
        if (!$existing) {
            $this->db->insert(TABLE_COLLEGE, [
                'id' => SINGLE_COLLEGE_ID,
                'name' => 'Standard Fireworks Rajaratnam College for Women',
                'established_year' => '1968',
                'address' => 'Thiruthangal Road, Sivakasi – 626123, Virudhunagar District, Tamil Nadu, India',
                'description' => 'Autonomous women’s college affiliated to Madurai Kamaraj University; NAAC A+; focus on academic excellence and women’s development.',
                'city' => 'Sivakasi',
                'state' => 'Tamil Nadu',
                'phone_number' => '+91 4562 220389',
                'email' => 'sfrc@sfrcollege.edu.in',
                'logo' => null,
                'banner' => null,
                'is_active' => 1,
                'created_by' => 0,
                'updated_by' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists(TABLE_COLLEGE)) {
            $this->dbforge->drop_table(TABLE_COLLEGE, TRUE);
        }
    }
}

