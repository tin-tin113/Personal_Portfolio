<?php
/**
 * Admin Education Manager List
 */
$pageTitle = 'Education Manager';
$activePage = 'education';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT credential_file FROM education WHERE id = ?");
        $stmt->execute([$id]);
        $edu = $stmt->fetch();
        if ($edu && !empty($edu['credential_file'])) {
            delete_uploaded_file($edu['credential_file']);
        }
        $del = $pdo->prepare("DELETE FROM education WHERE id = ?");
        $del->execute([$id]);
        set_flash('success', 'Education record deleted.');
    }
    redirect(BASE_URL . '/admin/education.php');
}

$education = $pdo->query("SELECT * FROM education ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Education &amp; Academic Background</h4>
    <p class="text-muted small m-0">Manage degrees, academic institutions, graduation dates, and relevant coursework.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/education-edit.php" class="btn btn-primary fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
    <i class="bi bi-plus-lg me-1"></i> Add Education
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search education...">
    </div>
    <span class="text-muted small">Total: <strong><?= count($education) ?></strong> items</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:60px;">Order</th>
            <th>Degree / Program</th>
            <th>Institution</th>
            <th>Years</th>
            <th class="text-end" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($education)): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No education records added yet.</td></tr>
          <?php else: ?>
            <?php foreach ($education as $e): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$e['display_order'] ?></td>
                <td class="fw-bold text-dark"><?= e($e['degree_program']) ?></td>
                <td class="small text-muted"><?= e($e['institution']) ?></td>
                <td class="small fw-semibold"><?= e($e['start_year']) ?> &ndash; <?= e($e['end_year']) ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>/admin/education-edit.php?id=<?= $e['id'] ?>" class="btn btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $e['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($e['degree_program']) ?>" title="Delete">
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
