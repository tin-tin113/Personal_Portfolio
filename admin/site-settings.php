<?php
/**
 * Admin Site Settings Configuration
 */
$pageTitle = 'Global Site Settings';
$activePage = 'settings';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $settingsToUpdate = [
        'site_title'         => sanitize_input($_POST['site_title'] ?? ''),
        'site_description'   => sanitize_input($_POST['site_description'] ?? ''),
        'accent_color'       => sanitize_input($_POST['accent_color'] ?? '#0f766e'),
        'contact_email'      => sanitize_input($_POST['contact_email'] ?? ''),
        'footer_text'        => sanitize_input($_POST['footer_text'] ?? ''),
        'copyright_text'     => sanitize_input($_POST['copyright_text'] ?? ''),
        'seo_keywords'       => sanitize_input($_POST['seo_keywords'] ?? ''),
        'availability_status'=> sanitize_input($_POST['availability_status'] ?? '')
    ];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($settingsToUpdate as $key => $val) {
            $stmt->execute([$key, $val]);
        }

        set_flash('success', 'Site settings saved successfully!');
        redirect(BASE_URL . '/admin/site-settings.php');

    } catch (Exception $e) {
        error_log("Failed to save settings: " . $e->getMessage());
        set_flash('danger', 'Database error saving settings.');
    }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 fw-bold"><i class="bi bi-gear text-teal me-2"></i> Configure Global Site &amp; SEO Settings</h5>
      </div>
      <div class="card-body p-4">
        <form action="" method="POST">
          <?= csrf_field() ?>

          <div class="row g-3">
            <!-- Site Title -->
            <div class="col-12 col-md-8">
              <label for="site_title" class="form-label fw-semibold">Browser Title / Site Name</label>
              <input type="text" class="form-control" id="site_title" name="site_title" value="<?= e(site_setting('site_title')) ?>" required>
            </div>

            <!-- Accent Color -->
            <div class="col-12 col-md-4">
              <label for="accent_color" class="form-label fw-semibold">Primary Accent Color</label>
              <div class="input-group">
                <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" value="<?= e(site_setting('accent_color', '#0f766e')) ?>" title="Choose accent color">
                <input type="text" class="form-control font-monospace" value="<?= e(site_setting('accent_color', '#0f766e')) ?>" readonly>
              </div>
            </div>

            <!-- Site Description -->
            <div class="col-12">
              <label for="site_description" class="form-label fw-semibold">Meta Description (SEO)</label>
              <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= e(site_setting('site_description')) ?></textarea>
            </div>

            <!-- SEO Keywords -->
            <div class="col-12 col-md-6">
              <label for="seo_keywords" class="form-label fw-semibold">Meta Keywords (Comma-separated)</label>
              <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?= e(site_setting('seo_keywords')) ?>" placeholder="PHP, MySQL, Quality Assurance, Full-Stack">
            </div>

            <!-- Contact Email -->
            <div class="col-12 col-md-6">
              <label for="contact_email" class="form-label fw-semibold">Recipient Contact Form Email</label>
              <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= e(site_setting('contact_email')) ?>">
            </div>

            <!-- Footer Text -->
            <div class="col-12">
              <label for="footer_text" class="form-label fw-semibold">Footer Philosophy Text</label>
              <input type="text" class="form-control" id="footer_text" name="footer_text" value="<?= e(site_setting('footer_text')) ?>">
            </div>

            <!-- Copyright Text -->
            <div class="col-12 col-md-6">
              <label for="copyright_text" class="form-label fw-semibold">Copyright Notice</label>
              <input type="text" class="form-control" id="copyright_text" name="copyright_text" value="<?= e(site_setting('copyright_text')) ?>">
            </div>

            <!-- Availability Status -->
            <div class="col-12 col-md-6">
              <label for="availability_status" class="form-label fw-semibold">Global Status Headline</label>
              <input type="text" class="form-control" id="availability_status" name="availability_status" value="<?= e(site_setting('availability_status')) ?>">
            </div>

          </div>

          <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
              <i class="bi bi-save me-1"></i> Save Settings
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
