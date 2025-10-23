<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: var(--bg-secondary);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .success-banner {
            background: var(--success-light);
            color: var(--success-color);
            padding: 1rem;
            text-align: center;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
        }

        .actions {
            margin-top: 2rem;
            text-align: center;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--bg-tertiary);
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .success-banner,
            .actions {
                display: none;
            }

            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="success-banner">
        <h2><i class="fas fa-check-circle"></i> Payment Recorded Successfully!</h2>
        <p>The payment has been recorded and a receipt has been generated.</p>
    </div>

    <div class="receipt-container">
        <?= view('partials/payment_receipt', ['payment' => $payment]) ?>
    </div>

    <div class="actions">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i>
            Print Receipt
        </button>
        
        <?php if (isset($qr_download_url)): ?>
        <a href="<?= $qr_download_url ?>" class="btn btn-primary" download>
            <i class="fas fa-download"></i>
            Download QR Code
        </a>
        <?php endif; ?>
        
        <a href="<?= base_url('payments') ?>" class="btn btn-secondary">
            <i class="fas fa-plus"></i>
            Record Another Payment
        </a>
    </div>
</body>
</html>