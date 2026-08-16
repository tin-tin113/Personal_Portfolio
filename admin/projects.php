<?php
/**
 * Admin Projects Management List & Delete Handler
 */
$pageTitle = 'Projects Showcase Manager';
$activePage = 'projects';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        // Fetch project to delete main image & additional screenshots
        $stmt = $pdo->prepare("SELECT project_image FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if ($project) {
            if (!empty($project['project_image'])) {
                delete_uploaded_file($project['project_image']);
            }

            // Fetch extra images
            $imgStmt = $pdo->prepare("SELECT image_path FROM project_images WHERE project_id = ?");
            $imgStmt->execute([$id]);
            $images = $imgStmt->fetchAll();
            foreach ($images as $img) {
                delete_uploaded_file($img['image_path']);
            }

            // Delete project record
            $delStmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $delStmt->execute([$id]);

            set_flash('success', 'Project deleted successfully.');
        }
    }
    redirect(BASE_URL . '/admin/projects.php');
}

// Handle Toggle Published Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_publish') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE projects SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('info', 'Project visibility toggled.');
    }
    redirect(BASE_URL . '/admin/projects.php');
}

// Fetch All Projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Projects Library</h4>
    <p class="text-muted small m-0">Manage project showcases, tech badges, links, and public visibility.</p>
  </div>
  <div>
    <a href="<?= BASE_URL ?>/admin/project-edit.php" class="btn btn-primary fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
      <i class="bi bi-plus-lg me-1"></i> Add New Project
    </a>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm style-search" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search projects...">
    </div>
    <span class="text-muted small">Total: <strong><?= count($projects) ?></strong> projects</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:70px;">Order</th>
            <th>Project Title</th>
            <th>Technologies</th>
            <th>Year</th>
            <th>Featured</th>
            <th>Visibility</th>
            <th class="text-end" style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($projects)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No projects found. Click "Add New Project" to create one.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($projects as $p): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$p['display_order'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($p['project_image'])): ?>
                      <img src="<?= BASE_URL . '/' . e($p['project_image']) ?>" class="rounded border" style="width:45px; height:35px; object-fit:cover;" alt="Thumbnail">
                    <?php else: ?>
                      <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width:45px; height:35px;"><i class="bi bi-image"></i></div>
                    <?php endif; ?>
                    <div>
                      <div class="fw-bold text-dark mb-0"><?= e($p['title']) ?></div>
                      <div class="text-muted extra-small text-truncate" style="max-width:250px;"><?= e($p['short_description']) ?></div>
                    </div>
                  </div>
                </td>
                <td class="small text-muted"><?= e($p['technologies_used']) ?></td>
                <td class="small fw-semibold"><?= e($p['project_year']) ?></td>
                <td>
                  <?php if ($p['is_featured']): ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Featured</span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <form action="" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_publish">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle visibility">
                      <?php if ($p['is_published']): ?>
                        <span class="badge-published"><i class="bi bi-eye me-1"></i> Published</span>
                      <?php else: ?>
                        <span class="badge-hidden"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                      <?php endif; ?>
                    </button>
                  </form>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>/project.php?id=<?= $p['id'] ?>" target="_blank" class="btn btn-outline-secondary" title="View Public Page">
                      <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/project-edit.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $p['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($p['title']) ?>" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
