-- Update college table to add new fields and remove description
-- Add new columns for correspondent, vice_correspondent, our_vision, our_mission
-- Remove description column

ALTER TABLE `college`
ADD COLUMN `correspondent` VARCHAR(255) DEFAULT NULL AFTER `description`,
ADD COLUMN `vice_correspondent` VARCHAR(255) DEFAULT NULL AFTER `correspondent`,
ADD COLUMN `our_vision` TEXT DEFAULT NULL AFTER `vice_correspondent`,
ADD COLUMN `our_mission` TEXT DEFAULT NULL AFTER `our_vision`;

-- Remove the description column (if it exists)
ALTER TABLE `college` DROP COLUMN `description`;
