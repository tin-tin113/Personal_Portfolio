<?php
/**
 * QA & Automated Verification Test Suite
 */
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/csrf.php';

echo "===== QA & VERIFICATION TEST SUITE =====\n";

$passCount = 0;
$failCount = 0;

function assert_test(bool $condition, string $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test Admin Login
$loginRes = attempt_login('admin', 'AdminPassword123!');
assert_test($loginRes['success'] === true, 'Admin Login with default credentials (admin / AdminPassword123!)');

// 2. Test Invalid Login
$invalidRes = attempt_login('admin', 'WrongPassword!');
assert_test($invalidRes['success'] === false, 'Invalid Login rejection');

// 3. Test Profile Fetching
$profile = get_profile();
assert_test(!empty($profile['full_name']), 'Fetch Profile details from MySQL database');

// 4. Test CSRF Token Generation
$token = csrf_token();
assert_test(!empty($token) && strlen($token) === 64, 'CSRF Token Generation (hex string length 64)');

// 5. Test XSS Helper
$xssInput = '<script>alert("xss")</script>';
$escaped = e($xssInput);
assert_test($escaped === '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', 'XSS Output Escaping via e() helper');

// 6. Test Slugify
$slug = slugify('E-Commerce Inventory & Order System 2026');
assert_test($slug === 'e-commerce-inventory-order-system-2026', 'URL Slugify function generator');

// 7. Test Database Querying (Projects)
$pdo = db();
$projects = $pdo->query("SELECT * FROM projects WHERE is_published = 1")->fetchAll();
assert_test(count($projects) >= 3, 'Fetch published projects from MySQL database (>= 3 items)');

// 8. Test Database Querying (Skills)
$skills = $pdo->query("SELECT * FROM skills WHERE is_active = 1")->fetchAll();
assert_test(count($skills) >= 10, 'Fetch active skills from MySQL database (>= 10 items)');

echo "----------------------------------------\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================\n";
