-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2025 at 07:06 PM
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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `visibility`, `department_id`, `sender_id`, `college_id`, `priority`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Hi', 'smdsk', 'all', NULL, 1, 1, 'normal', 1, NULL, NULL, '2025-12-20 06:55:53', '2025-12-20 06:55:53'),
(2, 'Welcome to the New Academic Year', 'Dear students, welcome to the new academic year! We are excited to have you back and look forward to an enriching learning experience. Please check your course schedules and ensure you have all required materials.', 'all', NULL, 1, 1, 'normal', 1, 1, 1, '2025-12-21 09:15:28', '2025-12-21 09:15:28'),
(3, 'Important: Course Registration Deadline', 'This is a reminder that course registration for the upcoming semester closes in 3 days. Students who have not yet registered should do so immediately through the student portal. Late registrations will not be accepted.', 'all', NULL, 1, 1, 'high', 1, 1, 1, '2025-12-20 09:15:28', '2025-12-20 09:15:28'),
(4, 'Computer Science Department Meeting', 'All Computer Science students are required to attend the department orientation meeting on Friday at 2 PM in Room 101. Topics will include curriculum updates, internship opportunities, and important department policies.', 'department', 1, 1, 1, 'normal', 1, 1, 1, '2025-12-19 09:15:28', '2025-12-19 09:15:28'),
(5, 'Welcome to the New Academic Year', 'Dear students, welcome to the new academic year! We are excited to have you back and look forward to an enriching learning experience. Please check your course schedules and ensure you have all required materials.', 'all', NULL, 1, 1, 'normal', 1, 1, 1, '2025-12-21 09:19:39', '2025-12-21 09:19:39'),
(6, 'Important: Course Registration Deadline', 'This is a reminder that course registration for the upcoming semester closes in 3 days. Students who have not yet registered should do so immediately through the student portal. Late registrations will not be accepted.', 'all', NULL, 1, 1, 'high', 1, 1, 1, '2025-12-20 09:19:39', '2025-12-20 09:19:39'),
(7, 'Computer Science Department Meeting', 'All Computer Science students are required to attend the department orientation meeting on Friday at 2 PM in Room 101. Topics will include curriculum updates, internship opportunities, and important department policies.', 'department', 1, 1, 1, 'normal', 1, 1, 1, '2025-12-19 09:19:39', '2025-12-19 09:19:39');

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
  `established_year` year(4) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
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

INSERT INTO `college` (`id`, `name`, `email`, `phone`, `address`, `city`, `state`, `established_year`, `website`, `description`, `logo`, `banner`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Demo College', 'info@democollege.com', '+1-234-567-8900', '123 College Street, City, State 12345', 'Srivilliputhur', 'Tamil Nadu', 2000, 'https://www.democollege.com', 'A comprehensive educational institution offering quality education.', 'logo_1766151432_7760.jpg', 'banner_1766151454_7961.jpg', 1, 1, NULL, '2025-12-13 18:46:12', '2025-12-19 13:37:34');

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

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `course_code`, `tag`, `college_id`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Test College', 'aDs', '982818', NULL, 1, 1, NULL, 1, '2025-12-18 07:54:18', '2025-12-18 07:54:18');

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

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `course_id`, `student_id`, `enrolled_by`, `progress_percentage`, `status`, `created_by`, `updated_by`, `enrolled_at`, `completed_at`, `updated_at`) VALUES
(1, 1, 1, 1, '0.00', 'enrolled', 1, NULL, '2025-12-18 07:54:18', NULL, '2025-12-18 07:54:18');

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

--
-- Dumping data for table `course_modules`
--

