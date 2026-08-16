<?php
/**
 * Admin CMS Login Handler
 */
require_once dirname(__DIR__) . '/config/app.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/csrf.php';
require_once INCLUDES_PATH . '/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(BASE_URL . '/admin/index.php');
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $identifier = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = attempt_login($identifier, $password);

    if ($result['success']) {
        set_flash('success', 'Welcome back to Admin CMS!');
        redirect(BASE_URL . '/admin/index.php');
    } else {
        $errorMessage = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Portfolio CMS</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #0f172a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }
    .login-card {
      width: 100%;
      max-width: 420px;
      background: #ffffff;
      border-radius: 0.75rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
      padding: 2.5rem;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center bg-light text-teal rounded-circle p-3 mb-2" style="width:60px; height:60px; color:#0f766e;">
        <i class="bi bi-shield-lock-fill fs-2"></i>
      </div>
      <h4 class="fw-bold mb-1">Admin CMS Login</h4>
      <p class="text-muted small">Enter your admin credentials to continue</p>
    </div>

    <?php if ($errorMessage): ?>
      <div class="alert alert-danger alert-dismissible fade show text-start small" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($errorMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php render_flash_messages(); ?>

    <form action="" method="POST">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label for="username" class="form-label fw-semibold small">Username or Email</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="admin or email">
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label fw-semibold small">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
          <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
        Sign In <i class="bi bi-arrow-right ms-1"></i>
      </button>
    </form>

    <div class="mt-4 text-center border-top pt-3">
      <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-muted small">
        <i class="bi bi-house me-1"></i> Return to Public Portfolio
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/bootstrap.bundle.min.js"></script>
</body>
</html>
