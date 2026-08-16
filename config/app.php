<?php
/**
 * Application Configuration
 * Personal Portfolio & Private Admin CMS
 */

// Prevent direct script access
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// Environment: 'development' or 'production'
define('APP_ENV', 'development');

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Root Directory Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Base URL calculation (Supports Laragon subfolder or custom virtual host)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$dirName = dirname($scriptName);
$dirName = str_replace('\\', '/', $dirName);
if ($dirName === '/' || $dirName === '.') {
    $baseUrl = '';
} else {
    // If inside /admin, strip the /admin prefix to find base URL
    if (str_contains($dirName, '/admin')) {
        $baseUrl = preg_replace('/\/admin.*$/', '', $dirName);
    } else {
        $baseUrl = rtrim($dirName, '/');
    }
}
define('BASE_URL', $baseUrl);
define('UPLOADS_URL', BASE_URL . '/uploads');

// Upload Limits & Whitelists
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_DOC_SIZE', 10 * 1024 * 1024); // 10MB

$ALLOWED_IMAGE_MIMES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif'
];

$ALLOWED_DOC_MIMES = [
    'application/pdf' => 'pdf',
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'image/webp'      => 'webp'
];

define('ALLOWED_IMAGE_MIMES', $ALLOWED_IMAGE_MIMES);
define('ALLOWED_DOC_MIMES', $ALLOWED_DOC_MIMES);

// Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    // Set secure cookie attributes
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path'     => $baseUrl ?: '/',
        'domain'   => $_SERVER['HTTP_HOST'] ?? '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
