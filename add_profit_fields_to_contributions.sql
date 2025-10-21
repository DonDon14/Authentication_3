-- Add profit tracking fields to contributions table
ALTER TABLE contributions 
ADD COLUMN cost_price DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cost/expense for this contribution',
ADD COLUMN profit_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Calculated profit (amount - cost_price)',
ADD COLUMN profit_margin DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Profit margin percentage',
ADD COLUMN profit_calculated_at TIMESTAMP NULL COMMENT 'When profit was last calculated';

-- Update existing records to have 0 cost and calculate profit
UPDATE contributions 
SET cost_price = 0.00, 
    profit_amount = amount, 
    profit_margin = 100.00,
    profit_calculated_at = NOW() 
WHERE cost_price IS NULL;