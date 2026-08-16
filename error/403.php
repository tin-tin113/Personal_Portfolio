<?php
/**
 * 403 Forbidden Error Page
 */
$customPageTitle = '403 Forbidden';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<section class="section-padding text-center">
  <div class="container my-5">
    <div class="display-1 fw-bold text-danger">403</div>
    <h2 class="fw-bold mb-3">Access Forbidden</h2>
    <p class="text-muted max-w-md mx-auto mb-4">
      You do not have permission to access the requested resource or administrative functionality.
    </p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary-custom">
      <i class="bi bi-house me-1"></i> Return to Homepage
    </a>
  </div>
</section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
