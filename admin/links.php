<?php
/**
 * Admin Social & External Links Manager
 */
$pageTitle = 'Social & External Links';
$activePage = 'links';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_csrf_token();
    $action = $_POST['action'];

    if ($action === 'save_link') {
        $linkId       = (int)($_POST['link_id'] ?? 0);
        $name         = sanitize_input($_POST['name'] ?? '');
        $url          = sanitize_input($_POST['url'] ?? '');
        $icon         = sanitize_input($_POST['icon'] ?? '');
        $category     = sanitize_input($_POST['category'] ?? 'Social');
        $displayOrder = (int)($_POST['display_order'] ?? 10);
        $isActive     = isset($_POST['is_active']) ? 1 : 0;

        if (!empty($name) && !empty($url)) {
            // Basic URL prefix fix if missing scheme and not mailto
            if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://') && !str_starts_with($url, 'mailto:')) {
                $url = 'https://' . $url;
            }

            if ($linkId > 0) {
                $stmt = $pdo->prepare("
                    UPDATE external_links SET name = ?, url = ?, icon = ?, category = ?, display_order = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $url, $icon, $category, $displayOrder, $isActive, $linkId]);
                set_flash('success', 'Link updated.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO external_links (name, url, icon, category, display_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $url, $icon, $category, $displayOrder, $isActive]);
                set_flash('success', 'New link created.');
            }
        } else {
            set_flash('danger', 'Link name and URL are required.');
        }
    } elseif ($action === 'delete_link') {
        $linkId = (int)($_POST['link_id'] ?? 0);
        if ($linkId > 0) {
            $stmt = $pdo->prepare("DELETE FROM external_links WHERE id = ?");
            $stmt->execute([$linkId]);
            set_flash('success', 'Link deleted.');
        }
    }
    redirect(BASE_URL . '/admin/links.php');
}

$links = $pdo->query("SELECT * FROM external_links ORDER BY display_order ASC, id DESC")->fetchAll();

$editLink = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM external_links WHERE id = ?");
    $stmt->execute([$editId]);
    $editLink = $stmt->fetch();
}
?>

<div class="row g-4">
  <!-- Link Form -->
  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="m-0 fw-bold"><i class="bi bi-link-45deg text-teal me-2"></i> <?= $editLink ? 'Edit Link' : 'Add New Link' ?></h6>
        <?php if ($editLink): ?>
          <a href="<?= BASE_URL ?>/admin/links.php" class="btn btn-sm btn-outline-secondary">Cancel Edit</a>
        <?php endif; ?>
      </div>
      <div class="card-body p-4">
        <form action="" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_link">
          <input type="hidden" name="link_id" value="<?= $editLink['id'] ?? 0 ?>">

          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Link Name / Platform *</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= e($editLink['name'] ?? '') ?>" placeholder="e.g. GitHub, LinkedIn, Personal Site" required>
          </div>

          <div class="mb-3">
            <label for="url" class="form-label fw-semibold">Target URL *</label>
            <input type="text" class="form-control" id="url" name="url" value="<?= e($editLink['url'] ?? '') ?>" placeholder="https://github.com/username" required>
          </div>

          <div class="mb-3">
            <label for="icon" class="form-label fw-semibold">Icon Class (Bootstrap Icons)</label>
            <input type="text" class="form-control" id="icon" name="icon" value="<?= e($editLink['icon'] ?? 'bi bi-link') ?>" placeholder="bi bi-github">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-6">
              <label for="category" class="form-label fw-semibold">Category</label>
              <select class="form-select" id="category" name="category">
                <option value="Social" <?= ($editLink['category'] ?? '') === 'Social' ? 'selected' : '' ?>>Social</option>
                <option value="Contact" <?= ($editLink['category'] ?? '') === 'Contact' ? 'selected' : '' ?>>Contact</option>
                <option value="Portfolio" <?= ($editLink['category'] ?? '') === 'Portfolio' ? 'selected' : '' ?>>Portfolio</option>
                <option value="Other" <?= ($editLink['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
              </select>
            </div>
            <div class="col-6">
              <label for="display_order" class="form-label fw-semibold">Order</label>
              <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)($editLink['display_order'] ?? 10) ?>">
            </div>
          </div>

          <div class="mb-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (!$editLink || $editLink['is_active']) ? 'checked' : '' ?>>
              <label class="form-check-label fw-semibold" for="is_active">Active (Visible)</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
            <i class="bi bi-check-lg me-1"></i> <?= $editLink ? 'Update Link' : 'Save Link' ?>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Links Table -->
  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom">
        <h6 class="m-0 fw-bold">Active Social &amp; External Links</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle m-0">
            <thead class="table-light">
              <tr>
                <th style="width:50px;">Order</th>
                <th>Platform</th>
                <th>URL</th>
                <th>Category</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($links)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No external links added yet.</td></tr>
              <?php else: ?>
                <?php foreach ($links as $l): ?>
                  <tr>
                    <td class="fw-bold text-muted"><?= (int)$l['display_order'] ?></td>
                    <td class="fw-bold text-dark">
                      <i class="<?= e($l['icon'] ?: 'bi bi-link') ?> me-1 text-teal"></i>
                      <?= e($l['name']) ?>
                    </td>
                    <td class="small">
                      <a href="<?= e($l['url']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width:200px;">
                        <?= e($l['url']) ?>
                      </a>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= e($l['category']) ?></span></td>
                    <td class="text-end">
                      <a href="<?= BASE_URL ?>/admin/links.php?edit_id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form action="" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_link">
                        <input type="hidden" name="link_id" value="<?= $l['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item-title="<?= e($l['name']) ?>">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
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
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
