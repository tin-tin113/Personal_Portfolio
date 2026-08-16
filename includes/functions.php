<?php
/**
 * Utility & Helper Functions
 * Personal Portfolio & Private Admin CMS
 */

if (!defined('APP_INIT')) {
    require_once dirname(__DIR__) . '/config/app.php';
}

require_once CONFIG_PATH . '/database.php';

/**
 * Escape HTML output for XSS prevention
 */
function e(?string $value): string {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize string input
 */
function sanitize_input(?string $data): string {
    if ($data === null) {
        return '';
    }
    return trim(strip_tags($data));
}

/**
 * Generate a URL-friendly slug
 */
function slugify(string $text): string {
    // Replace non-alphanumeric characters with hyphen
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim hyphens
    $text = trim($text, '-');
    // Lowercase
    $text = strtolower($text);

    return empty($text) ? 'n-a-' . time() : $text;
}

/**
 * Safe HTTP Redirect
 */
function redirect(string $url): void {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    }
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . e($url) . '"></noscript>';
    exit;
}

/**
 * Flash Messaging System
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'text' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function render_flash_messages(): void {
    $flash = get_flash();
    if ($flash) {
        $type = e($flash['type']);
        $text = e($flash['text']);
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show shadow-sm" role="alert">';
        echo $text;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Retrieve Site Setting with fallback
 */
function site_setting(string $key, string $default = ''): string {
    static $settingsCache = null;

    if ($settingsCache === null) {
        $settingsCache = [];
        try {
            $stmt = db()->query("SELECT setting_key, setting_value FROM site_settings");
            while ($row = $stmt->fetch()) {
                $settingsCache[$row['setting_key']] = $row['setting_value'] ?? '';
            }
        } catch (Exception $e) {
            error_log("Failed to load site settings: " . $e->getMessage());
        }
    }

    return $settingsCache[$key] ?? $default;
}

/**
 * Fetch Main Profile Details
 */
function get_profile(): array {
    static $profileCache = null;

    if ($profileCache === null) {
        try {
            $stmt = db()->query("SELECT * FROM profile ORDER BY id ASC LIMIT 1");
            $profileCache = $stmt->fetch() ?: [
                'full_name' => '[Your Name]',
                'professional_title' => 'AI-Assisted Full-Stack Developer',
                'tagline' => 'Building reliable web applications',
                'short_summary' => 'Full-Stack Developer, Quality Assurance & Admin Specialist',
                'about_me' => 'Welcome to my portfolio.',
                'email' => 'contact@example.com',
                'phone' => '',
                'location' => '',
                'profile_image' => '',
                'resume_file' => '',
                'availability_status' => 'Available for Opportunities',
                'github_url' => '',
                'linkedin_url' => ''
            ];
        } catch (Exception $e) {
            error_log("Failed to fetch profile: " . $e->getMessage());
            $profileCache = [];
        }
    }

    return $profileCache;
}

/**
 * Date Formatter
 */
function format_date(?string $dateStr, string $format = 'M Y'): string {
    if (empty($dateStr)) {
        return '';
    }
    $timestamp = strtotime($dateStr);
    return $timestamp ? date($format, $timestamp) : $dateStr;
}

/**
 * URL Validator
 */
function is_valid_url(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Extract clean single-character monogram for avatar placeholder
 */
function get_monogram(?string $name): string {
    if (empty($name)) {
        return 'P';
    }
    $clean = preg_replace('/[^a-zA-Z0-9]/', '', $name);
    return !empty($clean) ? strtoupper(substr($clean, 0, 1)) : 'P';
}
