<?php
/**
 * Admin Media Assets Manager
 */
$pageTitle = 'Media Assets Manager';
$activePage = 'media';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    require_csrf_token();
    $mediaId = (int)($_POST['media_id'] ?? 0);

    if ($mediaId > 0) {
        $stmt = $pdo->prepare("SELECT file_path, original_name FROM media WHERE id = ? LIMIT 1");
        $stmt->execute([$mediaId]);
        $m = $stmt->fetch();

        if ($m) {
            delete_uploaded_file($m['file_path']);
            set_flash('success', 'Media file "' . e($m['original_name']) . '" deleted.');
        }
    }
    redirect(BASE_URL . '/admin/media.php');
}

$mediaFiles = $pdo->query("SELECT * FROM media ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Central Media Asset Library</h4>
    <p class="text-muted small m-0">View all uploaded images, PDFs, certificates, and document attachments.</p>
  </div>
  <div class="text-muted small">
    Total Storage Assets: <strong><?= count($mediaFiles) ?></strong>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
      <input type="text" id="table-search-input" class="form-control" placeholder="Search media by filename...">
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-custom table-searchable align-middle m-0">
        <thead>
          <tr>
            <th style="width:70px;">Preview</th>
            <th>Original Name</th>
            <th>Module</th>
            <th>Type</th>
            <th>Size</th>
            <th>Uploaded</th>
            <th class="text-end" style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($mediaFiles)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No media assets in library yet.</td></tr>
          <?php else: ?>
            <?php foreach ($mediaFiles as $m): ?>
              <tr>
                <td>
                  <?php if (str_starts_with($m['mime_type'], 'image/')): ?>
                    <img src="<?= BASE_URL . '/' . e($m['file_path']) ?>" class="rounded border" style="width:45px; height:45px; object-fit:cover;" alt="Thumb">
                  <?php else: ?>
                    <div class="bg-light rounded border d-flex align-items-center justify-content-center text-primary fs-4" style="width:45px; height:45px;">
                      <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-bold text-dark text-truncate" style="max-width:260px;"><?= e($m['original_name']) ?></div>
                  <div class="text-muted extra-small font-monospace"><?= e($m['stored_filename']) ?></div>
                </td>
                <td><span class="badge bg-light text-dark border"><?= e($m['associated_module'] ?: 'General') ?></span></td>
                <td class="small text-muted"><?= e($m['mime_type']) ?></td>
                <td class="small fw-semibold"><?= round($m['file_size'] / 1024, 1) ?> KB</td>
                <td class="small text-muted"><?= format_date($m['created_at'], 'M d, Y H:i') ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= BASE_URL . '/' . e($m['file_path']) ?>" target="_blank" class="btn btn-outline-secondary" title="View / Download">
                      <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <form action="" method="POST" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="media_id" value="<?= $m['id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-delete-confirm" data-item-title="<?= e($m['original_name']) ?>" title="Delete">
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
