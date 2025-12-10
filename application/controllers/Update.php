<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Update extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the database library
        $this->load->database();
    }

    public function index()
    {
        // Optional: Check for a security key to prevent unauthorized access
        if ($this->input->get('key') !== DB_INSTALL_KEY) {
            echo 'Access Denied';
            exit;
        }

        // Start transaction
        $this->db->trans_start();

        try {
            // Add your ALTER TABLE statements here
            // Example:
            // $this->db->query("ALTER TABLE table_name ADD COLUMN new_column VARCHAR(255)");
            
            // Add new tables 
            $this->add_new_tables();

            // Add new columns to existing tables
            $this->add_new_columns();

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo 'Database update failed';
            } else {
                echo 'Database updated successfully';
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo 'Failed: ' . $e->getMessage();
        }
    }

    private function add_new_columns()
    {
        // Add new columns to existing tables
        // Example
        // $this->db->query("ALTER TABLE courses ADD COLUMN course_type  tinyint DEFAULT 1");
        // $this->db->query("ALTER TABLE tests ADD COLUMN negative_mark_value INT DEFAULT 0");
        // $this->db->query("ALTER TABLE course_tests ADD COLUMN level INT DEFAULT 0");
        // $this->db->query("ALTER TABLE course_tests ADD COLUMN pass_score DECIMAL(5,2) DEFAULT 0");
        // $this->db->query("ALTER TABLE courses ADD COLUMN course_mode  tinyint DEFAULT 1");
        // $this->db->query("ALTER TABLE student_test_submission ADD COLUMN attempts INT DEFAULT 1");
        // $this->db->query("ALTER TABLE student_test_submission ADD COLUMN sync_count INT DEFAULT 0");
        // $this->db->query("ALTER TABLE test_settings_security ADD COLUMN shuffle_answer_options tinyint DEFAULT 0");
        // $this->db->query("ALTER TABLE tests ADD COLUMN is_new_tab tinyint DEFAULT 0");

        // Update existing rows (old records) to have sync_count = 1
        // if ($this->db->field_exists('sync_count', 'student_test_submission')) {
        //     $this->db->query("UPDATE student_test_submission SET sync_count = 1");
        // }

        // $this->db->query("ALTER TABLE test_settings_ui ADD COLUMN enable_sections tinyint DEFAULT 0");
        // $this->db->query("ALTER TABLE test_questions ADD COLUMN section_id INT DEFAULT NULL AFTER test_id");
    }

    private function add_new_tables()
    {
    
    // if (!$this->db->table_exists('special_courses')) 
    // {
    //     $this->db->query("CREATE TABLE `special_courses` (
    //     `id` int(11) NOT NULL AUTO_INCREMENT,
    //     `course_id` int(11) NOT NULL,
    //     `to_college_id` int(11) NOT NULL,
    //     `shared_by_owner_id` int(11) NOT NULL,
    //     `is_active` tinyint(1) DEFAULT 1,
    //     `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    //     `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    //     PRIMARY KEY (`id`)
    //     );");
    // }

        // if (!$this->db->table_exists('test_sections'))
        // {
        //      $this->db->query("CREATE TABLE `test_sections` (
        //         `id` INT(11) NOT NULL AUTO_INCREMENT,
        //         `section_name` VARCHAR(255) NOT NULL,
        //         `is_active` TINYINT(1) DEFAULT 1,
        //         `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //         `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        //         PRIMARY KEY (`id`)
        //     );");

        //     // Insert default row
        //     $this->db->insert('test_sections', ['section_name' => '$@#UNASSIGNED_QAS#@$']);

        // }
    }
} 

