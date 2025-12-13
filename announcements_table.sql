-- Create announcements table
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `visibility` enum('all','department') NOT NULL DEFAULT 'all',
    `department_id` int(11) DEFAULT NULL,
    `sender_id` int(11) NOT NULL,
    `college_id` int(11) NOT NULL,
    `priority` enum('normal','high') NOT NULL DEFAULT 'normal',
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sender_id` (`sender_id`),
    KEY `college_id` (`college_id`),
    KEY `department_id` (`department_id`),
    KEY `visibility` (`visibility`),
    KEY `is_active` (`is_active`),
    CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
    CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `college` (`id`) ON DELETE CASCADE,
    CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
ALTER TABLE `announcements` ADD INDEX `idx_created_at` (`created_at`);
ALTER TABLE `announcements` ADD INDEX `idx_visibility_dept` (`visibility`, `department_id`);