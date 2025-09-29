-- Add QR receipt columns to payments table
ALTER TABLE payments 
ADD COLUMN qr_receipt_path VARCHAR(255) NULL AFTER receipt_number,
ADD COLUMN verification_code VARCHAR(100) NULL AFTER qr_receipt_path;