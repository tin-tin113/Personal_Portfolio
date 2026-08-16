<?php
/**
 * Admin CMS Template Header
 */
if (!defined('APP_INIT')) {
    require_once dirname(__DIR__, 2) . '/config/app.php';
}

require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/csrf.php';

// Enforce login for all admin pages using this header
require_login();

$user = current_user();
$activePage = $activePage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Admin CMS') ?> | Portfolio Admin</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Admin Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

  <!-- Admin Sidebar Navigation -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
      <i class="bi bi-speedometer2 text-teal"></i>
      <span>Portfolio Admin</span>
    </div>

    <ul class="admin-sidebar-menu">
      <li class="admin-menu-heading">Main</li>
      <li>
        <a href="<?= BASE_URL ?>/admin/index.php" class="admin-sidebar-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/profile.php" class="admin-sidebar-link <?= $activePage === 'profile' ? 'active' : '' ?>">
          <i class="bi bi-person-badge-fill"></i>
          <span>Profile & Bio</span>
        </a>
      </li>

      <li class="admin-menu-heading">Portfolio Content</li>
      <li>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="admin-sidebar-link <?= $activePage === 'projects' ? 'active' : '' ?>">
          <i class="bi bi-folder-fill"></i>
          <span>Projects</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/skills.php" class="admin-sidebar-link <?= $activePage === 'skills' ? 'active' : '' ?>">
          <i class="bi bi-cpu-fill"></i>
          <span>Skills & Categories</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/experience.php" class="admin-sidebar-link <?= $activePage === 'experience' ? 'active' : '' ?>">
          <i class="bi bi-briefcase-fill"></i>
          <span>Experience</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/education.php" class="admin-sidebar-link <?= $activePage === 'education' ? 'active' : '' ?>">
          <i class="bi bi-mortarboard-fill"></i>
          <span>Education</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/certifications.php" class="admin-sidebar-link <?= $activePage === 'certifications' ? 'active' : '' ?>">
          <i class="bi bi-patch-check-fill"></i>
          <span>Certifications</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/licenses.php" class="admin-sidebar-link <?= $activePage === 'licenses' ? 'active' : '' ?>">
          <i class="bi bi-award-fill"></i>
          <span>Licenses & Credentials</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/links.php" class="admin-sidebar-link <?= $activePage === 'links' ? 'active' : '' ?>">
          <i class="bi bi-link-45deg"></i>
          <span>Links & Socials</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/media.php" class="admin-sidebar-link <?= $activePage === 'media' ? 'active' : '' ?>">
          <i class="bi bi-images"></i>
          <span>Media Library</span>
        </a>
      </li>

      <li class="admin-menu-heading">System</li>
      <li>
        <a href="<?= BASE_URL ?>/admin/site-settings.php" class="admin-sidebar-link <?= $activePage === 'settings' ? 'active' : '' ?>">
          <i class="bi bi-gear-fill"></i>
          <span>Site Settings</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/change-password.php" class="admin-sidebar-link <?= $activePage === 'password' ? 'active' : '' ?>">
          <i class="bi bi-shield-lock-fill"></i>
          <span>Change Password</span>
        </a>
      </li>
    </ul>

    <div class="p-3 border-top border-secondary text-center">
      <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-sm btn-outline-light w-100 mb-2">
        <i class="bi bi-box-arrow-up-right me-1"></i> View Public Site
      </a>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-sm btn-danger w-100">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </a>
    </div>
  </aside>

  <!-- Main Wrapper -->
  <div class="admin-main-wrapper">
    <!-- Top Bar -->
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <button id="sidebar-toggle-btn" class="btn btn-sm btn-outline-secondary d-lg-none" aria-label="Toggle Sidebar">
          <i class="bi bi-list fs-5"></i>
        </button>
        <h5 class="m-0 fw-bold"><?= e($pageTitle ?? 'Dashboard') ?></h5>
      </div>

      <div class="d-flex align-items-center gap-3">
        <span class="text-muted small d-none d-md-inline">
          <i class="bi bi-person-circle me-1"></i> Logged in as <strong><?= e($user['username'] ?? 'Admin') ?></strong>
        </span>
      </div>
    </header>

    <!-- Main Content -->
    <main class="admin-content">
      <?php render_flash_messages(); ?>
