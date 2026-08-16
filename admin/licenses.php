<?php
/**
 * Admin Licenses & Professional Credentials Manager
 */
$pageTitle = 'Licenses & Credentials';
$activePage = 'licenses';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT document_file FROM licenses WHERE id = ?");
        $stmt->execute([$id]);
        $lic = $stmt->fetch();
        if ($lic && !empty($lic['document_file'])) {
            delete_uploaded_file($lic['document_file']);
        }
        $del = $pdo->prepare("DELETE FROM licenses WHERE id = ?");
        $del->execute([$id]);
        set_flash('success', 'License deleted successfully.');
    }
    redirect(BASE_URL . '/admin/licenses.php');
}

$licenses = $pdo->query("SELECT * FROM licenses ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Licenses &amp; Professional Credentials</h4>
    <p class="text-muted small m-0">Manage professional qualifications, examination results, and agency licenses.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/license-edit.php" class="btn btn-primary fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
    <i class="bi bi-plus-lg me-1"></i> Add License/Credential
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search licenses...">
    </div>
    <span class="text-muted small">Total: <strong><?= count($licenses) ?></strong> items</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:60px;">Order</th>
            <th>License Title &amp; Agency</th>
            <th>Issue Date</th>
            <th>License #</th>
            <th>Status</th>
            <th class="text-end" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($licenses)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No licenses or credentials added yet.</td></tr>
          <?php else: ?>
            <?php foreach ($licenses as $l): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$l['display_order'] ?></td>
                <td>
                  <div class="fw-bold text-dark"><?= e($l['title']) ?></div>
                  <div class="text-muted extra-small"><?= e($l['issuing_organization']) ?></div>
                </td>
                <td class="small text-muted"><?= format_date($l['issue_date'], 'M Y') ?></td>
                <td class="small font-monospace"><?= e($l['license_number'] ?: 'N/A') ?></td>
                <td>
                  <?php if ($l['is_published']): ?>
                    <span class="badge-published"><i class="bi bi-eye me-1"></i> Published</span>
                  <?php else: ?>
                    <span class="badge-hidden"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL ?>/admin/license-edit.php?id=<?= $l['id'] ?>" class="btn btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $l['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($l['title']) ?>" title="Delete">
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
