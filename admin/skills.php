<?php
/**
 * Admin Skills & Categories Manager
 */
$pageTitle = 'Skills & Categories Manager';
$activePage = 'skills';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

// Handle Category Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_csrf_token();
    $action = $_POST['action'];

    if ($action === 'add_category') {
        $catName = sanitize_input($_POST['category_name'] ?? '');
        $order   = (int)($_POST['category_order'] ?? 10);
        if (!empty($catName)) {
            $stmt = $pdo->prepare("INSERT INTO skill_categories (name, display_order) VALUES (?, ?)");
            $stmt->execute([$catName, $order]);
            set_flash('success', 'Skill category created.');
        }
    } elseif ($action === 'delete_category') {
        $catId = (int)($_POST['category_id'] ?? 0);
        if ($catId > 0) {
            $stmt = $pdo->prepare("DELETE FROM skill_categories WHERE id = ?");
            $stmt->execute([$catId]);
            set_flash('info', 'Category deleted. Associated skills moved to unassigned.');
        }
    } elseif ($action === 'save_skill') {
        $skillId     = (int)($_POST['skill_id'] ?? 0);
        $catId       = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $name        = sanitize_input($_POST['skill_name'] ?? '');
        $proficiency = (int)($_POST['proficiency_level'] ?? 80);
        $levelName   = sanitize_input($_POST['level_name'] ?? 'Advanced');
        $icon        = sanitize_input($_POST['icon_class'] ?? '');
        $order       = (int)($_POST['display_order'] ?? 10);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if (!empty($name)) {
            if ($skillId > 0) {
                $stmt = $pdo->prepare("
                    UPDATE skills SET category_id = ?, name = ?, proficiency_level = ?, level_name = ?, icon_class = ?, display_order = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$catId, $name, $proficiency, $levelName, $icon, $order, $isActive, $skillId]);
                set_flash('success', 'Skill updated successfully.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO skills (category_id, name, proficiency_level, level_name, icon_class, display_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$catId, $name, $proficiency, $levelName, $icon, $order, $isActive]);
                set_flash('success', 'Skill added successfully.');
            }
        }
    } elseif ($action === 'delete_skill') {
        $skillId = (int)($_POST['skill_id'] ?? 0);
        if ($skillId > 0) {
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$skillId]);
            set_flash('success', 'Skill removed.');
        }
    }

    redirect(BASE_URL . '/admin/skills.php');
}

// Fetch Data
$categories = $pdo->query("SELECT * FROM skill_categories ORDER BY display_order ASC, id ASC")->fetchAll();
$skills = $pdo->query("
    SELECT s.*, c.name as category_name
    FROM skills s
    LEFT JOIN skill_categories c ON s.category_id = c.id
    ORDER BY c.display_order ASC, s.display_order ASC, s.id DESC
")->fetchAll();

// Edit modal data helper
$editSkill = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$editId]);
    $editSkill = $stmt->fetch();
}
?>

