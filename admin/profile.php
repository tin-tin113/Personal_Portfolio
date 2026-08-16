<?php
/**
 * Admin Profile & Bio Management
 */
$pageTitle = 'Edit Profile & Bio';
$activePage = 'profile';

require_once __DIR__ . '/includes/admin-header.php';
require_once INCLUDES_PATH . '/upload.php';

$pdo = db();
$profile = get_profile();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $fullName          = sanitize_input($_POST['full_name'] ?? '');
    $profTitle         = sanitize_input($_POST['professional_title'] ?? '');
    $tagline           = sanitize_input($_POST['tagline'] ?? '');
    $shortSummary      = sanitize_input($_POST['short_summary'] ?? '');
    $aboutMe           = trim($_POST['about_me'] ?? '');
    $email             = sanitize_input($_POST['email'] ?? '');
    $phone             = sanitize_input($_POST['phone'] ?? '');
    $location          = sanitize_input($_POST['location'] ?? '');
    $availability      = sanitize_input($_POST['availability_status'] ?? '');
    $githubUrl         = sanitize_input($_POST['github_url'] ?? '');
    $linkedinUrl       = sanitize_input($_POST['linkedin_url'] ?? '');

    $profileImage = $profile['profile_image'] ?? '';
    $resumeFile   = $profile['resume_file'] ?? '';

    // Handle Profile Image Upload
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadResult = handle_file_upload($_FILES['profile_image'], 'avatars', 'image', $profileImage);
        if ($uploadResult['success']) {
            $profileImage = $uploadResult['filepath'];
        } else {
            set_flash('danger', 'Profile image upload failed: ' . $uploadResult['error']);
            redirect(BASE_URL . '/admin/profile.php');
        }
    }

    // Handle Resume / CV Upload
    if (!empty($_FILES['resume_file']['name'])) {
        $docResult = handle_file_upload($_FILES['resume_file'], 'resume', 'doc', $resumeFile);
        if ($docResult['success']) {
            $resumeFile = $docResult['filepath'];
        } else {
            set_flash('danger', 'Resume upload failed: ' . $docResult['error']);
            redirect(BASE_URL . '/admin/profile.php');
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE profile SET
                full_name = ?,
                professional_title = ?,
                tagline = ?,
                short_summary = ?,
                about_me = ?,
                email = ?,
                phone = ?,
                location = ?,
                profile_image = ?,
                resume_file = ?,
                availability_status = ?,
                github_url = ?,
                linkedin_url = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $fullName,
            $profTitle,
            $tagline,
            $shortSummary,
            $aboutMe,
            $email,
            $phone,
            $location,
            $profileImage,
            $resumeFile,
            $availability,
            $githubUrl,
            $linkedinUrl,
            $profile['id']
        ]);

        set_flash('success', 'Profile information updated successfully!');
        redirect(BASE_URL . '/admin/profile.php');

    } catch (Exception $e) {
        error_log("Failed to update profile: " . $e->getMessage());
        set_flash('danger', 'Database error updating profile.');
    }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 fw-bold"><i class="bi bi-person-gear text-teal me-2"></i> Manage Personal Profile &amp; Resume</h5>
      </div>
      <div class="card-body p-4">

        <form action="" method="POST" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="row g-3">
            <!-- Full Name -->
            <div class="col-12 col-md-6">
              <label for="full_name" class="form-label fw-semibold">Full Name *</label>
              <input type="text" class="form-control" id="full_name" name="full_name" value="<?= e($profile['full_name'] ?? '') ?>" required>
            </div>

            <!-- Professional Title -->
            <div class="col-12 col-md-6">
              <label for="professional_title" class="form-label fw-semibold">Professional Title *</label>
              <input type="text" class="form-control" id="professional_title" name="professional_title" value="<?= e($profile['professional_title'] ?? '') ?>" required>
            </div>

            <!-- Tagline -->
            <div class="col-12">
              <label for="tagline" class="form-label fw-semibold">Tagline / Hero Subtitle</label>
              <input type="text" class="form-control" id="tagline" name="tagline" value="<?= e($profile['tagline'] ?? '') ?>">
            </div>

            <!-- Short Summary -->
            <div class="col-12">
              <label for="short_summary" class="form-label fw-semibold">Short Summary / Pitch</label>
              <textarea class="form-control" id="short_summary" name="short_summary" rows="2"><?= e($profile['short_summary'] ?? '') ?></textarea>
            </div>

            <!-- About Me -->
            <div class="col-12">
              <label for="about_me" class="form-label fw-semibold">Detailed About Me</label>
              <textarea class="form-control" id="about_me" name="about_me" rows="6"><?= e($profile['about_me'] ?? '') ?></textarea>
            </div>

            <!-- Email -->
            <div class="col-12 col-md-4">
              <label for="email" class="form-label fw-semibold">Public Contact Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= e($profile['email'] ?? '') ?>">
            </div>

            <!-- Phone -->
            <div class="col-12 col-md-4">
              <label for="phone" class="form-label fw-semibold">Phone Number</label>
              <input type="text" class="form-control" id="phone" name="phone" value="<?= e($profile['phone'] ?? '') ?>">
            </div>

            <!-- Location -->
            <div class="col-12 col-md-4">
              <label for="location" class="form-label fw-semibold">Location</label>
              <input type="text" class="form-control" id="location" name="location" value="<?= e($profile['location'] ?? '') ?>">
            </div>

            <!-- Availability Status -->
            <div class="col-12 col-md-4">
              <label for="availability_status" class="form-label fw-semibold">Availability Status Badge</label>
              <input type="text" class="form-control" id="availability_status" name="availability_status" value="<?= e($profile['availability_status'] ?? '') ?>">
            </div>

            <!-- GitHub URL -->
            <div class="col-12 col-md-4">
              <label for="github_url" class="form-label fw-semibold">GitHub Profile URL</label>
              <input type="url" class="form-control" id="github_url" name="github_url" value="<?= e($profile['github_url'] ?? '') ?>">
            </div>

            <!-- LinkedIn URL -->
            <div class="col-12 col-md-4">
              <label for="linkedin_url" class="form-label fw-semibold">LinkedIn Profile URL</label>
              <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" value="<?= e($profile['linkedin_url'] ?? '') ?>">
            </div>

            <!-- File Uploads Divider -->
            <div class="col-12 border-top my-3 pt-3">
              <h6 class="fw-bold text-teal"><i class="bi bi-paperclip me-1"></i> Avatar &amp; Document Uploads</h6>
            </div>

            <!-- Profile Image Upload -->
            <div class="col-12 col-md-6">
              <label for="profile_image" class="form-label fw-semibold">Profile Avatar Image</label>
              <?php if (!empty($profile['profile_image'])): ?>
                <div class="mb-2">
                  <img src="<?= BASE_URL . '/' . e($profile['profile_image']) ?>" id="avatar-preview" class="rounded-circle border" style="width:90px; height:90px; object-fit:cover;" alt="Avatar">
                </div>
              <?php else: ?>
                <div class="mb-2">
                  <img src="" id="avatar-preview" class="rounded-circle border" style="width:90px; height:90px; object-fit:cover; display:none;" alt="Avatar">
                </div>
              <?php endif; ?>
              <input type="file" class="form-control image-preview-input" data-preview-target="avatar-preview" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/webp">
              <div class="form-text extra-small">Max size 5MB (JPG, PNG, WEBP).</div>
            </div>

            <!-- Resume Upload -->
            <div class="col-12 col-md-6">
              <label for="resume_file" class="form-label fw-semibold">Resume / CV Document</label>
              <?php if (!empty($profile['resume_file'])): ?>
                <div class="mb-2">
                  <a href="<?= BASE_URL . '/' . e($profile['resume_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-pdf me-1"></i> View Current Uploaded Resume
                  </a>
                </div>
              <?php endif; ?>
              <input type="file" class="form-control" id="resume_file" name="resume_file" accept=".pdf,.doc,.docx,application/pdf">
              <div class="form-text extra-small">Max size 10MB (PDF format recommended).</div>
            </div>

          </div>

          <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
              <i class="bi bi-save me-1"></i> Save Profile Changes
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
