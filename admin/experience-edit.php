<?php
/**
 * Admin Create / Edit Experience Form
 */
$id = (int)($_GET['id'] ?? 0);
$isEditing = $id > 0;
$pageTitle = $isEditing ? 'Edit Experience' : 'Add Experience';
$activePage = 'experience';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

$exp = [
    'id' => 0,
    'position' => '',
    'organization' => '',
    'employment_type' => 'Full-time',
    'start_date' => '',
    'end_date' => '',
    'is_current' => 0,
    'description' => '',
    'responsibilities' => '',
    'achievements' => '',
    'technologies_used' => '',
    'display_order' => 10,
    'is_published' => 1
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('danger', 'Experience record not found.');
        redirect(BASE_URL . '/admin/experience.php');
    }
    $exp = array_merge($exp, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $position      = sanitize_input($_POST['position'] ?? '');
    $org           = sanitize_input($_POST['organization'] ?? '');
    $empType       = sanitize_input($_POST['employment_type'] ?? 'Full-time');
    $startDate     = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $endDate       = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $isCurrent     = isset($_POST['is_current']) ? 1 : 0;
    $description   = trim($_POST['description'] ?? '');
    $resp          = trim($_POST['responsibilities'] ?? '');
    $achieve       = trim($_POST['achievements'] ?? '');
    $tech          = sanitize_input($_POST['technologies_used'] ?? '');
    $displayOrder  = (int)($_POST['display_order'] ?? 10);
    $isPublished   = isset($_POST['is_published']) ? 1 : 0;

    if ($isCurrent) {
        $endDate = null;
    }

    try {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE experience SET
                    position = ?,
                    organization = ?,
                    employment_type = ?,
                    start_date = ?,
                    end_date = ?,
                    is_current = ?,
                    description = ?,
                    responsibilities = ?,
                    achievements = ?,
                    technologies_used = ?,
                    display_order = ?,
                    is_published = ?
                WHERE id = ?
            ");
            $stmt->execute([$position, $org, $empType, $startDate, $endDate, $isCurrent, $description, $resp, $achieve, $tech, $displayOrder, $isPublished, $id]);
            set_flash('success', 'Experience record updated.');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO experience (
                    position, organization, employment_type, start_date, end_date,
                    is_current, description, responsibilities, achievements,
                    technologies_used, display_order, is_published
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$position, $org, $empType, $startDate, $endDate, $isCurrent, $description, $resp, $achieve, $tech, $displayOrder, $isPublished]);
            set_flash('success', 'Experience record created.');
        }
        redirect(BASE_URL . '/admin/experience.php');
    } catch (Exception $e) {
        error_log("Experience save error: " . $e->getMessage());
        set_flash('danger', 'Database error saving experience.');
    }
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1"><?= e($pageTitle) ?></h4>
    <p class="text-muted small m-0">Provide details regarding your role, accomplishments, and tech stack.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/experience.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Experience
  </a>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <form action="" method="POST">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="position" class="form-label fw-semibold">Job Title / Position *</label>
          <input type="text" class="form-control" id="position" name="position" value="<?= e($exp['position']) ?>" required>
        </div>

        <div class="col-12 col-md-6">
          <label for="organization" class="form-label fw-semibold">Company / Organization *</label>
          <input type="text" class="form-control" id="organization" name="organization" value="<?= e($exp['organization']) ?>" required>
        </div>

        <div class="col-12 col-md-4">
          <label for="employment_type" class="form-label fw-semibold">Employment Type</label>
          <select class="form-select" id="employment_type" name="employment_type">
            <option value="Full-time" <?= $exp['employment_type'] === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
            <option value="Part-time" <?= $exp['employment_type'] === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
            <option value="Contract" <?= $exp['employment_type'] === 'Contract' ? 'selected' : '' ?>>Contract</option>
            <option value="Freelance" <?= $exp['employment_type'] === 'Freelance' ? 'selected' : '' ?>>Freelance</option>
            <option value="Internship" <?= $exp['employment_type'] === 'Internship' ? 'selected' : '' ?>>Internship</option>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label for="start_date" class="form-label fw-semibold">Start Date</label>
          <input type="date" class="form-control" id="start_date" name="start_date" value="<?= e($exp['start_date']) ?>">
        </div>

        <div class="col-12 col-md-4">
          <label for="end_date" class="form-label fw-semibold">End Date</label>
          <input type="date" class="form-control" id="end_date" name="end_date" value="<?= e($exp['end_date']) ?>" <?= $exp['is_current'] ? 'disabled' : '' ?>>
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" id="is_current" name="is_current" value="1" <?= $exp['is_current'] ? 'checked' : '' ?> onchange="document.getElementById('end_date').disabled = this.checked;">
            <label class="form-check-label small" for="is_current">I currently work here</label>
          </div>
        </div>

        <div class="col-12">
          <label for="technologies_used" class="form-label fw-semibold">Technologies / Tools Used</label>
          <input type="text" class="form-control" id="technologies_used" name="technologies_used" value="<?= e($exp['technologies_used']) ?>" placeholder="e.g. PHP 8, MySQL, Git, Postman">
        </div>

        <div class="col-12">
          <label for="description" class="form-label fw-semibold">Role Summary / Overview</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= e($exp['description']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label for="responsibilities" class="form-label fw-semibold">Key Responsibilities (Bullet Points)</label>
          <textarea class="form-control" id="responsibilities" name="responsibilities" rows="4" placeholder="• Responsibility 1&#10;• Responsibility 2"><?= e($exp['responsibilities']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label for="achievements" class="form-label fw-semibold">Key Achievements / Metrics</label>
          <textarea class="form-control" id="achievements" name="achievements" rows="4" placeholder="• Reduced API latency by 30%&#10;• Authored 50+ test cases"><?= e($exp['achievements']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label for="display_order" class="form-label fw-semibold">Display Order</label>
          <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)$exp['display_order'] ?>">
        </div>

        <div class="col-12 col-md-6 d-flex align-items-center pt-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $exp['is_published'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_published">Published (Visible Publicly)</label>
          </div>
        </div>

      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="<?= BASE_URL ?>/admin/experience.php" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
          <i class="bi bi-save me-1"></i> <?= $isEditing ? 'Save Changes' : 'Add Experience' ?>
        </button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
