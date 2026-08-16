<?php
/**
 * Public Site Header Template
 */
if (!defined('APP_INIT')) {
    require_once dirname(__DIR__) . '/config/app.php';
}

require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/csrf.php';

$profile = get_profile();
$siteTitle = site_setting('site_title', '[Your Name] | Portfolio');
$siteDesc = site_setting('site_description', 'Full-Stack Developer & Quality Assurance Portfolio');
$accentColor = site_setting('accent_color', '#0f766e');
$pageTitle = isset($customPageTitle) ? e($customPageTitle) . ' | ' . e($siteTitle) : e($siteTitle);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>

  <meta name="description" content="<?= e($siteDesc) ?>">
  <meta name="keywords" content="<?= e(site_setting('seo_keywords', 'Full-Stack Developer, PHP, MySQL, Quality Assurance')) ?>">
  <meta name="author" content="<?= e($profile['full_name']) ?>">

  <!-- Open Graph / Social Media -->
  <meta property="og:title" content="<?= $pageTitle ?>">
  <meta property="og:description" content="<?= e($siteDesc) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
  <?php if (!empty($profile['profile_image'])): ?>
    <meta property="og:image" content="<?= BASE_URL . '/' . e($profile['profile_image']) ?>">
  <?php endif; ?>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Public Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

  <style>
    :root {
      --primary-color: <?= e($accentColor) ?>;
    }
  </style>
</head>
<body>
