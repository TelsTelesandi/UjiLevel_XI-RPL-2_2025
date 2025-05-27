-- Check if table exists and show its structure
SHOW TABLES LIKE 'verifikasi_event';
DESCRIBE verifikasi_event;

-- Check for any existing records
SELECT * FROM verifikasi_event;

-- Check for any foreign key constraints
SELECT 
    TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_NAME = 'event_pengajuan'; 