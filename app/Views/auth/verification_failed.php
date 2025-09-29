<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification Failed</title>
  <link rel="stylesheet" href="<?= base_url('css/verification_failed.css') ?>">
</head>
<body>
  <div class="error-container">
    <div class="error-icon">❌</div>
    <h1>Verification Failed</h1>
    <p><?= esc($message) ?></p>
    <p>This could happen if:</p>
    <ul style="text-align: left; color: #666; margin: 20px 0;">
      <li>The verification link has expired (links are valid for 24 hours)</li>
      <li>The link has already been used</li>
      <li>The link is malformed or corrupted</li>
    </ul>
    <div class="btn-group">
      <a href="/register" class="btn btn-primary">Register Again</a>
      <a href="/" class="btn btn-secondary">Back to Login</a>
    </div>
  </div>
</body>
</html>