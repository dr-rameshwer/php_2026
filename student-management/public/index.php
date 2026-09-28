<?php
/**
 * Student Management System
 * Application Landing Page
 * 
 * Purpose: Directs traffic to the appropriate starting point. If the user
 * is already logged in, redirects to dashboard; otherwise redirects to login.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
