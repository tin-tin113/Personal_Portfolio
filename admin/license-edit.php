<?php
/**
 * Admin Create / Edit License Form
 */
$id = (int)($_GET['id'] ?? 0);
$isEditing = $id > 0;
$pageTitle = $isEditing ? 'Edit License / Credential' : 'Add License / Credential';
$activePage = 'licenses';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

$lic = [
    'id' => 0,
    'title' => '',
    'issuing_organization' => '',
    'issue_date' => '',
    'license_number' => '',
    'verification_url' => '',
    'document_file' => '',
    'description' => '',
    'is_published' => 1,
    'display_order' => 10
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM licenses WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('danger', 'License not found.');
        redirect(BASE_URL . '/admin/licenses.php');
    }
    $lic = array_merge($lic, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $title         = sanitize_input($_POST['title'] ?? '');
    $org           = sanitize_input($_POST['issuing_organization'] ?? '');
    $issueDate     = !empty($_POST['issue_date']) ? $_POST['issue_date'] : null;
    $licNum        = sanitize_input($_POST['license_number'] ?? '');
    $verifyUrl     = sanitize_input($_POST['verification_url'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $isPublished   = isset($_POST['is_published']) ? 1 : 0;
    $displayOrder  = (int)($_POST['display_order'] ?? 10);

    $docFile = $lic['document_file'];

    if (!empty($_FILES['document_file']['name'])) {
        $upRes = handle_file_upload($_FILES['document_file'], 'licenses', 'doc', $docFile);
        if ($upRes['success']) {
            $docFile = $upRes['filepath'];
        } else {
            set_flash('danger', 'License document upload failed: ' . $upRes['error']);
            redirect($_SERVER['REQUEST_URI']);
        }
    }

    try {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE licenses SET
                    title = ?,
                    issuing_organization = ?,
                    issue_date = ?,
                    license_number = ?,
                    verification_url = ?,
                    document_file = ?,
                    description = ?,
                    is_published = ?,
                    display_order = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $org, $issueDate, $licNum, $verifyUrl, $docFile, $description, $isPublished, $displayOrder, $id]);
            set_flash('success', 'License updated successfully!');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO licenses (
                    title, issuing_organization, issue_date, license_number,
                    verification_url, document_file, description, is_published, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $org, $issueDate, $licNum, $verifyUrl, $docFile, $description, $isPublished, $displayOrder]);
            set_flash('success', 'License added successfully!');
        }
        redirect(BASE_URL . '/admin/licenses.php');
    } catch (Exception $e) {
        error_log("License save error: " . $e->getMessage());
        set_flash('danger', 'Database error saving license.');
    }
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1"><?= e($pageTitle) ?></h4>
    <p class="text-muted small m-0">Enter credential information and optional verification documents.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/licenses.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Licenses
  </a>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <form action="" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="title" class="form-label fw-semibold">License / Credential Title *</label>
          <input type="text" class="form-control" id="title" name="title" value="<?= e($lic['title']) ?>" required>
        </div>

        <div class="col-12 col-md-6">
          <label for="issuing_organization" class="form-label fw-semibold">Issuing Organization / Board *</label>
          <input type="text" class="form-control" id="issuing_organization" name="issuing_organization" value="<?= e($lic['issuing_organization']) ?>" required>
        </div>

        <div class="col-12 col-md-4">
          <label for="issue_date" class="form-label fw-semibold">Issue Date</label>
          <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?= e($lic['issue_date']) ?>">
        </div>

        <div class="col-12 col-md-4">
          <label for="license_number" class="form-label fw-semibold">License / Registration #</label>
          <input type="text" class="form-control" id="license_number" name="license_number" value="<?= e($lic['license_number']) ?>">
        </div>

        <div class="col-12 col-md-4">
          <label for="display_order" class="form-label fw-semibold">Display Order</label>
          <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)$lic['display_order'] ?>">
        </div>

        <div class="col-12 col-md-6">
          <label for="verification_url" class="form-label fw-semibold">Verification URL</label>
          <input type="url" class="form-control" id="verification_url" name="verification_url" value="<?= e($lic['verification_url']) ?>" placeholder="https://example.com/verify">
        </div>

        <div class="col-12 col-md-6">
          <label for="document_file" class="form-label fw-semibold">Document / PDF Attachment</label>
          <?php if (!empty($lic['document_file'])): ?>
            <div class="mb-2">
              <a href="<?= BASE_URL . '/' . e($lic['document_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i> View Existing Document
              </a>
            </div>
          <?php endif; ?>
          <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf,image/jpeg,image/png,image/webp">
        </div>

        <div class="col-12">
          <label for="description" class="form-label fw-semibold">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= e($lic['description']) ?></textarea>
        </div>

        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $lic['is_published'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_published">Published Publicly</label>
          </div>
        </div>

      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="<?= BASE_URL ?>/admin/licenses.php" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
          <i class="bi bi-save me-1"></i> <?= $isEditing ? 'Save Changes' : 'Add License' ?>
        </button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