INSERT INTO `course_modules` (`id`, `course_id`, `name`, `description`, `order`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'new', 'sdsf', 1, 1, NULL, 1, '2025-12-18 07:54:52', '2025-12-18 07:54:52'),
(2, 1, 'Introduction to Test College', 'This module provides an introduction to the basic concepts of Test College. You will learn the fundamental principles and get started with practical exercises.', 1, 1, NULL, 1, '2025-12-21 03:39:32', '2025-12-21 03:39:32'),
(3, 1, 'Advanced Topics in Test College', 'This advanced module covers complex topics and advanced techniques in Test College. Prerequisites: Basic knowledge of the subject.', 2, 1, NULL, 1, '2025-12-21 03:39:32', '2025-12-21 03:39:32'),
(4, 1, 'Practical Applications', 'In this module, you will apply what you\'ve learned through hands-on projects and real-world scenarios. This module includes assignments and case studies.', 3, 1, NULL, 1, '2025-12-21 03:39:32', '2025-12-21 03:39:32'),
(5, 1, 'Introduction to Test College', 'This module provides an introduction to the basic concepts of Test College. You will learn the fundamental principles and get started with practical exercises.', 1, 1, NULL, 1, '2025-12-21 04:29:46', '2025-12-21 04:29:46'),
(6, 1, 'Advanced Topics in Test College', 'This advanced module covers complex topics and advanced techniques in Test College. Prerequisites: Basic knowledge of the subject.', 2, 1, NULL, 1, '2025-12-21 04:29:46', '2025-12-21 04:29:46'),
(7, 1, 'Practical Applications', 'In this module, you will apply what you\'ve learned through hands-on projects and real-world scenarios. This module includes assignments and case studies.', 3, 1, NULL, 1, '2025-12-21 04:29:46', '2025-12-21 04:29:46'),
(8, 1, 'Introduction to Test College', 'This module provides an introduction to the basic concepts of Test College. You will learn the fundamental principles and get started with practical exercises.', 1, 1, NULL, 1, '2025-12-21 04:49:37', '2025-12-21 04:49:37'),
(9, 1, 'Advanced Topics in Test College', 'This advanced module covers complex topics and advanced techniques in Test College. Prerequisites: Basic knowledge of the subject.', 2, 1, NULL, 1, '2025-12-21 04:49:37', '2025-12-21 04:49:37'),
(10, 1, 'Practical Applications', 'In this module, you will apply what you\'ve learned through hands-on projects and real-world scenarios. This module includes assignments and case studies.', 3, 1, NULL, 1, '2025-12-21 04:49:37', '2025-12-21 04:49:37');

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

--
-- Dumping data for table `course_module_lessons`
--

INSERT INTO `course_module_lessons` (`id`, `module_id`, `title`, `type`, `content`, `course_text`, `course_url`, `course_file`, `duration`, `order`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(4, 1, 'Lesson - one', 'text', 'cccccccccccccccccccccccccccc', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '', NULL, '2 hurs', 1, 1, 1, NULL, '2025-12-20 15:44:08', '2025-12-20 15:44:08'),
(5, 1, 'Lesson - two', 'video', 'gan', '', 'https://youtu.be/y-OQ7tdU8kI?si=h8cAjnNhDElGOato', NULL, '3 hrs', 2, 1, 1, NULL, '2025-12-20 15:50:31', '2025-12-20 15:50:31'),
(8, 1, 'Leson thre', 'file', 'bhbjj', '', '', 'uploads/course_files/lesson_1766248377_1424.pdf', '', 3, 1, 1, NULL, '2025-12-20 16:32:57', '2025-12-20 16:32:57');

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
(1, 'Instrumental Music', 1, 1, 1, NULL, '2025-12-13 18:46:12', '2025-12-13 18:46:12'),
(2, 'Vocal Music', 1, 1, 1, NULL, '2025-12-13 18:46:12', '2025-12-13 18:46:12'),
(3, 'Percussion', 1, 1, 1, NULL, '2025-12-13 18:46:12', '2025-12-13 18:46:12'),
(4, 'Western Music', 1, 1, 1, NULL, '2025-12-13 18:46:12', '2025-12-13 18:46:12'),
(5, 'Indian Classical Musica', 1, 1, 1, 1, '2025-12-13 18:46:12', '2025-12-18 01:13:59'),
(6, 'Music Theory & Composition', 1, 0, 1, NULL, '2025-12-13 18:46:12', '2025-12-18 01:13:51'),
(7, 'adaf', 1, 1, 1, NULL, '2025-12-18 01:14:08', '2025-12-18 01:14:08');

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
(1, 'Super Administrator', 'admin@college.com', '+1-234-567-8901', '$2y$10$51hpKyG1m7Ds5r73nH9zd.uAh6//21rqenThMNFONjs1Y2tI8jjFG', 1, 1, NULL, NULL, 1, '2025-12-14', NULL, 1, NULL, NULL, '2025-12-13 18:46:12', '2025-12-13 18:46:12'),
(2, 'koa', 'koa@gmail.com', '6412324311', '$2y$10$EgplQ9HiuKrYhdw82UPsYeFik7lD2h8LqSYOEmzinzVBbLvByHyHq', 2, NULL, NULL, NULL, 1, NULL, NULL, 1, 0, NULL, '2025-12-16 03:02:43', '2025-12-16 03:02:43'),
(3, 'K Ganesh krishna', 'ganeshkrishna203@gmail.com', '06380249114', '$2y$10$Fgz6ZiCkBKEDgnP.b0Iby.mB8MRDGi2JvUFyZdTAt5pgEw9ilkd8e', 4, 4, '2', NULL, 1, NULL, NULL, 1, 1, NULL, '2025-12-17 13:12:36', '2025-12-17 13:12:36'),
(4, 'prabhkaran', 'prab@gmail.com', '0987654321', '$2y$10$uKSrXLdhinCxPS.6SwxPIO0MCLgjJFrwemLipzqWQkFPboCecrXZW', 5, 5, '5', NULL, 1, NULL, NULL, 1, 1, 1, '2025-12-18 00:22:36', '2025-12-18 00:25:44'),
(5, 'aloa', 'ald@gmail.com', '1234567890', '$2y$10$I8VDiurezmcRtkHgFlzZouohwdqgS6dPy0S5Hd6Wd/u.xo8rwYjZa', 6, 6, '3', NULL, 1, NULL, NULL, 1, 1, NULL, '2025-12-18 00:27:35', '2025-12-18 00:27:35'),
(6, 'K Ganesh krishna', 'its2@demo.in', '06380249114', '$2y$10$kZ5zwIxqj9WaXMV.eyiAcehyB3Y.VKrWjaz0BLHTp2aThaKP9nYxK', 3, 3, NULL, NULL, 1, NULL, NULL, 1, 1, NULL, '2025-12-20 01:44:48', '2025-12-20 01:44:48');

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

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `college_id`, `group_expiry`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'afafaaa', 'hiugj', 1, NULL, 1, 1, 1, '2025-12-13 18:46:49', '2025-12-18 02:31:03'),
(4, 'afad', 'yguuvgh', 1, NULL, 1, NULL, 0, '2025-12-18 02:30:42', '2025-12-18 02:31:09');

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
  `availability_status` varchar(20) NOT NULL DEFAULT 'available',
  `condition` varchar(20) NOT NULL DEFAULT 'good',
  `college_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instruments`
