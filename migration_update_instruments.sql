-- Migration: Update instruments table with new library-like fields
-- Run this SQL script in your MySQL database to update the instruments table

-- Add new columns to instruments table (if they don't exist)
SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'issue_date');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN issue_date DATE DEFAULT NULL AFTER condition_notes', 'SELECT "issue_date column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'due_date');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN due_date DATE DEFAULT NULL AFTER issue_date', 'SELECT "due_date column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'instrument_price');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN instrument_price DECIMAL(10,2) DEFAULT NULL AFTER due_date', 'SELECT "instrument_price column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'instrument_image');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN instrument_image VARCHAR(255) DEFAULT NULL AFTER instrument_price', 'SELECT "instrument_image column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add condition column if it doesn't exist (enum type)
SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'condition');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN `condition` ENUM(\'excellent\',\'good\',\'fair\',\'poor\',\'damaged\') NOT NULL DEFAULT \'good\' AFTER instrument_image', 'SELECT "condition column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add description column if it doesn't exist
SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'description');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN description TEXT AFTER `condition`', 'SELECT "description column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add created_by column if it doesn't exist
SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'created_by');
SET @sql = IF(@column_exists = 0, 'ALTER TABLE instruments ADD COLUMN created_by INT(11) NOT NULL AFTER description', 'SELECT "created_by column already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Change availability_status from enum to tinyint if it's still enum
SET @column_type = (SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'availability_status');
SET @sql = IF(@column_type = 'enum',
    'ALTER TABLE instruments MODIFY COLUMN availability_status TINYINT(1) NOT NULL DEFAULT 1',
    'SELECT "availability_status already tinyint or does not exist"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Update enum values to numeric constants
UPDATE instruments SET availability_status = CASE
    WHEN availability_status = 'available' THEN 1
    WHEN availability_status = 'issued' THEN 2
    WHEN availability_status = 'maintenance' THEN 3
    WHEN availability_status = 'damaged' THEN 4
    ELSE 1
END WHERE availability_status IN ('available', 'issued', 'maintenance', 'damaged');

-- Rename existing columns (if they exist)
-- Rename purchase_date to issue_date (only if purchase_date exists and issue_date doesn't)
SET @purchase_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'purchase_date');
SET @issue_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'issue_date');
SET @sql = IF(@purchase_exists > 0 AND @issue_exists = 0, 'ALTER TABLE instruments CHANGE COLUMN purchase_date issue_date DATE DEFAULT NULL', 'SELECT "purchase_date rename not needed"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Rename purchase_cost to instrument_price (only if purchase_cost exists and instrument_price doesn't)
SET @cost_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'purchase_cost');
SET @price_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instruments' AND COLUMN_NAME = 'instrument_price');
SET @sql = IF(@cost_exists > 0 AND @price_exists = 0, 'ALTER TABLE instruments CHANGE COLUMN purchase_cost instrument_price DECIMAL(10,2) DEFAULT NULL', 'SELECT "purchase_cost rename not needed"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;