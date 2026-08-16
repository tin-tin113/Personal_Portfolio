<?php
/**
 * Public Detailed Project View
 */
require_once __DIR__ . '/config/app.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';

$pdo = db();

$id   = (int)($_GET['id'] ?? 0);
$slug = sanitize_input($_GET['slug'] ?? '');

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
} elseif (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
} else {
    redirect(BASE_URL . '/projects.php');
}

$project = $stmt->fetch();

// Check if project exists and is published (or allow admin preview)
if (!$project || (!$project['is_published'] && !is_logged_in())) {
    http_response_code(404);
    require_once __DIR__ . '/error/404.php';
    exit;
}

// Fetch Gallery Screenshots
$imgStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
$imgStmt->execute([$project['id']]);
$gallery = $imgStmt->fetchAll();

$customPageTitle = $project['title'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="section-padding bg-card border-bottom" style="background-color:var(--bg-card); padding-top:3.5rem; padding-bottom:3rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb extra-small">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projects.php">Projects</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($project['title']) ?></li>
      </ol>
    </nav>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <span class="badge bg-light text-teal border mb-2" style="color:var(--primary-color);">
          <?= e($project['project_year']) ?> &bull; <?= e($project['my_role'] ?: 'Full-Stack Developer') ?>
        </span>
        <h1 class="hero-title mb-2" style="font-size:2.5rem;"><?= e($project['title']) ?></h1>
        <p class="hero-subtitle mb-0"><?= e($project['short_description']) ?></p>
      </div>

      <div class="d-flex flex-wrap gap-2 w-100 w-sm-auto mt-2 mt-md-0">
        <?php if (!empty($project['live_demo_url'])): ?>
          <a href="<?= e($project['live_demo_url']) ?>" target="_blank" class="btn btn-primary-custom btn-sm-custom">
            <i class="bi bi-box-arrow-up-right me-1"></i> Live Demo
          </a>
        <?php endif; ?>

        <?php if (!empty($project['github_url'])): ?>
          <a href="<?= e($project['github_url']) ?>" target="_blank" class="btn btn-outline-custom btn-sm-custom">
            <i class="bi bi-github me-1"></i> GitHub Repo
          </a>
        <?php endif; ?>

        <?php if (!empty($project['documentation_url'])): ?>
          <a href="<?= e($project['documentation_url']) ?>" target="_blank" class="btn btn-outline-custom btn-sm-custom">
            <i class="bi bi-file-earmark-text me-1"></i> Docs
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-5">

      <!-- Main Content Column -->
      <div class="col-12 col-lg-8">
        <!-- Main Cover Image -->
        <?php if (!empty($project['project_image'])): ?>
          <div class="mb-4 rounded shadow-sm overflow-hidden border reveal-fade-up">
            <img src="<?= BASE_URL . '/' . e($project['project_image']) ?>" class="img-fluid w-100" style="max-height:420px; object-fit:cover;" alt="<?= e($project['title']) ?>">
          </div>
        <?php endif; ?>

        <!-- Detailed Overview -->
        <?php if (!empty($project['detailed_description'])): ?>
          <div class="card-custom p-4 mb-4 reveal-fade-up">
            <h4 class="fw-bold mb-3">Project Overview</h4>
            <div class="lh-lg text-muted">
              <?= nl2br(e($project['detailed_description'])) ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Key Features -->
        <?php if (!empty($project['key_features'])): ?>
          <div class="card-custom p-4 mb-4 reveal-fade-up">
            <h4 class="fw-bold mb-3">Key Features &amp; Functionality</h4>
            <div class="lh-lg text-muted">
              <?= nl2br(e($project['key_features'])) ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Challenges & Solutions -->
        <?php if (!empty($project['challenges'])): ?>
          <div class="card-custom p-4 mb-4 reveal-fade-up">
            <h4 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i> Technical Challenges &amp; Solutions</h4>
            <div class="lh-lg text-muted">
              <?= nl2br(e($project['challenges'])) ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Screenshot Gallery -->
        <?php if (!empty($gallery)): ?>
          <div class="card-custom p-4 mb-4 reveal-fade-up">
            <h4 class="fw-bold mb-3">Project Screenshots Gallery</h4>
            <div class="row g-3">
              <?php foreach ($gallery as $g): ?>
                <div class="col-12 col-md-6">
                  <a href="<?= BASE_URL . '/' . e($g['image_path']) ?>" target="_blank">
                    <img src="<?= BASE_URL . '/' . e($g['image_path']) ?>" class="img-fluid rounded border shadow-sm" alt="Screenshot" loading="lazy">
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- Sidebar Specs Column -->
      <div class="col-12 col-lg-4 reveal-fade-up">
        <div class="card-custom p-4 sticky-top" style="top:90px;">
          <h5 class="fw-bold mb-3 border-bottom pb-2">Project Metadata</h5>

          <div class="mb-3">
            <div class="extra-small text-muted fw-bold text-uppercase">Role</div>
            <div class="fw-semibold text-dark"><?= e($project['my_role'] ?: 'Full-Stack Developer') ?></div>
          </div>

          <div class="mb-3">
            <div class="extra-small text-muted fw-bold text-uppercase">Year</div>
            <div class="fw-semibold text-dark"><?= e($project['project_year']) ?></div>
          </div>

          <?php if (!empty($project['development_methodology'])): ?>
            <div class="mb-3">
              <div class="extra-small text-muted fw-bold text-uppercase">Methodology / AI Workflow</div>
              <div class="fw-semibold text-dark"><?= e($project['development_methodology']) ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($project['technologies_used'])): ?>
            <div class="mb-3">
              <div class="extra-small text-muted fw-bold text-uppercase mb-2">Technologies Used</div>
              <div class="d-flex flex-wrap gap-1">
                <?php foreach (explode(',', $project['technologies_used']) as $t): ?>
                  <span class="tech-badge"><?= e(trim($t)) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <hr>

          <a href="<?= BASE_URL ?>/projects.php" class="btn btn-sm btn-outline-secondary w-100">
            &larr; Back to All Projects
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
