<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        // Security check - replace 'your_secure_key' with an actual secure key
        if ($this->input->get('key') !== 'cmms_install_2024') {
            echo 'Access Denied - Invalid Installation Key';
            exit;
        }

        // Start transaction
        $this->db->trans_start();

        try {
            // Drop all existing tables
            $this->drop_all_tables();

            // Create all tables
            $this->create_tables();

            // Insert default data
            $this->insert_default_data();

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo 'Installation failed. Please check database permissions and try again.';
            } else {
                echo 'Installation completed successfully! <br>';
                echo 'Default SuperAdmin credentials: <br>';
                echo 'Email: admin@college.com <br>';
                echo 'Password: admin123 <br>';
                echo 'Please change these credentials after first login.';
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo 'Installation failed: ' . $e->getMessage();
        }
    }

    private function drop_all_tables()
    {
        $tables = [
            'announcements',
            'instrument_issues',
            'instruments',
            'instrument_categories',
            'course_enrollments',
            'course_modules',
            'courses',
            'memgroups',
            'groups',
            'students',
            'faculty',
            'departments',
            'college'
        ];

        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->query("DROP TABLE `$table`");
                echo "Dropped table: $table <br>";
            }
        }
    }

    private function create_tables()
    {
        // College table
        $this->db->query("
            CREATE TABLE `college` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `address` text,
                `city` varchar(100) DEFAULT NULL,
                `state` varchar(100) DEFAULT NULL,
                `website` varchar(255) DEFAULT NULL,
                `established_year` year(4) DEFAULT NULL,
                `description` text,
                `logo` varchar(255) DEFAULT NULL,
                `banner` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: college <br>";

        // Departments table
        $this->db->query("
            CREATE TABLE `departments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `college_id` int(11) NOT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) NOT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `college_id` (`college_id`),
                CONSTRAINT `fk_departments_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: departments <br>";

        // Faculty table
        $this->db->query("
            CREATE TABLE `faculty` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `role` int(11) NOT NULL DEFAULT 5,
                `designation` int(11) DEFAULT NULL,
                `department` varchar(100) DEFAULT NULL,
                `other_department` text,
                `college_id` int(11) NOT NULL,
                `joining_date` date DEFAULT NULL,
                `file_path` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                KEY `college_id` (`college_id`),
                KEY `role` (`role`),
                CONSTRAINT `fk_faculty_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: faculty <br>";

        // Students table
        $this->db->query("
            CREATE TABLE `students` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `roll_no` varchar(50) DEFAULT NULL,
                `department` varchar(100) DEFAULT NULL,
                `batch` varchar(50) DEFAULT NULL,
                `college_id` int(11) NOT NULL,
                `joining_date` date DEFAULT NULL,
                `file_path` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                KEY `college_id` (`college_id`),
                CONSTRAINT `fk_students_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: students <br>";

        // Groups table
        $this->db->query("
            CREATE TABLE `groups` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `college_id` int(11) NOT NULL,
                `group_expiry` date DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `college_id` (`college_id`),
                CONSTRAINT `fk_groups_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: groups <br>";

        // Memgroups table (Group Members)
        $this->db->query("
            CREATE TABLE `memgroups` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `group_id` int(11) NOT NULL,
                `student_id` int(11) NOT NULL,
                `college_id` int(11) NOT NULL,
                `added_by` int(11) DEFAULT NULL,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_group_student` (`group_id`, `student_id`),
                KEY `group_id` (`group_id`),
                KEY `student_id` (`student_id`),
                KEY `college_id` (`college_id`),
                CONSTRAINT `fk_memgroups_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_memgroups_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_memgroups_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: memgroups <br>";

        // Courses table
        $this->db->query("
            CREATE TABLE `courses` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `course_code` varchar(50) DEFAULT NULL,
                `tag` varchar(100) DEFAULT NULL,
                `college_id` int(11) NOT NULL,
                `created_by` int(11) NOT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `college_id` (`college_id`),
                CONSTRAINT `fk_courses_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: courses <br>";

        // Course Modules table
        $this->db->query("
            CREATE TABLE `course_modules` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `course_id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text,
                `order` int(11) NOT NULL DEFAULT 0,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `course_id` (`course_id`),
                CONSTRAINT `fk_course_modules_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: course_modules <br>";

        // Course Enrollments table
        $this->db->query("
            CREATE TABLE `course_enrollments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `course_id` int(11) NOT NULL,
                `student_id` int(11) NOT NULL,
                `enrolled_by` int(11) NOT NULL,
                `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
                `status` enum('enrolled','in_progress','completed','dropped') NOT NULL DEFAULT 'enrolled',
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `completed_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`),
                KEY `student_id` (`student_id`),
                KEY `enrolled_by` (`enrolled_by`),
                CONSTRAINT `fk_course_enrollments_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_course_enrollments_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_course_enrollments_enrolled_by` FOREIGN KEY (`enrolled_by`) REFERENCES `faculty` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: course_enrollments <br>";

        // Course Module Lessons table
        $this->db->query("
            CREATE TABLE `course_module_lessons` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `module_id` int(11) NOT NULL,
                `title` varchar(255) NOT NULL,
                `type` enum('video','text','quiz','assignment','presentation') NOT NULL DEFAULT 'text',
                `content` text NOT NULL,
                `duration` varchar(50) DEFAULT NULL,
                `order` int(11) NOT NULL DEFAULT 0,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `module_id` (`module_id`),
                CONSTRAINT `fk_course_module_lessons_module_id` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: course_module_lessons <br>";


        // Instrument Categories table
        $this->db->query("
            CREATE TABLE `instrument_categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `description` text,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: instrument_categories <br>";

        // Instruments table
        $this->db->query("
            CREATE TABLE `instruments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `category` varchar(100) DEFAULT NULL,
                `serial_no` varchar(100) DEFAULT NULL,
                `model` varchar(100) DEFAULT NULL,
                `brand` varchar(100) DEFAULT NULL,
                `condition_notes` text,
                `instrument_price` decimal(10,2) DEFAULT NULL,
                `instrument_image` varchar(255) DEFAULT NULL,
                `description` text DEFAULT NULL,
                `availability_status` varchar(20) NOT NULL DEFAULT 'available',
                `condition` varchar(20) NOT NULL DEFAULT 'good',
                `college_id` int(11) NOT NULL,
                `created_by` int(11) NOT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `college_id` (`college_id`),
                KEY `availability_status` (`availability_status`),
                KEY `category` (`category`),
                CONSTRAINT `fk_instruments_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: instruments <br>";

        // Instrument Issues table
        $this->db->query("
            CREATE TABLE `instrument_issues` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `instrument_id` int(11) NOT NULL,
                `student_id` int(11) DEFAULT NULL,
                `faculty_id` int(11) DEFAULT NULL,
                `issued_by` int(11) NOT NULL,
                `issue_date` datetime NOT NULL,
                `expected_return_date` datetime DEFAULT NULL,
                `actual_return_date` datetime DEFAULT NULL,
                `status` varchar(20) NOT NULL DEFAULT 'issued',
                `condition_on_issue` text,
                `condition_on_return` text,
                `notes` text,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `instrument_id` (`instrument_id`),
                KEY `student_id` (`student_id`),
                KEY `faculty_id` (`faculty_id`),
                KEY `status` (`status`),
                CONSTRAINT `fk_instrument_issues_instrument_id` FOREIGN KEY (`instrument_id`) REFERENCES `instruments` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_instrument_issues_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
                CONSTRAINT `fk_instrument_issues_faculty_id` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: instrument_issues <br>";

        // Announcements table
        $this->db->query("
            CREATE TABLE `announcements` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `message` text NOT NULL,
                `visibility` enum('all','department') NOT NULL DEFAULT 'all',
                `department_id` int(11) DEFAULT NULL,
                `sender_id` int(11) NOT NULL,
                `college_id` int(11) NOT NULL,
                `priority` enum('normal','high') NOT NULL DEFAULT 'normal',
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `sender_id` (`sender_id`),
                KEY `college_id` (`college_id`),
                KEY `department_id` (`department_id`),
                KEY `visibility` (`visibility`),
                KEY `is_active` (`is_active`),
                CONSTRAINT `fk_announcements_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_announcements_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_announcements_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: announcements <br>";

        // Add indexes for better performance
        $this->db->query("ALTER TABLE `announcements` ADD INDEX `idx_created_at` (`created_at`)");
        $this->db->query("ALTER TABLE `announcements` ADD INDEX `idx_visibility_dept` (`visibility`, `department_id`)");

        echo "All tables created successfully! <br>";
    }

    private function insert_default_data()
    {
        // Insert default college
        $this->db->insert('college', [
            'name' => 'Demo College',
            'email' => 'info@democollege.com',
            'phone' => '+1-234-567-8900',
            'address' => '123 College Street, City, State 12345',
            'website' => 'https://www.democollege.com',
            'description' => 'A comprehensive educational institution offering quality education.',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $college_id = $this->db->insert_id();
        echo "Inserted default college (ID: $college_id) <br>";

        // Insert default departments
        $departments = [
            ['name' => 'Instrumental Music', 'college_id' => $college_id, 'created_by' => 1],
            ['name' => 'Vocal Music', 'college_id' => $college_id, 'created_by' => 1],
            ['name' => 'Percussion', 'college_id' => $college_id, 'created_by' => 1],
            ['name' => 'Western Music', 'college_id' => $college_id, 'created_by' => 1],
            ['name' => 'Indian Classical Music', 'college_id' => $college_id, 'created_by' => 1],
            ['name' => 'Music Theory & Composition', 'college_id' => $college_id, 'created_by' => 1]
        ];

        foreach ($departments as $dept) {
            $this->db->insert('departments', $dept);
        }
        echo "Inserted default departments <br>";

        // Insert SuperAdmin user
        $this->db->insert('faculty', [
            'name' => 'Super Administrator',
            'email' => 'admin@college.com',
            'phone' => '+1-234-567-8901',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => ROLE_PRINCIPAL,
            'designation' => ROLE_PRINCIPAL,
            'college_id' => $college_id,
            'joining_date' => date('Y-m-d'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $admin_id = $this->db->insert_id();
        echo "Inserted SuperAdmin user (ID: $admin_id) <br>";

        // Update department created_by
        $this->db->update('departments', ['created_by' => $admin_id], ['college_id' => $college_id]);


        // Insert default instrument categories
        $categories = [
            ['name' => 'String Instruments', 'description' => 'Guitar, Violin, etc.'],
            ['name' => 'Percussion Instruments', 'description' => 'Drums, Tabla, etc.'],
            ['name' => 'Wind Instruments', 'description' => 'Flute, Trumpet, etc.'],
            ['name' => 'Keyboard Instruments', 'description' => 'Piano, Keyboard, etc.'],
            ['name' => 'Electronic Instruments', 'description' => 'Synthesizers, etc.']
        ];

        foreach ($categories as $category) {
            $this->db->insert('instrument_categories', $category);
        }
        echo "Inserted default instrument categories <br>";

        echo "Default data insertion completed! <br>";
    }
}