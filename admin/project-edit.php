<?php
/**
 * Admin Create / Edit Project Form
 */
$id = (int)($_GET['id'] ?? 0);
$isEditing = $id > 0;
$pageTitle = $isEditing ? 'Edit Project' : 'Create New Project';
$activePage = 'projects';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();

$project = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'short_description' => '',
    'detailed_description' => '',
    'project_image' => '',
    'my_role' => '',
    'key_features' => '',
    'challenges' => '',
    'solutions' => '',
    'development_methodology' => '',
    'project_year' => date('Y'),
    'github_url' => '',
    'live_demo_url' => '',
    'documentation_url' => '',
    'technologies_used' => '',
    'is_featured' => 0,
    'is_published' => 1,
    'display_order' => 10
];

if ($isEditing) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('danger', 'Project not found.');
        redirect(BASE_URL . '/admin/projects.php');
    }
    $project = array_merge($project, $found);
}

// Fetch existing screenshot gallery
$existingImages = [];
if ($isEditing) {
    $imgStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
    $imgStmt->execute([$id]);
    $existingImages = $imgStmt->fetchAll();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $title             = sanitize_input($_POST['title'] ?? '');
    $slug              = sanitize_input($_POST['slug'] ?? '');
    $shortDesc         = sanitize_input($_POST['short_description'] ?? '');
    $detailedDesc      = trim($_POST['detailed_description'] ?? '');
    $role              = sanitize_input($_POST['my_role'] ?? '');
    $keyFeatures       = trim($_POST['key_features'] ?? '');
    $challenges        = trim($_POST['challenges'] ?? '');
    $solutions         = trim($_POST['solutions'] ?? '');
    $methodology       = sanitize_input($_POST['development_methodology'] ?? '');
    $projectYear       = sanitize_input($_POST['project_year'] ?? '');
    $githubUrl         = sanitize_input($_POST['github_url'] ?? '');
    $liveDemoUrl       = sanitize_input($_POST['live_demo_url'] ?? '');
    $docUrl            = sanitize_input($_POST['documentation_url'] ?? '');
    $techUsed          = sanitize_input($_POST['technologies_used'] ?? '');
    $isFeatured        = isset($_POST['is_featured']) ? 1 : 0;
    $isPublished       = isset($_POST['is_published']) ? 1 : 0;
    $displayOrder      = (int)($_POST['display_order'] ?? 10);

    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    $projectImage = $project['project_image'];

    // Handle Main Project Cover Upload
    if (!empty($_FILES['project_image']['name'])) {
        $uploadRes = handle_file_upload($_FILES['project_image'], 'projects', 'image', $projectImage);
        if ($uploadRes['success']) {
            $projectImage = $uploadRes['filepath'];
        } else {
            set_flash('danger', 'Project image upload failed: ' . $uploadRes['error']);
            redirect($_SERVER['REQUEST_URI']);
        }
    }

    try {
        if ($isEditing) {
            $stmt = $pdo->prepare("
                UPDATE projects SET
                    title = ?,
                    slug = ?,
                    short_description = ?,
                    detailed_description = ?,
                    project_image = ?,
                    my_role = ?,
                    key_features = ?,
                    challenges = ?,
                    solutions = ?,
                    development_methodology = ?,
                    project_year = ?,
                    github_url = ?,
                    live_demo_url = ?,
                    documentation_url = ?,
                    technologies_used = ?,
                    is_featured = ?,
                    is_published = ?,
                    display_order = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $slug, $shortDesc, $detailedDesc, $projectImage,
                $role, $keyFeatures, $challenges, $solutions, $methodology,
                $projectYear, $githubUrl, $liveDemoUrl, $docUrl, $techUsed,
                $isFeatured, $isPublished, $displayOrder, $id
            ]);
            $targetId = $id;
            set_flash('success', 'Project updated successfully!');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO projects (
                    title, slug, short_description, detailed_description, project_image,
                    my_role, key_features, challenges, solutions, development_methodology,
                    project_year, github_url, live_demo_url, documentation_url, technologies_used,
                    is_featured, is_published, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $slug, $shortDesc, $detailedDesc, $projectImage,
                $role, $keyFeatures, $challenges, $solutions, $methodology,
                $projectYear, $githubUrl, $liveDemoUrl, $docUrl, $techUsed,
                $isFeatured, $isPublished, $displayOrder
            ]);
            $targetId = (int)$pdo->lastInsertId();
            set_flash('success', 'New project created successfully!');
        }

        // Handle Multiple Screenshots Upload if provided
        if (!empty($_FILES['gallery_images']['name'][0])) {
            $count = count($_FILES['gallery_images']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $_FILES['gallery_images']['name'][$i],
                        'type' => $_FILES['gallery_images']['type'][$i],
                        'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                        'error' => $_FILES['gallery_images']['error'][$i],
                        'size' => $_FILES['gallery_images']['size'][$i]
                    ];
                    $gRes = handle_file_upload($singleFile, 'projects', 'image');
                    if ($gRes['success']) {
                        $imgIns = $pdo->prepare("INSERT INTO project_images (project_id, image_path, display_order) VALUES (?, ?, ?)");
                        $imgIns->execute([$targetId, $gRes['filepath'], ($i + 1) * 10]);
                    }
                }
            }
        }

        redirect(BASE_URL . '/admin/projects.php');

    } catch (Exception $e) {
        error_log("Project save error: " . $e->getMessage());
        set_flash('danger', 'Database error saving project. Please check duplicate title/slug.');
    }
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1"><?= e($pageTitle) ?></h4>
    <p class="text-muted small m-0">Fill out the form below to <?= $isEditing ? 'update project details' : 'publish a new project' ?>.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Projects
  </a>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <form action="" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="row g-3">
        <!-- Title -->
        <div class="col-12 col-md-8">
          <label for="title" class="form-label fw-semibold">Project Title *</label>
          <input type="text" class="form-control" id="title" name="title" value="<?= e($project['title']) ?>" required>
        </div>

        <!-- Year -->
        <div class="col-12 col-md-4">
          <label for="project_year" class="form-label fw-semibold">Project Year</label>
          <input type="text" class="form-control" id="project_year" name="project_year" value="<?= e($project['project_year']) ?>">
        </div>

        <!-- Short Description -->
        <div class="col-12">
          <label for="short_description" class="form-label fw-semibold">Short Description (Summary Card) *</label>
          <input type="text" class="form-control" id="short_description" name="short_description" value="<?= e($project['short_description']) ?>" required maxlength="255">
        </div>

        <!-- Technologies Used -->
        <div class="col-12 col-md-8">
          <label for="technologies_used" class="form-label fw-semibold">Technologies Used (Comma-separated)</label>
          <input type="text" class="form-control" id="technologies_used" name="technologies_used" value="<?= e($project['technologies_used']) ?>" placeholder="e.g. PHP 8, MySQL, Bootstrap 5, Vanilla JS">
        </div>

        <!-- Role -->
        <div class="col-12 col-md-4">
          <label for="my_role" class="form-label fw-semibold">My Role</label>
          <input type="text" class="form-control" id="my_role" name="my_role" value="<?= e($project['my_role']) ?>" placeholder="e.g. Full-Stack Developer">
        </div>

        <!-- Detailed Description -->
        <div class="col-12">
          <label for="detailed_description" class="form-label fw-semibold">Detailed Description</label>
          <textarea class="form-control" id="detailed_description" name="detailed_description" rows="5"><?= e($project['detailed_description']) ?></textarea>
        </div>

        <!-- Key Features -->
        <div class="col-12 col-md-6">
          <label for="key_features" class="form-label fw-semibold">Key Features (Bullet Points)</label>
          <textarea class="form-control" id="key_features" name="key_features" rows="4" placeholder="• Feature 1&#10;• Feature 2"><?= e($project['key_features']) ?></textarea>
        </div>

        <!-- Challenges & Solutions -->
        <div class="col-12 col-md-6">
          <label for="challenges" class="form-label fw-semibold">Challenges &amp; Solutions</label>
          <textarea class="form-control" id="challenges" name="challenges" rows="4" placeholder="Describe technical challenges solved..."><?= e($project['challenges']) ?></textarea>
        </div>

        <!-- Development Methodology -->
        <div class="col-12 col-md-6">
          <label for="development_methodology" class="form-label fw-semibold">Development Methodology / Approach</label>
          <input type="text" class="form-control" id="development_methodology" name="development_methodology" value="<?= e($project['development_methodology']) ?>" placeholder="e.g. AI-Assisted Agile Iterative Workflow">
        </div>

        <!-- URLs -->
        <div class="col-12 col-md-6">
          <label for="github_url" class="form-label fw-semibold">GitHub Repository URL</label>
          <input type="url" class="form-control" id="github_url" name="github_url" value="<?= e($project['github_url']) ?>" placeholder="https://github.com/username/repo">
        </div>

        <div class="col-12 col-md-6">
          <label for="live_demo_url" class="form-label fw-semibold">Live Demo URL</label>
          <input type="url" class="form-control" id="live_demo_url" name="live_demo_url" value="<?= e($project['live_demo_url']) ?>" placeholder="https://demo.example.com">
        </div>

        <div class="col-12 col-md-6">
          <label for="documentation_url" class="form-label fw-semibold">Documentation URL</label>
          <input type="url" class="form-control" id="documentation_url" name="documentation_url" value="<?= e($project['documentation_url']) ?>" placeholder="https://docs.example.com">
        </div>

        <!-- Main Image Upload -->
        <div class="col-12 col-md-6">
          <label for="project_image" class="form-label fw-semibold">Main Project Cover Image</label>
          <?php if (!empty($project['project_image'])): ?>
            <div class="mb-2">
              <img src="<?= BASE_URL . '/' . e($project['project_image']) ?>" id="cover-preview" class="rounded border" style="max-height:120px; object-fit:cover;" alt="Cover">
            </div>
          <?php else: ?>
            <div class="mb-2">
              <img src="" id="cover-preview" class="rounded border" style="max-height:120px; object-fit:cover; display:none;" alt="Cover">
            </div>
          <?php endif; ?>
          <input type="file" class="form-control image-preview-input" data-preview-target="cover-preview" id="project_image" name="project_image" accept="image/jpeg,image/png,image/webp">
          <div class="form-text extra-small">Max size 5MB (JPG, PNG, WEBP).</div>
        </div>

        <!-- Gallery Upload -->
        <div class="col-12 col-md-6">
          <label for="gallery_images" class="form-label fw-semibold">Additional Screenshots (Gallery)</label>
          <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/webp">
          <div class="form-text extra-small">Select multiple images to attach to screenshot gallery.</div>
        </div>

        <!-- Display Order & Flags -->
        <div class="col-12 col-md-4">
          <label for="display_order" class="form-label fw-semibold">Display Order (Numeric)</label>
          <input type="number" class="form-control" id="display_order" name="display_order" value="<?= (int)$project['display_order'] ?>">
        </div>

        <div class="col-12 col-md-4 d-flex align-items-center pt-4">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $project['is_featured'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_featured">Mark as Featured Project</label>
          </div>
        </div>

        <div class="col-12 col-md-4 d-flex align-items-center pt-4">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $project['is_published'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-semibold" for="is_published">Published (Visible Publicly)</label>
          </div>
        </div>

      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
          <i class="bi bi-save me-1"></i> <?= $isEditing ? 'Save Changes' : 'Create Project' ?>
        </button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
