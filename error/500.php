<?php
/**
 * 500 Internal Server Error Page
 */
$customPageTitle = '500 Server Error';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<section class="section-padding text-center">
  <div class="container my-5">
    <div class="display-1 fw-bold text-secondary">500</div>
    <h2 class="fw-bold mb-3">Internal Server Error</h2>
    <p class="text-muted max-w-md mx-auto mb-4">
      An unexpected server error occurred. Please try refreshing the page or try again later.
    </p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary-custom">
      <i class="bi bi-house me-1"></i> Return to Homepage
    </a>
  </div>
</section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
