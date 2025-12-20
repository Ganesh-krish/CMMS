-- Migration: Add content columns to course_module_lessons table and change type to VARCHAR
-- Run this SQL script to add the new columns for lesson content storage

USE your_database_name; -- Replace with your actual database name

-- Add new columns to course_module_lessons table
ALTER TABLE `course_module_lessons`
ADD COLUMN `course_text` TEXT NULL AFTER `content`,
ADD COLUMN `course_url` VARCHAR(500) NULL AFTER `course_text`,
ADD COLUMN `course_file` VARCHAR(255) NULL AFTER `course_url`;

-- Change type from ENUM to VARCHAR and remove unused lesson types
ALTER TABLE `course_module_lessons`
MODIFY COLUMN `type` VARCHAR(20) NOT NULL DEFAULT 'text';

-- Optional: Create uploads directory for course files
-- You may need to create this directory manually or via PHP:
-- mkdir('./uploads/course_files/', 0777, true);