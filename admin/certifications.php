<?php
/**
 * Admin Certifications Manager List & Actions
 */
$pageTitle = 'Certifications Manager';
$activePage = 'certifications';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

// Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT certificate_file FROM certifications WHERE id = ?");
        $stmt->execute([$id]);
        $cert = $stmt->fetch();
        if ($cert && !empty($cert['certificate_file'])) {
            delete_uploaded_file($cert['certificate_file']);
        }

        $del = $pdo->prepare("DELETE FROM certifications WHERE id = ?");
        $del->execute([$id]);

        set_flash('success', 'Certification deleted successfully.');
    }
    redirect(BASE_URL . '/admin/certifications.php');
}

// Toggle Publish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_publish') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE certifications SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('info', 'Certification visibility toggled.');
    }
    redirect(BASE_URL . '/admin/certifications.php');
}

$certifications = $pdo->query("SELECT * FROM certifications ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Certifications &amp; Training</h4>
    <p class="text-muted small m-0">Manage professional certificates, credential IDs, and certificate downloads.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/certification-edit.php" class="btn btn-primary fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
    <i class="bi bi-plus-lg me-1"></i> Add Certification
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search certifications...">
    </div>
    <span class="text-muted small">Total: <strong><?= count($certifications) ?></strong> items</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:60px;">Order</th>
            <th>Title &amp; Issuing Body</th>
            <th>Issue Date</th>
            <th>Credential ID</th>
            <th>Document</th>
            <th>Status</th>
            <th class="text-end" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($certifications)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No certifications added yet.</td></tr>
          <?php else: ?>
            <?php foreach ($certifications as $c): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$c['display_order'] ?></td>
                <td>
                  <div class="fw-bold text-dark"><?= e($c['title']) ?></div>
                  <div class="text-muted extra-small"><?= e($c['issuing_organization']) ?></div>
                </td>
                <td class="small text-muted"><?= format_date($c['issue_date'], 'M Y') ?></td>
                <td class="small font-monospace"><?= e($c['credential_id'] ?: 'N/A') ?></td>
                <td>
                  <?php if (!empty($c['certificate_file'])): ?>
                    <a href="<?= BASE_URL . '/' . e($c['certificate_file']) ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                      <i class="bi bi-file-earmark-pdf me-1"></i> File
                    </a>
                  <?php else: ?>
                    <span class="text-muted extra-small">No File</span>
                  <?php endif; ?>
                </td>
                <td>
                  <form action="" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_publish">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-sm border-0 p-0">
                      <?php if ($c['is_published']): ?>
                        <span class="badge-published"><i class="bi bi-eye me-1"></i> Published</span>
                      <?php else: ?>
                        <span class="badge-hidden"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                      <?php endif; ?>
                    </button>
                  </form>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>/admin/certification-edit.php?id=<?= $c['id'] ?>" class="btn btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $c['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($c['title']) ?>" title="Delete">
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
