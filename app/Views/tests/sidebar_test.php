
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Test</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/header-components.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add test-specific styles */
        .test-content {
            margin-left: 280px; /* Adjust based on your sidebar width */
            padding: 20px;
        }
        .test-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .test-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Include the sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Test content -->
    <div class="test-content">
        <div class="test-card">
            <h2>Sidebar Test Page</h2>
            <p>This is a test view to verify the sidebar component is rendering correctly.</p>
            
            <div class="test-actions" style="margin-top: 20px;">
                <h3>Test Actions:</h3>
                <ul>
                    <li>Click on different navigation items to test routing</li>
                    <li>Test the sidebar collapse functionality</li>
                    <li>Verify icons and labels are correctly aligned</li>
                    <li>Check responsive behavior on mobile devices</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript files -->
    <script src="<?= base_url('js/main.js') ?>"></script>
    <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>