<?php
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

$pdo = db();

$name = 'Martin Diasanta';
$linkedin = 'https://www.linkedin.com/in/martin-diasanta-4b5200279';

// Update profile table
$stmt = $pdo->prepare("UPDATE profile SET full_name = ?, linkedin_url = ? WHERE id = 1");
$stmt->execute([$name, $linkedin]);

// Update site settings
$stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'site_title'");
$stmt->execute(['Martin Diasanta | Portfolio']);

// Update external links
$stmt = $pdo->prepare("UPDATE external_links SET url = ? WHERE name = 'LinkedIn'");
$stmt->execute([$linkedin]);

echo "Profile updated with Martin Diasanta & LinkedIn URL.\n";