--

INSERT INTO `instruments` (`id`, `name`, `category`, `serial_no`, `model`, `brand`, `condition_notes`, `instrument_price`, `instrument_image`, `description`, `availability_status`, `condition`, `college_id`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Portable Keyboard', 'keyboard', 'JRK660', 'Octavé ', 'Juarez ', 'do nor make wet', '2999.00', 'uploads/instruments/d813cbb11260f15175e3c6551d07330f.png', 'Aaaaaaaaaaaaaaaaaaaaaaaaaa', 'available', 'excellent', 1, 1, NULL, 1, '2025-12-20 03:07:27', '2025-12-20 13:28:30');

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

--
-- Dumping data for table `instrument_issues`
--

INSERT INTO `instrument_issues` (`id`, `instrument_id`, `student_id`, `faculty_id`, `issued_by`, `issue_date`, `expected_return_date`, `actual_return_date`, `status`, `condition_on_issue`, `condition_on_return`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 1, '2025-12-20 00:00:00', '2025-12-31 00:00:00', '2025-12-20 00:00:00', 'returned', NULL, 'good', 'Quick return via issues page', NULL, NULL, '2025-12-20 13:19:03', '2025-12-20 13:28:30');

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

--
-- Dumping data for table `memgroups`
--

INSERT INTO `memgroups` (`id`, `group_id`, `student_id`, `college_id`, `added_by`, `created_by`, `updated_by`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 1, NULL, 1, NULL, 1, '2025-12-18 17:56:10', '2025-12-18 17:56:10');

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
  `role` int(11) DEFAULT 6,
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
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `password`, `roll_no`, `department`, `batch`, `role`, `college_id`, `joining_date`, `file_path`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'K Ganesh krishna', 'ganeshkrishna203@gmail.com', '06380249114', '$2y$10$c3f7Uup1OdBOCN2ursT22eTVS.07OxL6P3mSyXC2SlYr4m8SbqSPa', 'ug1221', '1', '2025', 6, 1, NULL, NULL, 1, 1, NULL, '2025-12-13 18:47:12', '2025-12-21 02:18:53');

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `college_id` (`college_id`),
  ADD KEY `idx_students_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `college`
--
ALTER TABLE `college`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_module_lessons`
--
ALTER TABLE `course_module_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `instruments`
--
ALTER TABLE `instruments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `instrument_categories`
--
ALTER TABLE `instrument_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instrument_issues`
--
ALTER TABLE `instrument_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `memgroups`
--
ALTER TABLE `memgroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
