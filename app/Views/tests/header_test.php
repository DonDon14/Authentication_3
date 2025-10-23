<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Components Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: var(--bg-primary, #f8f9fa);
            min-height: 100vh;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="test-container">
        <!-- Header Test Section -->
        <header class="header">
            <?= $this->include('partials/header_components') ?>
        </header>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="<?= base_url('js/header-components.js') ?>"></script>
</body>
</html>