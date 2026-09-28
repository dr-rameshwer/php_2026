<?php
/**
 * BCA Student Management System
 * Logout Controller
 * 
 * Purpose: Safely terminates the user session, clears cookies,
 * queues a flash notification, and redirects to login.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

// Destroy session
logoutUser();

// Start fresh session solely to carry the flash notification
session_start();
setFlash('info', 'You have been logged out securely. See you soon!');

header("Location: login.php");
exit();
