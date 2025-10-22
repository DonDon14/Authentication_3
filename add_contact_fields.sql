-- Add contact_number and email_address columns to payments table
ALTER TABLE payments 
ADD COLUMN contact_number VARCHAR(20) NULL COMMENT 'Contact number of the person making payment' AFTER student_name,
ADD COLUMN email_address VARCHAR(255) NULL COMMENT 'Email address for receipts and notifications' AFTER contact_number;

-- Show the updated table structure
DESCRIBE payments;