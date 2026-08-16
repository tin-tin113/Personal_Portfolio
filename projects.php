<?php
/**
 * Public Projects Showcase Gallery (Interactive & Responsive)
 */
$customPageTitle = 'Projects Portfolio';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = db();

// Fetch All Published Projects
$projects = $pdo->query("
    SELECT * FROM projects
    WHERE is_published = 1
    ORDER BY display_order ASC, id DESC
")->fetchAll();
?>

<section class="section-padding bg-card border-bottom" style="background-color:var(--bg-card); padding-top:4.5rem; padding-bottom:3.5rem;">
  <div class="container text-center reveal-fade-up">
    <h1 class="hero-title mb-2">Projects Showcase</h1>
    <p class="hero-subtitle mx-auto mb-0" style="max-width:680px;">
      Explore custom web applications, database architectures, testing suites, and AI-accelerated development projects.
    </p>
  </div>
</section>

<section class="section-padding">
  <div class="container">

    <!-- Search & Filter Controls -->
    <div class="row justify-content-center mb-4 reveal-fade-up">
      <div class="col-12 col-md-6 col-lg-5 mb-3">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" id="project-search-input" class="form-control border-start-0" placeholder="Filter by technology, keyword, or title...">
        </div>
      </div>
    </div>

    <!-- Technology Quick Filter Buttons -->
    <div class="filter-nav-wrap reveal-fade-up">
      <button class="filter-btn project-filter-btn active" data-filter="all">All Projects</button>
      <button class="filter-btn project-filter-btn" data-filter="PHP">PHP</button>
      <button class="filter-btn project-filter-btn" data-filter="MySQL">MySQL</button>
      <button class="filter-btn project-filter-btn" data-filter="Bootstrap">Bootstrap</button>
      <button class="filter-btn project-filter-btn" data-filter="QA">QA / Testing</button>
    </div>

    <!-- Projects Grid -->
    <div class="row g-4" id="projects-grid">
      <?php if (empty($projects)): ?>
        <div class="col-12 text-center text-muted py-5">No projects published yet.</div>
      <?php else: ?>
        <?php foreach ($projects as $p): ?>
          <div class="col-12 col-md-6 col-lg-4 project-item reveal-fade-up" data-tech="<?= e($p['technologies_used']) ?>">
            <div class="card-custom d-flex flex-column h-100">
              <div class="project-card-img-wrap">
                <?php if (!empty($p['project_image'])): ?>
                  <img src="<?= BASE_URL . '/' . e($p['project_image']) ?>" class="project-card-img" alt="<?= e($p['title']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fs-2">
                    <i class="bi bi-folder2-open"></i>
                  </div>
                <?php endif; ?>
                <?php if ($p['is_featured']): ?>
                  <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark shadow-sm fw-bold">
                    <i class="bi bi-star-fill me-1"></i> Featured
                  </span>
                <?php endif; ?>
              </div>

              <div class="p-4 d-flex flex-column flex-grow-1">
                <div class="text-muted extra-small mb-1 fw-bold text-uppercase"><?= e($p['project_year']) ?></div>
                <h5 class="fw-bold mb-2 text-dark"><?= e($p['title']) ?></h5>
                <p class="small text-muted mb-3 flex-grow-1"><?= e($p['short_description']) ?></p>

                <?php if (!empty($p['technologies_used'])): ?>
                  <div class="d-flex flex-wrap gap-1 mb-3">
                    <?php 
                    $techs = explode(',', $p['technologies_used']);
                    foreach (array_slice($techs, 0, 4) as $t): 
                    ?>
                      <span class="tech-badge"><?= e(trim($t)) ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                  <a href="<?= BASE_URL ?>/project.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-custom btn-card-action fw-semibold">
                    View Details &rarr;
                  </a>
                  <div class="d-flex gap-2">
                    <?php if (!empty($p['github_url'])): ?>
                      <a href="<?= e($p['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted fs-5" title="GitHub Repo"><i class="bi bi-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($p['live_demo_url'])): ?>
                      <a href="<?= e($p['live_demo_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted fs-5" title="Live Demo"><i class="bi bi-box-arrow-up-right"></i></a>
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
