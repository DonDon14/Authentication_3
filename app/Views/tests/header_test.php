<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Components Test</title>
    <!-- Base styles -->
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-white: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background: var(--bg-primary);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .test-header {
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--bg-white);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .test-title {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
        }
        
        .test-description {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.875rem;
        }
        
        .header-container {
            margin-top: 2rem;
            padding: 1rem;
            background: var(--bg-white);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
    
    <!-- Component specific styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/header-components.css') ?>">
</head>
<body>
    <div class="test-container">
        <!-- Test Information -->
        <div class="test-header">
            <h1 class="test-title">Header Components Test Page</h1>
            <p class="test-description">This page tests the header components functionality including dropdowns and notifications.</p>
        </div>
        
        <!-- Header Test Section -->
        <div class="header-container">
            <?php
            // Make sure we have the required variables
            if (!isset($name)) $name = 'John Doe';
            if (!isset($email)) $email = 'john.doe@example.com';
            if (!isset($profilePictureUrl)) $profilePictureUrl = null;
            
            // Include the header components
            echo view('partials/header_components', [
                'name' => $name,
                'email' => $email,
                'profilePictureUrl' => $profilePictureUrl
            ]);
            ?>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="<?= base_url('js/header-components.js') ?>"></script>
    
    <!-- Test page specific JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Test page loaded');
        console.log('Profile name:', <?= json_encode($name) ?>);
        console.log('Profile email:', <?= json_encode($email) ?>);
    });
    </script>
</body>
</html>