<?php
/**
 * 404 Page Not Found Error Page
 */
$customPageTitle = '404 Page Not Found';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<section class="section-padding text-center">
  <div class="container my-5">
    <div class="display-1 fw-bold text-teal" style="color:var(--primary-color);">404</div>
    <h2 class="fw-bold mb-3">Page Not Found</h2>
    <p class="text-muted max-w-md mx-auto mb-4">
      The portfolio page, project detail, or resource you are looking for does not exist or has been removed.
    </p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary-custom">
      <i class="bi bi-house me-1"></i> Return to Homepage
    </a>
  </div>
</section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
