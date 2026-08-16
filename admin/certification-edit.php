<?php
/**
 * Admin Create / Edit Certification Form
 */
$id = (int)($_GET['id'] ?? 0);
$isEditing = $id > 0;
$pageTitle = $isEditing ? 'Edit Certification' : 'Add Certification';
$activePage = 'certifications';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

$cert = [
    'id' => 0,
    'title' => '',
    'issuing_organization' => '',
    'issue_date' => '',
    'expiration_date' => '',
    'credential_id' => '',
    'credential_url' => '',
    'certificate_file' => '',
    'description' => '',
    'is_featured' => 0,
    'is_published' => 1,
    'display_order' => 10
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM certifications WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('danger', 'Certification not found.');
        redirect(BASE_URL . '/admin/certifications.php');
    }
    $cert = array_merge($cert, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $title         = sanitize_input($_POST['title'] ?? '');
    $org           = sanitize_input($_POST['issuing_organization'] ?? '');
    $issueDate     = !empty($_POST['issue_date']) ? $_POST['issue_date'] : null;
    $expDate       = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $credentialId  = sanitize_input($_POST['credential_id'] ?? '');
    $credentialUrl = sanitize_input($_POST['credential_url'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $isFeatured    = isset($_POST['is_featured']) ? 1 : 0;
    $isPublished   = isset($_POST['is_published']) ? 1 : 0;
    $displayOrder  = (int)($_POST['display_order'] ?? 10);

    $certFile = $cert['certificate_file'];

    if (!empty($_FILES['certificate_file']['name'])) {
        $upRes = handle_file_upload($_FILES['certificate_file'], 'certs', 'doc', $certFile);
        if ($upRes['success']) {
            $certFile = $upRes['filepath'];
        } else {
            set_flash('danger', 'Certificate file upload failed: ' . $upRes['error']);
            redirect($_SERVER['REQUEST_URI']);
        }
    }

    try {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE certifications SET
                    title = ?,
                    issuing_organization = ?,
                    issue_date = ?,
                    expiration_date = ?,
                    credential_id = ?,
                    credential_url = ?,
                    certificate_file = ?,
                    description = ?,
                    is_featured = ?,
                    is_published = ?,
                    display_order = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $org, $issueDate, $expDate, $credentialId,
                $credentialUrl, $certFile, $description, $isFeatured,
                $isPublished, $displayOrder, $id
            ]);
            set_flash('success', 'Certification updated successfully!');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO certifications (
                    title, issuing_organization, issue_date, expiration_date,
                    credential_id, credential_url, certificate_file, description,
                    is_featured, is_published, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $org, $issueDate, $expDate, $credentialId,
                $credentialUrl, $certFile, $description, $isFeatured,
                $isPublished, $displayOrder
            ]);
            set_flash('success', 'Certification added successfully!');
        }
        redirect(BASE_URL . '/admin/certifications.php');
    } catch (Exception $e) {
        error_log("Cert save error: " . $e->getMessage());
        set_flash('danger', 'Database error saving certification.');
    }
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1"><?= e($pageTitle) ?></h4>
    <p class="text-muted small m-0">Provide details and upload certificate verification documents.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/certifications.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Certifications
  </a>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <form action="" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="row g-3">
        <!-- Title -->
        <div class="col-12 col-md-6">
          <label for="title" class="form-label fw-semibold">Certification Name *</label>
          <input type="text" class="form-control" id="title" name="title" value="<?= e($cert['title']) ?>" required>
        </div>

        <!-- Organization -->
        <div class="col-12 col-md-6">
          <label for="issuing_organization" class="form-label fw-semibold">Issuing Organization *</label>
          <input type="text" class="form-control" id="issuing_organization" name="issuing_organization" value="<?= e($cert['issuing_organization']) ?>" required>
        </div>

        <!-- Issue Date -->
        <div class="col-12 col-md-3">
          <label for="issue_date" class="form-label fw-semibold">Issue Date</label>
          <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?= e($cert['issue_date']) ?>">
        </div>

        <!-- Expiration Date -->
        <div class="col-12 col-md-3">
          <label for="expiration_date" class="form-label fw-semibold">Expiration Date (Optional)</label>
          <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="<?= e($cert['expiration_date']) ?>">
        </div>

        <!-- Credential ID -->
        <div class="col-12 col-md-3">
          <label for="credential_id" class="form-label fw-semibold">Credential ID</label>
          <input type="text" class="form-control" id="credential_id" name="credential_id" value="<?= e($cert['credential_id']) ?>">
        </div>

        <!-- Display Order -->
        <div class="col-12 col-md-3">
          <label for="display_order" class="form-label fw-semibold">Display Order</label>
          <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)$cert['display_order'] ?>">
        </div>

        <!-- Credential URL -->
        <div class="col-12 col-md-6">
          <label for="credential_url" class="form-label fw-semibold">Online Verification URL</label>
          <input type="url" class="form-control" id="credential_url" name="credential_url" value="<?= e($cert['credential_url']) ?>" placeholder="https://example.com/verify/123">
        </div>

        <!-- Certificate File Upload -->
        <div class="col-12 col-md-6">
          <label for="certificate_file" class="form-label fw-semibold">Certificate Document/PDF File</label>
          <?php if (!empty($cert['certificate_file'])): ?>
            <div class="mb-2">
              <a href="<?= BASE_URL . '/' . e($cert['certificate_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i> View Existing Certificate
              </a>
            </div>
          <?php endif; ?>
          <input type="file" class="form-control" id="certificate_file" name="certificate_file" accept=".pdf,image/jpeg,image/png,image/webp">
          <div class="form-text extra-small">Max size 10MB (PDF, JPG, PNG).</div>
        </div>

        <!-- Description -->
        <div class="col-12">
          <label for="description" class="form-label fw-semibold">Description / Skills Validated</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= e($cert['description']) ?></textarea>
        </div>

        <!-- Flags -->
        <div class="col-12 col-md-6 d-flex align-items-center pt-2">
          <div class="form-check form-switch me-4">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $cert['is_featured'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_featured">Featured on Homepage</label>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $cert['is_published'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_published">Published</label>
          </div>
        </div>

      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="<?= BASE_URL ?>/admin/certifications.php" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
          <i class="bi bi-save me-1"></i> <?= $isEditing ? 'Save Changes' : 'Add Certification' ?>
        </button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
