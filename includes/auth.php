<?php
/**
 * Authentication & Authorization Manager
 * Personal Portfolio & Private Admin CMS
 */

if (!defined('APP_INIT')) {
    require_once dirname(__DIR__) . '/config/app.php';
}

require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

/**
 * Check if Admin user is authenticated
 */
function is_logged_in(): bool {
    if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_user_id'])) {
        return false;
    }

    // Optional: Check session expiration (e.g. 8 hours inactivity)
    $maxInactivity = 8 * 3600;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $maxInactivity)) {
        logout_admin();
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Enforce Admin Login middleware
 */
function require_login(): void {
    if (!is_logged_in()) {
        set_flash('danger', 'Please log in to access the Admin CMS.');
        $loginUrl = BASE_URL . '/admin/login.php';
        redirect($loginUrl);
    }
}

/**
 * Get Current Admin User Array
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        try {
            $stmt = db()->prepare("SELECT id, username, email, last_login, created_at FROM admin_users WHERE id = ? LIMIT 1");
            $stmt->execute([$_SESSION['admin_user_id']]);
            $user = $stmt->fetch() ?: null;
        } catch (Exception $e) {
            error_log("Failed to fetch current admin user: " . $e->getMessage());
            $user = null;
        }
    }
    return $user;
}

/**
 * Attempt Admin Login with rate limiting / brute force throttle
 */
function attempt_login(string $identifier, string $password): array {
    $identifier = trim($identifier);

    if (empty($identifier) || empty($password)) {
        return ['success' => false, 'message' => 'Please fill in all login fields.'];
    }

    try {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username/email or password.'];
        }

        // Basic Brute Force Throttling Check (Lock out if > 5 failed attempts within 15 minutes)
        if ($user['failed_logins'] >= 5 && !empty($user['last_failed_login'])) {
            $lockoutWindow = 15 * 60; // 15 mins
            $timeSinceFailed = time() - strtotime($user['last_failed_login']);
            if ($timeSinceFailed < $lockoutWindow) {
                $remainingMins = ceil(($lockoutWindow - $timeSinceFailed) / 60);
                return [
                    'success' => false,
                    'message' => "Too many failed login attempts. Account temporarily locked. Please try again in {$remainingMins} minute(s)."
                ];
            }
        }

        // Verify Password
        if (password_verify($password, $user['password_hash'])) {
            // Reset failed login counter and set last_login
            $resetStmt = $pdo->prepare("UPDATE admin_users SET failed_logins = 0, last_failed_login = NULL, last_login = NOW() WHERE id = ?");
            $resetStmt->execute([$user['id']]);

            // Regenerate session ID for security against session fixation
            if (!headers_sent()) {
                session_regenerate_id(true);
            }

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['last_activity'] = time();

            return ['success' => true, 'message' => 'Login successful.'];
        } else {
            // Increment failed login count
            $failStmt = $pdo->prepare("UPDATE admin_users SET failed_logins = failed_logins + 1, last_failed_login = NOW() WHERE id = ?");
            $failStmt->execute([$user['id']]);

            return ['success' => false, 'message' => 'Invalid username/email or password.'];
        }

    } catch (Exception $e) {
        error_log("Login Exception: " . $e->getMessage());
        return ['success' => false, 'message' => 'An authentication error occurred. Please try again.'];
    }
}

/**
 * Destroy Admin Session & Logout
 */
function logout_admin(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}
