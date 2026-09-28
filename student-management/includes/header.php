<?php
/**
 * BCA Student Management System
 * Global Layout Header Component
 * 
 * Purpose: Provides unified HTML head, Bootstrap 5 styling, meta tags,
 * responsive top navigation bar, and automatic flash notification rendering.
 */

declare(strict_types=1);

// Ensure session and helper functions are available
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/flash.php';

// Detect whether the current script resides in public/ or a subfolder like public/students/
$currentScriptDir = basename(dirname($_SERVER['PHP_SELF']));
$inSubfolder = ($currentScriptDir === 'students');
$rootPath = $inSubfolder ? '../' : './';
$assetPath = $inSubfolder ? '../../assets/' : '../assets/';

// Determine current page for active navbar highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = $pageTitle ?? 'BCA Student Management System';
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons 1.11.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Application CSS -->
    <link rel="stylesheet" href="<?php echo e($assetPath); ?>css/style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-ptu-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="<?php echo $currentUser ? $rootPath . 'dashboard.php' : $rootPath . 'index.php'; ?>">
                <i class="bi bi-mortarboard-fill fs-3 me-2 text-warning"></i>
                <div>
                    <span class="d-block lh-1">University BCA</span>
                    <small class="text-white-50 fs-6 fw-normal">Student Management System</small>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <?php if ($currentUser): ?>
                    <!-- Navigation Links for Authenticated Users -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage === 'dashboard.php') ? 'active fw-semibold' : ''; ?>" href="<?php echo $rootPath; ?>dashboard.php">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($inSubfolder && $currentPage === 'index.php') ? 'active fw-semibold' : ''; ?>" href="<?php echo $rootPath; ?>students/index.php">
                                <i class="bi bi-people-fill me-1"></i> All Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage === 'create.php') ? 'active fw-semibold' : ''; ?>" href="<?php echo $rootPath; ?>students/create.php">
                                <i class="bi bi-person-plus-fill me-1"></i> Add Student
                            </a>
                        </li>
                    </ul>

                    <!-- User Account / Logout -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle-sm bg-warning text-dark fw-bold me-2">
                                    <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                                </div>
                                <span><?php echo e($currentUser['name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="px-3 py-2 text-muted small border-bottom">
                                    Signed in as<br>
                                    <strong class="text-dark"><?php echo e($currentUser['email']); ?></strong>
                                </li>
                                <li><a class="dropdown-item" href="<?php echo $rootPath; ?>dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo $rootPath; ?>logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Links for Guests -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage === 'login.php') ? 'active fw-semibold' : ''; ?>" href="<?php echo $rootPath; ?>login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Admin Login
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container with Flash Notifications -->
    <main class="container my-4 flex-grow-1">
        <?php renderFlash(); ?>
