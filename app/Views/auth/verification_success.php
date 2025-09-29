<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verified Successfully</title>
  <link rel="stylesheet" href="<?= base_url('css/verification_success.css') ?>">
</head>
<body>
  <div class="success-container">
    <div class="success-icon">✅</div>
    <h1>Email Verified Successfully!</h1>
    <p>
      Hello <span class="name-highlight"><?= esc($name) ?></span>!<br>
      Your email address has been successfully verified. 
      You can now log in to your account and access all features.
    </p>
    <a href="/" class="login-btn">Go to Login</a>
  </div>
</body>
</html>