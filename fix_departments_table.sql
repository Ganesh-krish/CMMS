-- Add missing college_id column to departments table
ALTER TABLE `departments` ADD COLUMN `college_id` INT NOT NULL AFTER `name`;

-- Add foreign key constraint for college_id
ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_college_id`
    FOREIGN KEY (`college_id`) REFERENCES `college`(`id`) ON DELETE CASCADE;

-- Add index for better performance
ALTER TABLE `departments` ADD INDEX `idx_departments_college_id` (`college_id`);

-- Optional: Update existing departments to have a default college_id
-- You may need to set this based on your college setup
-- UPDATE `departments` SET `college_id` = 1 WHERE `college_id` IS NULL OR `college_id` = 0;