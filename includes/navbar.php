<?php
/**
 * Public Navbar Template Component
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$firstInitial = get_monogram($profile['full_name'] ?? 'P');
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container">
    <!-- Brand Logo / Monogram -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
      <span class="navbar-brand-avatar">
        <?= e($firstInitial) ?>
      </span>
      <span class="fw-bold"><?= e($profile['full_name'] ?? '[Your Name]') ?></span>
    </a>

    <!-- Action Toolbar (Theme Toggle + Resume Button + Mobile Toggler) -->
    <div class="d-flex align-items-center gap-2 order-lg-last">
      <!-- Dark / Light Mode Toggle Button -->
      <button id="theme-toggle-btn" class="btn btn-theme-toggle" aria-label="Toggle Theme" title="Toggle Theme">
        <i class="bi bi-moon-stars-fill fs-6"></i>
      </button>

      <?php if (!empty($profile['resume_file'])): ?>
        <a href="<?= BASE_URL ?>/resume.php" target="_blank" class="btn btn-sm btn-primary-custom d-none d-sm-inline-flex align-items-center gap-1">
          <i class="bi bi-file-earmark-arrow-down"></i> Resume
        </a>
      <?php endif; ?>

      <!-- Mobile Hamburger Button -->
      <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <!-- Navigation Menu Links -->
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-semibold">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/index.php#skills">Skills</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage === 'projects.php' || $currentPage === 'project.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/projects.php">Projects</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'certifications.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/certifications.php">Certifications</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/index.php#experience">Experience</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/index.php#education">Education</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/index.php#contact">Contact</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
