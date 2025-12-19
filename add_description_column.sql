-- Add description column to instruments table
-- Run this SQL command in your MySQL database

ALTER TABLE `instruments` ADD COLUMN `description` TEXT AFTER `condition`;