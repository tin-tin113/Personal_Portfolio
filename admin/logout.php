<?php
/**
 * Admin Logout Handler
 */
require_once dirname(__DIR__) . '/config/app.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';

logout_admin();

set_flash('info', 'You have been successfully logged out.');
redirect(BASE_URL . '/admin/login.php');
