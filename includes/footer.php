<?php
/**
 * Public Footer Component
 */
$socialLinks = db()->query("SELECT * FROM external_links WHERE is_active = 1 AND category = 'Social' ORDER BY display_order ASC")->fetchAll();
$footerText  = site_setting('footer_text', 'Building practical, reliable web solutions with modern PHP, database design, and AI workflows.');
$copyright   = site_setting('copyright_text', '© ' . date('Y') . ' ' . e($profile['full_name']) . '. All rights reserved.');
?>
<footer class="footer-custom">
  <div class="container">
    <div class="row g-4 align-items-center mb-4">
      <div class="col-12 col-md-6 text-center text-md-start">
        <h5 class="fw-bold mb-1"><?= e($profile['full_name']) ?></h5>
        <p class="small text-muted mb-0"><?= e($footerText) ?></p>
      </div>

      <div class="col-12 col-md-6 text-center text-md-end">
        <div class="d-flex justify-content-center justify-content-md-end gap-3 fs-5">
          <?php foreach ($socialLinks as $link): ?>
            <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted" title="<?= e($link['name']) ?>">
              <i class="<?= e($link['icon'] ?: 'bi bi-link-45deg') ?>"></i>
            </a>
          <?php endforeach; ?>
          <a href="<?= BASE_URL ?>/admin/login.php" class="text-muted opacity-50 ms-2" title="Admin CMS Portal">
            <i class="bi bi-shield-lock"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="border-top pt-3 text-center extra-small text-muted">
      <?= e($copyright) ?>
    </div>
  </div>
</footer>

<!-- Floating Back to Top Button -->
<button id="back-to-top-btn" class="back-to-top-btn" aria-label="Back to Top" title="Back to top">
  <i class="bi bi-arrow-up-short fs-4"></i>
</button>

<!-- Interactive Toast Feedback -->
<div id="copy-toast" class="copy-tooltip-toast" role="status" aria-live="polite"></div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/bootstrap.bundle.min.js"></script>
<!-- Public Custom JS -->
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= filemtime(ROOT_PATH . '/assets/js/main.js') ?>"></script>
</body>
</html>
