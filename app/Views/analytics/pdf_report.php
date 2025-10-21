<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics Report - ClearPay</title>
  <style>
    @media print {
      body { margin: 0; }
      .no-print { display: none; }
      .page-break { page-break-before: always; }
    }

    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      margin: 0;
      padding: 20px;
      background: white;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      border-bottom: 3px solid #3b82f6;
      padding-bottom: 20px;
    }

    .header h1 {
      margin: 0;
      color: #3b82f6;
      font-size: 28px;
      font-weight: bold;
    }

    .header p {
      margin: 5px 0 0 0;
      color: #666;
      font-size: 14px;
    }

    .company-info {
      text-align: center;
      margin-bottom: 10px;
    }

    .section {
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 18px;
      font-weight: bold;
      color: #3b82f6;
      margin-bottom: 15px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 8px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 25px;
    }

    .stat-card {
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      background: #f9fafb;
    }

    .stat-label {
      font-size: 12px;
      color: #666;
      margin-bottom: 8px;
      font-weight: 600;
    }

    .stat-value {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      margin-bottom: 8px;
    }

    .stat-change {
      font-size: 11px;
      color: #10b981;
      font-weight: 600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      border: 1px solid #e5e7eb;
    }

    th, td {
      border: 1px solid #e5e7eb;
      padding: 12px 8px;
      text-align: left;
    }

    th {
      background-color: #f3f4f6;
      font-weight: bold;
      font-size: 12px;
      color: #374151;
    }

    td {
      font-size: 11px;
      color: #4b5563;
    }

    .two-column {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
    }

    .footer {
      margin-top: 40px;
      text-align: center;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
      color: #666;
      font-size: 11px;
    }

    .profit-high { color: #10b981; font-weight: bold; }
    .profit-medium { color: #f59e0b; font-weight: bold; }
    .profit-low { color: #ef4444; font-weight: bold; }

    .summary-table {
      background: #f9fafb;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #e5e7eb;
    }

    .summary-row:last-child {
      border-bottom: none;
    }

    .summary-label {
      font-weight: 600;
      color: #374151;
    }

    .summary-value {
      font-weight: bold;
      color: #111827;
    }

    .print-controls {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 1000;
    }

    .btn {
      background: #3b82f6;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      margin: 0 5px;
      font-size: 12px;
    }

    .btn:hover {
      background: #2563eb;
    }

    .btn-secondary {
      background: #6b7280;
    }

    .btn-secondary:hover {
      background: #4b5563;
    }
  </style>
</head>
<body>
  <div class="print-controls no-print">
    <button class="btn" onclick="window.print()">Print Report</button>
    <button class="btn btn-secondary" onclick="window.close()">Close</button>
  </div>

  <div class="header">
    <div class="company-info">
      <h1>📊 ClearPay Analytics Report</h1>
      <p>Comprehensive Payment System Analysis</p>
      <p><strong>Generated on:</strong> <?= date('F j, Y \a\t g:i A') ?></p>
    </div>
  </div>

  <div class="section">
    <h2 class="section-title">📈 Executive Summary</h2>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">₱<?= number_format($overview['total_revenue'], 2) ?></div>
        <div class="stat-change">+<?= $overview['monthly_growth'] ?>% this month</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Profit</div>
        <div class="stat-value">₱<?= number_format($overview['total_profit'], 2) ?></div>
        <div class="stat-change"><?= $overview['avg_profit_margin'] ?>% avg margin</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Active Contributors</div>
        <div class="stat-value"><?= number_format($overview['active_contributors']) ?></div>
        <div class="stat-change">Growing steadily</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Contributions</div>
        <div class="stat-value"><?= number_format($overview['total_contributions']) ?></div>
        <div class="stat-change">Active campaigns</div>
      </div>
    </div>

    <div class="summary-table">
      <h3 style="margin-top: 0; color: #374151;">Key Performance Indicators</h3>
      <div class="summary-row">
        <span class="summary-label">Monthly Revenue:</span>
        <span class="summary-value">₱<?= number_format($overview['monthly_revenue'], 2) ?></span>
      </div>
      <div class="summary-row">
        <span class="summary-label">Average Transaction:</span>
        <span class="summary-value">₱<?= number_format($payments['avg_transaction'], 2) ?></span>
      </div>
      <div class="summary-row">
        <span class="summary-label">Monthly Growth Rate:</span>
        <span class="summary-value"><?= $overview['monthly_growth'] ?>%</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">Payment Methods Available:</span>
        <span class="summary-value"><?= count($payments['by_method']) ?> types</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">Active Categories:</span>
        <span class="summary-value"><?= count($contributions['by_category']) ?> categories</span>
      </div>
    </div>
  </div>

  <div class="two-column">
    <div class="section">
      <h2 class="section-title">🏆 Top Profitable Contributions</h2>
      <table>
        <thead>
          <tr>
            <th>Contribution</th>
            <th>Revenue</th>
            <th>Profit</th>
            <th>Margin</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($contributions['top_profitable'], 0, 10) as $contribution): ?>
          <tr>
            <td><?= esc($contribution['title']) ?></td>
            <td>₱<?= number_format($contribution['amount'], 2) ?></td>
            <td>₱<?= number_format($contribution['profit_amount'], 2) ?></td>
            <td class="<?= $contribution['profit_margin'] >= 30 ? 'profit-high' : ($contribution['profit_margin'] >= 15 ? 'profit-medium' : 'profit-low') ?>">
              <?= number_format($contribution['profit_margin'], 1) ?>%
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="section">
      <h2 class="section-title">💳 Recent Payments</h2>
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Contribution</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($payments['recent_payments'], 0, 10) as $payment): ?>
          <tr>
            <td><?= esc($payment['student_name']) ?></td>
            <td><?= esc($payment['contribution_title']) ?></td>
            <td>₱<?= number_format($payment['amount'], 2) ?></td>
            <td><?= date('M j, Y', strtotime($payment['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="section page-break">
    <h2 class="section-title">📊 Performance by Category</h2>
    <table>
      <thead>
        <tr>
          <th>Category</th>
          <th>Contributions</th>
          <th>Total Revenue</th>
          <th>Total Profit</th>
          <th>Average Margin</th>
          <th>Performance Rating</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contributions['by_category'] as $category): ?>
        <?php 
          $avgMargin = $category['total_amount'] > 0 ? ($category['total_profit'] / $category['total_amount'] * 100) : 0;
          $rating = $avgMargin >= 30 ? 'Excellent' : ($avgMargin >= 15 ? 'Good' : 'Needs Improvement');
        ?>
        <tr>
          <td><?= esc(ucfirst($category['category'])) ?></td>
          <td><?= number_format($category['count']) ?></td>
          <td>₱<?= number_format($category['total_amount'], 2) ?></td>
          <td>₱<?= number_format($category['total_profit'], 2) ?></td>
          <td class="<?= $avgMargin >= 30 ? 'profit-high' : ($avgMargin >= 15 ? 'profit-medium' : 'profit-low') ?>">
            <?= number_format($avgMargin, 1) ?>%
          </td>
          <td><?= $rating ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h2 class="section-title">💰 Payment Methods Analysis</h2>
    <table>
      <thead>
        <tr>
          <th>Payment Method</th>
          <th>Transaction Count</th>
          <th>Total Amount</th>
          <th>Percentage of Total</th>
          <th>Average Transaction</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $totalTransactions = array_sum(array_column($payments['by_method'], 'count'));
        $totalAmount = array_sum(array_column($payments['by_method'], 'total_amount'));
        foreach ($payments['by_method'] as $method): 
        ?>
        <tr>
          <td><?= esc(ucfirst($method['payment_method'])) ?></td>
          <td><?= number_format($method['count']) ?></td>
          <td>₱<?= number_format($method['total_amount'], 2) ?></td>
          <td><?= $totalTransactions > 0 ? round(($method['count'] / $totalTransactions) * 100, 1) : 0 ?>%</td>
          <td>₱<?= $method['count'] > 0 ? number_format($method['total_amount'] / $method['count'], 2) : '0.00' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p><strong>Report Details</strong></p>
    <p>📅 Generated: <?= $generated_at ?> | 🖥️ System: ClearPay Analytics v1.0</p>
    <p>This report contains confidential information. Handle with appropriate security measures.</p>
    <p>For questions about this report, contact your system administrator.</p>
  </div>

  <script>
    // Auto-focus for better print experience
    window.onload = function() {
      document.title = 'ClearPay Analytics Report - ' + new Date().toLocaleDateString();
    };
  </script>
</body>
</html>