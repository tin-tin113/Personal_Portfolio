<?php
/**
 * Admin CMS Dashboard
 */
$pageTitle = 'Dashboard Overview';
$activePage = 'dashboard';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

// Gather System Statistics
$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'published_projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE is_published = 1")->fetchColumn(),
    'hidden_projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE is_published = 0")->fetchColumn(),
    'certifications' => $pdo->query("SELECT COUNT(*) FROM certifications")->fetchColumn(),
    'licenses' => $pdo->query("SELECT COUNT(*) FROM licenses")->fetchColumn(),
    'skills' => $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
    'experience' => $pdo->query("SELECT COUNT(*) FROM experience")->fetchColumn(),
    'education' => $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn(),
    'media' => $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn(),
];

// Fetch Recent Projects
$recentProjects = $pdo->query("SELECT id, title, is_published, created_at FROM projects ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch Recent Media
$recentMedia = $pdo->query("SELECT id, original_name, stored_filename, file_type, created_at FROM media ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- Quick Actions Toolbar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Welcome back, <?= e($user['username']) ?>!</h4>
    <p class="text-muted small m-0">Here is the quick snapshot of your portfolio CMS status.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/admin/project-edit.php" class="btn btn-sm btn-primary" style="background-color:#0f766e; border-color:#0f766e;">
      <i class="bi bi-plus-lg me-1"></i> Add Project
    </a>
    <a href="<?= BASE_URL ?>/admin/certification-edit.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-patch-check me-1"></i> Add Cert
    </a>
    <a href="<?= BASE_URL ?>/admin/profile.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-person-gear me-1"></i> Edit Profile
    </a>
  </div>
</div>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="stat-card">
      <div>
        <div class="stat-number"><?= $stats['projects'] ?></div>
        <div class="stat-label">Total Projects (<?= $stats['published_projects'] ?> pub / <?= $stats['hidden_projects'] ?> hid)</div>
      </div>
      <div class="stat-icon-wrap">
        <i class="bi bi-folder-fill"></i>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="stat-card">
      <div>
        <div class="stat-number"><?= $stats['certifications'] + $stats['licenses'] ?></div>
        <div class="stat-label">Certs &amp; Credentials</div>
      </div>
      <div class="stat-icon-wrap" style="background:#eff6ff; color:#2563eb;">
        <i class="bi bi-patch-check-fill"></i>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="stat-card">
      <div>
        <div class="stat-number"><?= $stats['skills'] ?></div>
        <div class="stat-label">Skills &amp; Technologies</div>
      </div>
      <div class="stat-icon-wrap" style="background:#fef3c7; color:#d97706;">
        <i class="bi bi-cpu-fill"></i>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="stat-card">
      <div>
        <div class="stat-number"><?= $stats['experience'] ?></div>
        <div class="stat-label">Work Experience Items</div>
      </div>
      <div class="stat-icon-wrap" style="background:#f3e8ff; color:#9333ea;">
        <i class="bi bi-briefcase-fill"></i>
      </div>
    </div>
  </div>
</div>

<!-- Dashboard Content Tables -->
<div class="row g-4">
  <!-- Recent Projects -->
  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
        <h6 class="m-0 fw-bold"><i class="bi bi-folder me-2 text-teal"></i> Recent Projects</h6>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-sm btn-link text-decoration-none">View All &rarr;</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle m-0">
            <thead class="table-light">
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentProjects)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">No projects created yet.</td></tr>
              <?php else: ?>
                <?php foreach ($recentProjects as $p): ?>
                  <tr>
                    <td class="fw-semibold"><?= e($p['title']) ?></td>
                    <td>
                      <?php if ($p['is_published']): ?>
                        <span class="badge-published"><i class="bi bi-eye me-1"></i> Published</span>
                      <?php else: ?>
                        <span class="badge-hidden"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= format_date($p['created_at'], 'M d, Y') ?></td>
                    <td class="text-end">
                      <a href="<?= BASE_URL ?>/admin/project-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Media Uploads -->
  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
        <h6 class="m-0 fw-bold"><i class="bi bi-images me-2 text-teal"></i> Recent Media Assets</h6>
        <a href="<?= BASE_URL ?>/admin/media.php" class="btn btn-sm btn-link text-decoration-none">Manage Library &rarr;</a>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <?php if (empty($recentMedia)): ?>
            <div class="p-3 text-center text-muted">No media uploads recorded yet.</div>
          <?php else: ?>
            <?php foreach ($recentMedia as $m): ?>
              <div class="list-group-item d-flex align-items-center justify-content-between py-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                  <i class="bi bi-file-earmark-code fs-4 text-secondary"></i>
                  <div class="text-truncate">
                    <div class="fw-semibold small text-truncate"><?= e($m['original_name']) ?></div>
                    <div class="text-muted extra-small"><?= e($m['file_type']) ?> &bull; <?= format_date($m['created_at'], 'M d, H:i') ?></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
