-- Migration: Update instruments table with new library-like fields
-- Run this SQL script in your MySQL database to update the instruments table

-- Add new columns to instruments table
ALTER TABLE `instruments`
ADD COLUMN `issue_date` date DEFAULT NULL AFTER `condition_notes`,
ADD COLUMN `due_date` date DEFAULT NULL AFTER `issue_date`,
ADD COLUMN `instrument_price` decimal(10,2) DEFAULT NULL AFTER `due_date`,
ADD COLUMN `instrument_image` varchar(255) DEFAULT NULL AFTER `instrument_price`;

-- Rename existing columns (if they exist)
-- Note: These ALTER TABLE statements will only work if the columns exist
-- If purchase_date/purchase_cost don't exist, these will be ignored

-- Rename purchase_date to issue_date (only if purchase_date exists)
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'instruments'
    AND COLUMN_NAME = 'purchase_date'
);

SET @sql = IF(@column_exists > 0,
    'ALTER TABLE instruments CHANGE COLUMN purchase_date issue_date DATE DEFAULT NULL',
    'SELECT "purchase_date column does not exist, skipping rename"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Rename purchase_cost to instrument_price (only if purchase_cost exists)
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'instruments'
    AND COLUMN_NAME = 'purchase_cost'
);

SET @sql = IF(@column_exists > 0,
    'ALTER TABLE instruments CHANGE COLUMN purchase_cost instrument_price DECIMAL(10,2) DEFAULT NULL',
    'SELECT "purchase_cost column does not exist, skipping rename"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create uploads directory for instrument images (this needs to be done manually)
-- Run this command on your server: mkdir -p /path/to/your/project/uploads/instruments

COMMIT;