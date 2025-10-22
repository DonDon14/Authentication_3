<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Dashboard' ?> | ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Additional styles specific to this page -->
  <?= $this->renderSection('styles') ?>
</head>
<body>
  <!-- Main App Container -->
  <div class="app-container">
    
    <!-- Sidebar Navigation -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Header Bar -->
      <?= $this->include('partials/header', [
        'pageTitle' => $pageTitle ?? 'Dashboard',
        'pageSubtitle' => $pageSubtitle ?? 'Welcome back! Here\'s your overview.',
        'name' => $name ?? '',
        'email' => $email ?? '',
        'profilePictureUrl' => $profilePictureUrl ?? ''
      ]) ?>

      <!-- Main Content -->
      <div class="dashboard-content">
        <?= $this->renderSection('content') ?>
      </div>
    </main>
  </div>

  <!-- Additional sections for modals, dropdowns, etc. -->
  <?= $this->renderSection('modals') ?>

  <!-- JavaScript Dependencies -->
  <script src="<?= base_url('js/main.js') ?>"></script>
  <?= $this->renderSection('scripts') ?>
  
</body>
</html>