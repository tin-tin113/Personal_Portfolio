<?php
/**
 * Contact Form Submission Processor
 */
require_once __DIR__ . '/config/app.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/index.php#contact');
}

require_csrf_token();

// Spam Honeypot Protection
if (!empty($_POST['website_hp'])) {
    // Silent discard for automated bot submissions
    set_flash('success', 'Thank you! Your message has been sent successfully.');
    redirect(BASE_URL . '/index.php#contact');
}

$name    = sanitize_input($_POST['name'] ?? '');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

if (empty($name) || !$email || empty($subject) || empty($message)) {
    set_flash('danger', 'Please provide a valid name, email address, subject, and message.');
    redirect(BASE_URL . '/index.php#contact');
}

// In production, mail() or SMTP dispatch would occur here.
// For Laragon / local execution, we simulate clean receipt and flash feedback.

set_flash('success', 'Thank you, ' . e($name) . '! Your message has been sent. I will respond to your email shortly.');
redirect(BASE_URL . '/index.php#contact');
