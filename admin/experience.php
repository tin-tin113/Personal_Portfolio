<?php
/**
 * Admin Work Experience Manager List
 */
$pageTitle = 'Work Experience Manager';
$activePage = 'experience';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $del = $pdo->prepare("DELETE FROM experience WHERE id = ?");
        $del->execute([$id]);
        set_flash('success', 'Experience record deleted.');
    }
    redirect(BASE_URL . '/admin/experience.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_publish') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE experience SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('info', 'Experience visibility toggled.');
    }
    redirect(BASE_URL . '/admin/experience.php');
}

$experiences = $pdo->query("SELECT * FROM experience ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Work Experience Timeline</h4>
    <p class="text-muted small m-0">Manage employment history, roles, responsibilities, and achievements.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/experience-edit.php" class="btn btn-primary fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
    <i class="bi bi-plus-lg me-1"></i> Add Experience
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search experience...">
    </div>
    <span class="text-muted small">Total: <strong><?= count($experiences) ?></strong> items</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:60px;">Order</th>
            <th>Position &amp; Organization</th>
            <th>Employment Type</th>
            <th>Period</th>
            <th>Status</th>
            <th class="text-end" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($experiences)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No experience records added yet.</td></tr>
          <?php else: ?>
            <?php foreach ($experiences as $exp): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$exp['display_order'] ?></td>
                <td>
                  <div class="fw-bold text-dark"><?= e($exp['position']) ?></div>
                  <div class="text-muted extra-small"><?= e($exp['organization']) ?></div>
                </td>
                <td class="small text-muted"><?= e($exp['employment_type']) ?></td>
                <td class="small fw-semibold">
                  <?= format_date($exp['start_date'], 'M Y') ?> &ndash; 
                  <?= $exp['is_current'] ? '<span class="badge bg-success">Present</span>' : format_date($exp['end_date'], 'M Y') ?>
                </td>
                <td>
                  <form action="" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_publish">
                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                    <button type="submit" class="btn btn-sm border-0 p-0">
                      <?php if ($exp['is_published']): ?>
                        <span class="badge-published"><i class="bi bi-eye me-1"></i> Published</span>
                      <?php else: ?>
                        <span class="badge-hidden"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                      <?php endif; ?>
                    </button>
                  </form>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>/admin/experience-edit.php?id=<?= $exp['id'] ?>" class="btn btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($exp['position']) ?>" title="Delete">
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
