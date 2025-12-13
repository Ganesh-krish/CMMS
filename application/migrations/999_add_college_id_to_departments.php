<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_college_id_to_departments extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        // Add college_id column to departments table
        $fields = [
            'college_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'after' => 'name'
            ]
        ];

        $this->dbforge->add_column('departments', $fields);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_college_id`
            FOREIGN KEY (`college_id`) REFERENCES `college`(`id`) ON DELETE CASCADE');

        // Add index for performance
        $this->dbforge->add_key('college_id');
        $this->dbforge->create_table_indexes('departments');
    }

    public function down()
    {
        $this->load->dbforge();

        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE `departments` DROP FOREIGN KEY `fk_departments_college_id`');

        // Drop the column
        $this->dbforge->drop_column('departments', 'college_id');
    }
}