<div class="row g-4">
  <!-- Skills Form & List -->
  <div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="m-0 fw-bold"><i class="bi bi-cpu text-teal me-2"></i> <?= $editSkill ? 'Edit Skill' : 'Add New Skill' ?></h6>
        <?php if ($editSkill): ?>
          <a href="<?= BASE_URL ?>/admin/skills.php" class="btn btn-sm btn-outline-secondary">Cancel Edit</a>
        <?php endif; ?>
      </div>
      <div class="card-body p-4">
        <form action="" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_skill">
          <input type="hidden" name="skill_id" value="<?= $editSkill['id'] ?? 0 ?>">

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label for="skill_name" class="form-label fw-semibold">Skill Name *</label>
              <input type="text" class="form-control" id="skill_name" name="skill_name" value="<?= e($editSkill['name'] ?? '') ?>" placeholder="e.g. PHP 8, MySQL" required>
            </div>

            <div class="col-12 col-md-6">
              <label for="category_id" class="form-label fw-semibold">Category</label>
              <select class="form-select" id="category_id" name="category_id">
                <option value="">Unassigned</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= ($editSkill && $editSkill['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label for="proficiency_level" class="form-label fw-semibold">Proficiency (1-100 %)</label>
              <input type="number" class="form-control" id="proficiency_level" name="proficiency_level" min="10" max="100" value="<?= (int)($editSkill['proficiency_level'] ?? 85) ?>">
            </div>

            <div class="col-12 col-md-4">
              <label for="level_name" class="form-label fw-semibold">Level Label</label>
              <input type="text" class="form-control" id="level_name" name="level_name" value="<?= e($editSkill['level_name'] ?? 'Advanced') ?>" placeholder="e.g. Expert, Advanced">
            </div>

            <div class="col-12 col-md-4">
              <label for="icon_class" class="form-label fw-semibold">Bootstrap Icon Class</label>
              <input type="text" class="form-control" id="icon_class" name="icon_class" value="<?= e($editSkill['icon_class'] ?? 'bi bi-code-slash') ?>" placeholder="bi bi-code-slash">
            </div>

            <div class="col-12 col-md-6">
              <label for="display_order" class="form-label fw-semibold">Display Order</label>
              <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)($editSkill['display_order'] ?? 10) ?>">
            </div>

            <div class="col-12 col-md-6 d-flex align-items-center pt-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (!$editSkill || $editSkill['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="is_active">Active (Visible)</label>
              </div>
            </div>

            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
                <i class="bi bi-check-lg me-1"></i> <?= $editSkill ? 'Update Skill' : 'Save Skill' ?>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Skills List Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom">
        <h6 class="m-0 fw-bold">Active Skills Database</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle m-0">
            <thead class="table-light">
              <tr>
                <th>Skill</th>
                <th>Category</th>
                <th>Level</th>
                <th>Icon</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($skills)): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">No skills configured.</td></tr>
              <?php else: ?>
                <?php foreach ($skills as $s): ?>
                  <tr>
                    <td class="fw-bold">
                      <i class="<?= e($s['icon_class'] ?: 'bi bi-dash') ?> text-teal me-2"></i>
                      <?= e($s['name']) ?>
                    </td>
                    <td class="small text-muted"><?= e($s['category_name'] ?: 'Unassigned') ?></td>
                    <td>
                      <span class="badge bg-light text-dark border"><?= (int)$s['proficiency_level'] ?>% (<?= e($s['level_name']) ?>)</span>
                    </td>
                    <td class="small text-muted font-monospace"><?= e($s['icon_class']) ?></td>
                    <td class="text-end">
                      <a href="<?= BASE_URL ?>/admin/skills.php?edit_id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form action="" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_skill">
                        <input type="hidden" name="skill_id" value="<?= $s['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item-title="<?= e($s['name']) ?>">
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

  <!-- Skill Categories Panel -->
  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3 border-bottom">
        <h6 class="m-0 fw-bold"><i class="bi bi-tags text-teal me-2"></i> Skill Categories</h6>
      </div>
      <div class="card-body p-3">
        <!-- Create Category Form -->
        <form action="" method="POST" class="mb-3 border-bottom pb-3">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="add_category">
          
          <div class="mb-2">
            <label for="category_name" class="form-label small fw-semibold">New Category Name</label>
            <input type="text" class="form-control form-control-sm" id="category_name" name="category_name" required placeholder="e.g. Web Development">
          </div>
          <div class="mb-2">
            <label for="category_order" class="form-label small fw-semibold">Order</label>
            <input type="number" class="form-control form-control-sm" id="category_order" name="category_order" value="10">
          </div>
          <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
            <i class="bi bi-plus-lg me-1"></i> Add Category
          </button>
        </form>

        <!-- Categories List -->
        <ul class="list-group list-group-flush small">
          <?php foreach ($categories as $cat): ?>
            <li class="list-group-item d-flex align-items-center justify-content-between px-0 py-2">
              <div>
                <span class="fw-bold"><?= e($cat['name']) ?></span>
                <span class="text-muted extra-small ms-1">(Order: <?= (int)$cat['display_order'] ?>)</span>
              </div>
              <form action="" method="POST" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-sm text-danger border-0 p-0 btn-delete-confirm" data-item-title="<?= e($cat['name']) ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
