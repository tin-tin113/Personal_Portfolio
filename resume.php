<?php
/**
 * Resume Download & Preview Handler
 */
require_once __DIR__ . '/config/app.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$profile = get_profile();
$resumePath = $profile['resume_file'] ?? '';

if (empty($resumePath)) {
    http_response_code(404);
    die('<div style="font-family:sans-serif; text-align:center; padding:50px;"><h2>No Resume Uploaded</h2><p>Please upload a resume document through the Admin CMS.</p><a href="' . BASE_URL . '/index.php">Return Home</a></div>');
}

$cleanPath = str_replace(['..', '\\'], ['', '/'], $resumePath);
$fullPath  = ROOT_PATH . '/' . ltrim($cleanPath, '/');

if (!file_exists($fullPath)) {
    http_response_code(404);
    die('<div style="font-family:sans-serif; text-align:center; padding:50px;"><h2>Resume File Not Found</h2><p>The requested file does not exist on disk.</p><a href="' . BASE_URL . '/index.php">Return Home</a></div>');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: inline; filename="Resume_' . slugify($profile['full_name']) . '.pdf"');
header('Cache-Control: private, max-age=86400, must-revalidate');

readfile($fullPath);
exit;
