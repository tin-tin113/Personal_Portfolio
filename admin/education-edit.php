<?php
/**
 * Admin Create / Edit Education Form
 */
$id = (int)($_GET['id'] ?? 0);
$isEditing = $id > 0;
$pageTitle = $isEditing ? 'Edit Education' : 'Add Education';
$activePage = 'education';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

$edu = [
    'id' => 0,
    'institution' => '',
    'degree_program' => '',
    'start_year' => '',
    'end_year' => '',
    'description' => '',
    'achievements' => '',
    'relevant_coursework' => '',
    'credential_file' => '',
    'display_order' => 10
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('danger', 'Education record not found.');
        redirect(BASE_URL . '/admin/education.php');
    }
    $edu = array_merge($edu, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $institution   = sanitize_input($_POST['institution'] ?? '');
    $degree        = sanitize_input($_POST['degree_program'] ?? '');
    $startYear     = sanitize_input($_POST['start_year'] ?? '');
    $endYear       = sanitize_input($_POST['end_year'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $achievements  = trim($_POST['achievements'] ?? '');
    $coursework    = trim($_POST['relevant_coursework'] ?? '');
    $displayOrder  = (int)($_POST['display_order'] ?? 10);

    $credFile = $edu['credential_file'];

    if (!empty($_FILES['credential_file']['name'])) {
        $upRes = handle_file_upload($_FILES['credential_file'], 'certs', 'doc', $credFile);
        if ($upRes['success']) {
            $credFile = $upRes['filepath'];
        }
    }

    try {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE education SET
                    institution = ?,
                    degree_program = ?,
                    start_year = ?,
                    end_year = ?,
                    description = ?,
                    achievements = ?,
                    relevant_coursework = ?,
                    credential_file = ?,
                    display_order = ?
                WHERE id = ?
            ");
            $stmt->execute([$institution, $degree, $startYear, $endYear, $description, $achievements, $coursework, $credFile, $displayOrder, $id]);
            set_flash('success', 'Education record updated.');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO education (
                    institution, degree_program, start_year, end_year,
                    description, achievements, relevant_coursework, credential_file, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$institution, $degree, $startYear, $endYear, $description, $achievements, $coursework, $credFile, $displayOrder]);
            set_flash('success', 'Education record added.');
        }
        redirect(BASE_URL . '/admin/education.php');
    } catch (Exception $e) {
        error_log("Education save error: " . $e->getMessage());
        set_flash('danger', 'Database error saving education.');
    }
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1"><?= e($pageTitle) ?></h4>
    <p class="text-muted small m-0">Record academic background and diploma credentials.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/education.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Education
  </a>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <form action="" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="institution" class="form-label fw-semibold">Institution / University *</label>
          <input type="text" class="form-control" id="institution" name="institution" value="<?= e($edu['institution']) ?>" required>
        </div>

        <div class="col-12 col-md-6">
          <label for="degree_program" class="form-label fw-semibold">Degree / Field of Study *</label>
          <input type="text" class="form-control" id="degree_program" name="degree_program" value="<?= e($edu['degree_program']) ?>" required>
        </div>

        <div class="col-12 col-md-4">
          <label for="start_year" class="form-label fw-semibold">Start Year</label>
          <input type="text" class="form-control" id="start_year" name="start_year" value="<?= e($edu['start_year']) ?>" placeholder="2021">
        </div>

        <div class="col-12 col-md-4">
          <label for="end_year" class="form-label fw-semibold">End Year / Expected</label>
          <input type="text" class="form-control" id="end_year" name="end_year" value="<?= e($edu['end_year']) ?>" placeholder="2025 or Present">
        </div>

        <div class="col-12 col-md-4">
          <label for="display_order" class="form-label fw-semibold">Display Order</label>
          <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)$edu['display_order'] ?>">
        </div>

        <div class="col-12">
          <label for="description" class="form-label fw-semibold">Description / Overview</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= e($edu['description']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label for="achievements" class="form-label fw-semibold">Academic Achievements / Honors</label>
          <textarea class="form-control" id="achievements" name="achievements" rows="3" placeholder="• Honors Graduate&#10;• Capstone Award"><?= e($edu['achievements']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label for="relevant_coursework" class="form-label fw-semibold">Relevant Coursework</label>
          <textarea class="form-control" id="relevant_coursework" name="relevant_coursework" rows="3" placeholder="PHP, MySQL, Software QA, Data Structures"><?= e($edu['relevant_coursework']) ?></textarea>
        </div>

        <div class="col-12">
          <label for="credential_file" class="form-label fw-semibold">Diploma / Transcript Attachment (PDF)</label>
          <?php if (!empty($edu['credential_file'])): ?>
            <div class="mb-2">
              <a href="<?= BASE_URL . '/' . e($edu['credential_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i> View Existing Diploma File
              </a>
            </div>
          <?php endif; ?>
          <input type="file" class="form-control" id="credential_file" name="credential_file" accept=".pdf,image/jpeg,image/png,image/webp">
        </div>

      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="<?= BASE_URL ?>/admin/education.php" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
          <i class="bi bi-save me-1"></i> <?= $isEditing ? 'Save Changes' : 'Add Education' ?>
        </button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
