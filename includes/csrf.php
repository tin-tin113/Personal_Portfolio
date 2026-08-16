<?php
/**
 * CSRF Protection Utilities
 * Personal Portfolio & Private Admin CMS
 */

if (!defined('APP_INIT')) {
    require_once dirname(__DIR__) . '/config/app.php';
}

/**
 * Generate CSRF Token for current session if not exists
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output HTML hidden CSRF input field
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF Token submitted via POST or Header
 */
function verify_csrf_token(): bool {
    $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($submittedToken) || empty($sessionToken)) {
        return false;
    }

    return hash_equals($sessionToken, $submittedToken);
}

/**
 * Enforce CSRF Token validation or abort execution
 */
function require_csrf_token(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token()) {
            http_response_code(403);
            die('<div style="font-family:sans-serif; padding:30px; text-align:center; max-width:500px; margin:50px auto; border:1px solid #f87171; background:#fef2f2; color:#991b1b; border-radius:8px;">' .
                '<h2>403 Forbidden - Invalid CSRF Token</h2>' .
                '<p>Your session request could not be authenticated. Please refresh the page and try submitting again.</p>' .
                '<a href="javascript:history.back()" style="display:inline-block; padding:8px 16px; background:#991b1b; color:#fff; text-decoration:none; border-radius:4px;">Go Back</a>' .
                '</div>');
        }
    }
}
