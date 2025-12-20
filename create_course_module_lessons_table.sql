-- Create course_module_lessons table with all required columns
-- Run this SQL script to create the complete table

USE your_database_name; -- Replace with your actual database name

-- Create course_module_lessons table
CREATE TABLE IF NOT EXISTS `course_module_lessons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `module_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `type` varchar(20) NOT NULL DEFAULT 'text',
    `content` text NOT NULL,
    `course_text` text,
    `course_url` varchar(500) DEFAULT NULL,
    `course_file` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Create uploads directory for course files
-- You may need to create this directory manually or via PHP:
-- mkdir('./uploads/course_files/', 0777, true);