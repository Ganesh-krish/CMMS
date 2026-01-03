-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2026 at 02:43 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `college`
--

CREATE TABLE `college` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `established_year` year(4) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `college`
--

INSERT INTO `college` (`id`, `name`, `email`, `phone`, `address`, `city`, `state`, `website`, `established_year`, `description`, `logo`, `banner`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Demo College', 'info@democollege.com', '+1-234-567-8900', '123 College Street, City, State 12345', 'Srivilliputhur', 'Tamil Nadu', 'https://www.democollege.com', 2025, 'A comprehensive educational institution offering quality education.', 'logo_1767403549_6291.jpg', 'logo_1767403549_62911.jpg', 1, 2, NULL, '2026-01-02 15:18:46', '2026-01-03 01:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `college_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `enrolled_by` int(11) NOT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('enrolled','in_progress','completed','dropped') NOT NULL DEFAULT 'enrolled',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `enrolled_at` timestamp NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--

CREATE TABLE `course_modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_module_lessons`
--

CREATE TABLE `course_module_lessons` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `content` text NOT NULL,
  `course_text` text DEFAULT NULL,
  `course_url` varchar(500) DEFAULT NULL,
  `course_file` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `college_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `college_id`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Instrumental Music', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(2, 'Vocal Music', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(3, 'Percussion', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(4, 'Western Music', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(5, 'Indian Classical Music', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(6, 'Music Theory & Composition', 1, 1, 1, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 5,
  `designation` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `other_department` text DEFAULT NULL,
  `college_id` int(11) NOT NULL,
  `joining_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `name`, `email`, `phone`, `password`, `role`, `designation`, `department`, `other_department`, `college_id`, `joining_date`, `file_path`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin@college.com', '+1-234-567-8901', '$2y$10$JDHAfl6iDGYh/u4QRH5eSOPUD2zT1TWBVvAyTe1egycvCq7ujYEaS', 1, 1, NULL, NULL, 1, '2026-01-02', NULL, 1, NULL, NULL, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(2, 'K Ganesh krishna', 'ganeshkrishna203@gmail.com', '06380249114', '$2y$10$6LoW49tNrsJ1qiCAtEYxFuYPMNKE9dpNfjz6vyGtOF0PKHR9p/WGm', 2, 2, NULL, NULL, 1, NULL, NULL, 1, 1, NULL, '2026-01-02 15:19:39', '2026-01-02 15:19:39'),
(3, 'prabha', 'prabha@gmail.com', '1234567890', '$2y$10$WFm.f./3EvzyHfysq89KXucayQAimxNDci7FgTkuwENRrJ49s9SMC', 3, 3, '4', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:17:35', '2026-01-03 01:17:35'),
(4, 'Mani', 'mani@gmail.com', '7380249114', '$2y$10$LdVHKr892Sd0WS4AdxiYbeBnjxSh5XmZKynEP6WoGoVdP0rZrY5JK', 3, 3, '2', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:32:31', '2026-01-03 01:32:31'),
(5, 'surya', 'surya@gmail.com', '06380249114', '$2y$10$k5pdH..RKL28J9F5MpuzmOftFLC86u3/TDH1ajL36eWFx3d1gosl2', 3, 3, '3', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:33:24', '2026-01-03 01:33:24'),
(6, 'ramesh', 'ramesh@gamil.com', '8380249114', '$2y$10$74w0fAakLMo79Pa3EF46Tu0CZviSixkPnBEArxngmM3G0NceMtOxW', 4, 4, '2', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:36:47', '2026-01-03 01:36:47'),
(7, 'Raj', 'Raj@gmail.com', '7380249114', '$2y$10$7Rt4Mm5UMPg7pYQ6St3Tb.cg0RMjFzJ0pyjPUfqpFVe9rriVbaEcu', 4, 4, '3', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:37:16', '2026-01-03 01:37:16'),
(8, 'muthu', 'muthu@gmail.com', '7380249114', '$2y$10$cRNJK9dlSupOdax2hBnl2.vC4.bEDpurlkNYSP960wpU2yx7T44e2', 4, 4, '4', NULL, 1, NULL, NULL, 1, 2, NULL, '2026-01-03 01:38:40', '2026-01-03 01:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `college_id` int(11) NOT NULL,
  `group_expiry` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instruments`
--

CREATE TABLE `instruments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `condition_notes` text DEFAULT NULL,
  `instrument_price` decimal(10,2) DEFAULT NULL,
  `instrument_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `availability_status` tinyint(1) NOT NULL DEFAULT 1,
  `condition` varchar(20) NOT NULL DEFAULT 'good',
  `college_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instrument_categories`
--

CREATE TABLE `instrument_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instrument_categories`
--

INSERT INTO `instrument_categories` (`id`, `name`, `description`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'String Instruments', 'Guitar, Violin, etc.', NULL, NULL, 1, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(2, 'Percussion Instruments', 'Drums, Tabla, etc.', NULL, NULL, 1, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(3, 'Wind Instruments', 'Flute, Trumpet, etc.', NULL, NULL, 1, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(4, 'Keyboard Instruments', 'Piano, Keyboard, etc.', NULL, NULL, 1, '2026-01-02 15:18:46', '2026-01-02 15:18:46'),
(5, 'Electronic Instruments', 'Synthesizers, etc.', NULL, NULL, 1, '2026-01-02 15:18:46', '2026-01-02 15:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `instrument_issues`
--

CREATE TABLE `instrument_issues` (
  `id` int(11) NOT NULL,
  `instrument_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `issued_by` int(11) NOT NULL,
  `issue_date` datetime NOT NULL,
  `expected_return_date` datetime DEFAULT NULL,
  `actual_return_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'issued',
  `condition_on_issue` text DEFAULT NULL,
  `condition_on_return` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memgroups`
--

CREATE TABLE `memgroups` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(20) DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `password`, `roll_no`, `department`, `batch`, `college_id`, `joining_date`, `file_path`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `role`) VALUES
(1, 'john', 'its2@demo.in', '06380249114', '$2y$10$a4KXCxS.FXTD2fCE0E/zv.F0Svbb0ybvIA8Mud3uMR6DF63D0Cltu', 'ug1221', '4', '2026', 1, NULL, NULL, 1, NULL, NULL, '2026-01-03 01:28:07', '2026-01-03 01:28:07', '6'),
(2, 'doe', 'its1@demo.in', '0638024911', '$2y$10$8jqNh8jze5oadzahYOZjNOiaPWOSzFtW.2SE.M6Zq5zKdUcPT8Cqe', 'ug1222', '4', '2026', 1, NULL, NULL, 1, NULL, NULL, '2026-01-03 01:28:44', '2026-01-03 01:28:44', '6'),
(3, 'jim', 'eces1@demo.in', '06380249124', '$2y$10$Yt1xsQhpENlfQfMnsxCQFue6/y2tUP78k5lQ6Tqv3rBq6byF5SIwW', 'ug1223', '3', '2026', 1, NULL, NULL, 1, NULL, NULL, '2026-01-03 01:30:00', '2026-01-03 01:30:00', '6'),
(4, 'cook', 'eces2@demo.in', '8380249114', '$2y$10$7xcxKCrC.xVgz6xjJieDO.TsqHhCWVIhmYf3lPrK5EycMVURySOrC', 'ug1224', '2', '2026', 1, NULL, NULL, 1, NULL, NULL, '2026-01-03 01:31:46', '2026-01-03 01:31:46', '6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `visibility` (`visibility`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_visibility_dept` (`visibility`,`department_id`);

--
-- Indexes for table `college`
--
ALTER TABLE `college`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `enrolled_by` (`enrolled_by`);

--
-- Indexes for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_module_lessons`
--
ALTER TABLE `course_module_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `instruments`
--
ALTER TABLE `instruments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `availability_status` (`availability_status`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `instrument_categories`
--
ALTER TABLE `instrument_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `instrument_issues`
--
ALTER TABLE `instrument_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instrument_id` (`instrument_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `memgroups`
--
ALTER TABLE `memgroups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_student` (`group_id`,`student_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `college_id` (`college_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `college`
--
ALTER TABLE `college`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_module_lessons`
--
ALTER TABLE `course_module_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instruments`
--
ALTER TABLE `instruments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instrument_categories`
--
ALTER TABLE `instrument_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `instrument_issues`
--
ALTER TABLE `instrument_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memgroups`
--
ALTER TABLE `memgroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcements_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_announcements_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_announcements_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_courses_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `fk_course_enrollments_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_enrollments_enrolled_by` FOREIGN KEY (`enrolled_by`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_enrollments_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD CONSTRAINT `fk_course_modules_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_module_lessons`
--
ALTER TABLE `course_module_lessons`
  ADD CONSTRAINT `fk_course_module_lessons_module_id` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `fk_faculty_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `fk_groups_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instruments`
--
ALTER TABLE `instruments`
  ADD CONSTRAINT `fk_instruments_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instrument_issues`
--
ALTER TABLE `instrument_issues`
  ADD CONSTRAINT `fk_instrument_issues_faculty_id` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_instrument_issues_instrument_id` FOREIGN KEY (`instrument_id`) REFERENCES `instruments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_instrument_issues_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `memgroups`
--
ALTER TABLE `memgroups`
  ADD CONSTRAINT `fk_memgroups_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_memgroups_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_memgroups_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_college_id` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
