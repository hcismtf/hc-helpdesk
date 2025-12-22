-- ============================================================================
-- CHECK: Collation dan Character Set
-- ============================================================================

-- Check users table
SHOW CREATE TABLE users\G

-- Check report_jobs table
SHOW CREATE TABLE report_jobs\G

-- Check column details
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLUMN_TYPE,
    COLLATION_NAME,
    CHARACTER_SET_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'hc_helpdesk'
AND (
    (TABLE_NAME = 'users' AND COLUMN_NAME = 'id')
    OR (TABLE_NAME = 'report_jobs' AND COLUMN_NAME = 'created_by')
);

-- If collation mismatch, fix report_jobs.created_by to match users.id
-- ALTER TABLE report_jobs MODIFY created_by VARCHAR(36) COLLATE utf8mb4_unicode_ci NULL;

-- Then try FK constraint again
-- ALTER TABLE report_jobs ADD CONSTRAINT fk_report_jobs_users 
--     FOREIGN KEY (created_by) REFERENCES users(id)
--     ON DELETE SET NULL 
--     ON UPDATE CASCADE;
