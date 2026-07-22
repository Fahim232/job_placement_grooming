-- Add Company Logo Functionality Migration
-- This script adds logo support for companies

-- The companies table already has a logo field (VARCHAR 255)
-- This migration adds sample instructions for updating logos

-- To update a company logo manually via SQL:
-- UPDATE companies SET logo = 'uploads/company_logos/company_1_logo.png' WHERE id = 1;

-- OR via the company profile page:
-- 1. Login to company account
-- 2. Go to Profile page
-- 3. Upload logo in the "Company Logo" field
-- 4. Logo will be displayed in:
--    - Company navbar
--    - Browse jobs page
--    - Job details page
--    - Company profile
--    - All applicant/job views

-- Logo requirements:
-- - Supported formats: JPG, PNG, GIF
-- - Maximum size: 2MB
-- - Recommended dimensions: 200x100 pixels
-- - Stored in: uploads/company_logos/

-- Note: Ensure the uploads/company_logos/ directory exists and has write permissions
-- chmod 777 uploads/company_logos/ (on Linux/Unix)

COMMIT;
