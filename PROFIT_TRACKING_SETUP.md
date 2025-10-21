# Profit Tracking Feature Setup Instructions

## Database Migration Required

Before using the new profit tracking feature, you need to run the SQL migration to add the required database fields.

### Steps:

1. **Run the SQL Migration:**
   - Open your MySQL/phpMyAdmin interface
   - Execute the SQL file: `add_profit_fields_to_contributions.sql`
   - This will add the following fields to the `contributions` table:
     - `cost_price` (DECIMAL) - Cost/expense for the contribution
     - `profit_amount` (DECIMAL) - Calculated profit (amount - cost_price)
     - `profit_margin` (DECIMAL) - Profit margin percentage
     - `profit_calculated_at` (TIMESTAMP) - When profit was last calculated

2. **Verify Installation:**
   - Go to Contributions page: `/contributions`
   - Click "Add Contribution" - you should see the new "Cost Price" field
   - Add a contribution with both amount and cost price
   - You should see profit calculations displayed in real-time

3. **Access Analytics:**
   - In the Contributions page, click the "Profit Analytics" button
   - View comprehensive profit analysis and top performing contributions

## Features Added:

### 1. **Enhanced Contribution Form**
- New "Cost Price" field for entering expenses
- Real-time profit calculation display
- Color-coded profit/loss indicators

### 2. **Profit Display in Contribution List**
- Profit margin percentage shown for each contribution
- Color-coded for quick visual assessment

### 3. **Comprehensive Analytics Page**
- Total profit, revenue, and cost summary
- Average profit margin across all contributions
- Top profitable contributions ranking
- Complete profitability breakdown table

### 4. **Automatic Calculations**
- Profit Amount = Revenue - Cost Price
- Profit Margin % = (Profit Amount / Revenue) × 100
- All calculations handled automatically

## Usage Examples:

**Example 1 - Profitable Contribution:**
- Title: "School Uniform"
- Amount: $50.00
- Cost Price: $30.00
- **Result: $20.00 profit (40% margin)**

**Example 2 - Break-even:**
- Title: "Transportation"
- Amount: $25.00
- Cost Price: $25.00
- **Result: $0.00 profit (0% margin)**

**Example 3 - Loss (if applicable):**
- Title: "Subsidized Meal"
- Amount: $15.00
- Cost Price: $20.00
- **Result: -$5.00 loss (-33.3% margin)**

This feature enables comprehensive profit tracking and analytics for better financial decision-making.