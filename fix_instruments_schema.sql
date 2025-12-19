-- Fix instruments table schema
-- Run these SQL commands in your MySQL database

-- Add missing columns
ALTER TABLE `instruments` ADD COLUMN IF NOT EXISTS `condition` ENUM('excellent','good','fair','poor','damaged') NOT NULL DEFAULT 'good' AFTER `instrument_image`;
ALTER TABLE `instruments` ADD COLUMN IF NOT EXISTS `description` TEXT AFTER `condition`;
ALTER TABLE `instruments` ADD COLUMN IF NOT EXISTS `created_by` INT(11) NOT NULL AFTER `description`;

-- Change availability_status to tinyint
ALTER TABLE `instruments` MODIFY COLUMN `availability_status` TINYINT(1) NOT NULL DEFAULT 1;

-- Update existing string values to numeric constants
UPDATE `instruments` SET `availability_status` = 1 WHERE `availability_status` = 'available' OR `availability_status` = 1;
UPDATE `instruments` SET `availability_status` = 2 WHERE `availability_status` = 'issued' OR `availability_status` = 2;
UPDATE `instruments` SET `availability_status` = 3 WHERE `availability_status` = 'maintenance' OR `availability_status` = 3;
UPDATE `instruments` SET `availability_status` = 4 WHERE `availability_status` = 'damaged' OR `availability_status` = 4;