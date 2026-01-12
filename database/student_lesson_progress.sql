-- --------------------------------------------------------
--
-- Table structure for table `student_lesson_progress`
-- Tracks individual lesson progress for each student enrollment
--
CREATE TABLE IF NOT EXISTS `student_lesson_progress` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` INT(11) NOT NULL,
  `lesson_id` INT(11) NOT NULL,
  `module_id` INT(11) NOT NULL,
  `course_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `status` ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lesson_progress` (`enrollment_id`, `lesson_id`),
  KEY `enrollment_id` (`enrollment_id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `module_id` (`module_id`),
  KEY `course_id` (`course_id`),
  KEY `student_id` (`student_id`),
  KEY `status` (`status`),
  CONSTRAINT `student_lesson_progress_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `course_enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `course_module_lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_ibfk_4` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_ibfk_5` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
