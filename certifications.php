<?php
/**
 * Public Certifications & Credentials Showcase
 */
$customPageTitle = 'Certifications & Credentials';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = db();

$certifications = $pdo->query("
    SELECT * FROM certifications
    WHERE is_published = 1
    ORDER BY display_order ASC, id DESC
")->fetchAll();

$licenses = $pdo->query("
    SELECT * FROM licenses
    WHERE is_published = 1
    ORDER BY display_order ASC, id DESC
")->fetchAll();
?>

<section class="section-padding bg-card border-bottom" style="background-color:var(--bg-card); padding-top:4rem; padding-bottom:3rem;">
  <div class="container text-center">
    <h1 class="hero-title mb-2">Certifications &amp; Credentials</h1>
    <p class="hero-subtitle mx-auto mb-0" style="max-width:650px;">
      Verified professional certifications, technical training credentials, and industry licenses.
    </p>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4">
      <?php if (empty($certifications) && empty($licenses)): ?>
        <div class="col-12 text-center text-muted py-5">No certifications published yet.</div>
      <?php else: ?>
        <?php foreach ($certifications as $c): ?>
          <div class="col-12 col-md-6 reveal-fade-up">
            <div class="card-custom p-4 h-100">
              <div class="d-flex align-items-start gap-3">
                <div class="skill-icon-wrap flex-shrink-0"><i class="bi bi-patch-check-fill"></i></div>
                <div class="flex-grow-1">
                  <h5 class="fw-bold mb-1 text-dark"><?= e($c['title']) ?></h5>
                  <div class="text-muted small fw-semibold mb-2"><?= e($c['issuing_organization']) ?></div>
                  <p class="small text-muted mb-3"><?= e($c['description']) ?></p>

                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top extra-small text-muted">
                    <span>Issue Date: <strong><?= format_date($c['issue_date'], 'M Y') ?></strong></span>
                    <?php if (!empty($c['credential_id'])): ?>
                      <span class="font-monospace">ID: <?= e($c['credential_id']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($c['certificate_file']) && !empty($c['credential_url'])): ?>
                      <div class="d-flex gap-2">
                        <a href="<?= BASE_URL . '/' . e($c['certificate_file']) ?>" target="_blank" class="fw-bold text-teal">View File &rarr;</a>
                        <span class="text-muted">&bull;</span>
                        <a href="<?= e($c['credential_url']) ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-teal">Verify Online &rarr;</a>
                      </div>
                    <?php elseif (!empty($c['certificate_file'])): ?>
                      <a href="<?= BASE_URL . '/' . e($c['certificate_file']) ?>" target="_blank" class="fw-bold text-teal">View Certificate File &rarr;</a>
                    <?php elseif (!empty($c['credential_url'])): ?>
                      <a href="<?= e($c['credential_url']) ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-teal">Verify Credential &rarr;</a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php foreach ($licenses as $l): ?>
          <div class="col-12 col-md-6 reveal-fade-up">
            <div class="card-custom p-4 h-100">
              <div class="d-flex align-items-start gap-3">
                <div class="skill-icon-wrap flex-shrink-0" style="background:#eff6ff; color:#2563eb;"><i class="bi bi-award-fill"></i></div>
                <div class="flex-grow-1">
                  <h5 class="fw-bold mb-1 text-dark"><?= e($l['title']) ?></h5>
                  <div class="text-muted small fw-semibold mb-2"><?= e($l['issuing_organization']) ?></div>
                  <p class="small text-muted mb-3"><?= e($l['description']) ?></p>

                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top extra-small text-muted">
                    <span>License #: <?= e($l['license_number'] ?: 'Verified') ?></span>
                    <?php if (!empty($l['document_file'])): ?>
                      <a href="<?= BASE_URL . '/' . e($l['document_file']) ?>" target="_blank" class="fw-semibold">View Document &rarr;</a>
                    <?php elseif (!empty($l['verification_url'])): ?>
                      <a href="<?= e($l['verification_url']) ?>" target="_blank" class="fw-semibold">Verify License &rarr;</a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
