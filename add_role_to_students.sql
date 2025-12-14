-- Add role field to students table for unified authentication
ALTER TABLE students ADD COLUMN role VARCHAR(20) DEFAULT 'student';

-- Update existing students to have 'student' role
UPDATE students SET role = 'student' WHERE role IS NULL OR role = '';

-- Add index for better performance
ALTER TABLE students ADD INDEX idx_email_role (email, role);