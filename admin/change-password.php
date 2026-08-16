<?php
/**
 * Admin Change Password
 */
$pageTitle = 'Change Password';
$activePage = 'password';

require_once __DIR__ . '/includes/admin-header.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        set_flash('danger', 'Please fill in all password fields.');
    } elseif ($newPassword !== $confirmPassword) {
        set_flash('danger', 'New password and confirmation password do not match.');
    } elseif (strlen($newPassword) < 8) {
        set_flash('danger', 'New password must be at least 8 characters long.');
    } else {
        // Fetch current user hash
        $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ? LIMIT 1");
        $stmt->execute([$user['id']]);
        $dbUser = $stmt->fetch();

        if ($dbUser && password_verify($currentPassword, $dbUser['password_hash'])) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $user['id']]);

            set_flash('success', 'Your password has been changed successfully!');
            redirect(BASE_URL . '/admin/change-password.php');
        } else {
            set_flash('danger', 'Current password is incorrect.');
        }
    }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 fw-bold"><i class="bi bi-shield-lock text-teal me-2"></i> Update Admin Password</h5>
      </div>
      <div class="card-body p-4">
        <form action="" method="POST">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="current_password" class="form-label fw-semibold">Current Password *</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Enter current password">
          </div>

          <div class="mb-3">
            <label for="new_password" class="form-label fw-semibold">New Password *</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8" placeholder="Minimum 8 characters">
          </div>

          <div class="mb-4">
            <label for="confirm_password" class="form-label fw-semibold">Confirm New Password *</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter new password">
          </div>

          <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color:#0f766e; border-color:#0f766e;">
            <i class="bi bi-key me-1"></i> Change Password
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
