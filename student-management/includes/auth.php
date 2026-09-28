<?php
/**
 * BCA Student Management System
 * Authentication & Session Helper File
 * 
 * Purpose: Provides centralized, reusable authentication verification,
 * session lifecycle management, and route guarding.
 */

declare(strict_types=1);

// Start PHP session if not already active
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,              // Session cookie lasts until browser is closed
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,          // Set to true if running over HTTPS in production
        'httponly' => true,           // Inaccessible to client-side JavaScript (XSS defense)
        'samesite' => 'Lax'           // Protects against CSRF in cross-site requests
    ]);
    session_start();
}

/**
 * Checks whether an administrator user is currently logged in.
 *
 * @return bool True if logged in, false otherwise.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Enforces route authorization. If the visitor is not logged in,
 * they are redirected to the login page immediately.
 *
 * @param string $loginUrl Relative path to login page.
 * @return void
 */
function requireLogin(string $loginUrl = '../login.php'): void {
    if (!isLoggedIn()) {
        // Save flash error message to notify user why they were blocked
        if (function_exists('setFlash')) {
            setFlash('danger', 'Unauthorized access! Please login to access the admin portal.');
        }
        header("Location: {$loginUrl}");
        exit();
    }
}

/**
 * Enforces guest-only routes (e.g. login page).
 * If already logged in, redirects straight to dashboard.
 *
 * @param string $dashboardUrl Relative path to dashboard.
 * @return void
 */
function requireGuest(string $dashboardUrl = 'dashboard.php'): void {
    if (isLoggedIn()) {
        header("Location: {$dashboardUrl}");
        exit();
    }
}

/**
 * Retrieves the currently authenticated user's session data.
 *
 * @return array{id: int, name: string, email: string}|null
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'    => (int)$_SESSION['user_id'],
        'name'  => (string)$_SESSION['user_name'],
        'email' => (string)$_SESSION['user_email']
    ];
}

/**
 * Authenticates user and regenerates session ID to prevent Session Fixation.
 *
 * @param array $user Database user row
 * @return void
 */
function loginUser(array $user): void {
    // Session Fixation Defense: Always generate a brand new session ID upon privilege elevation
    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
}

/**
 * Safely terminates the user session and destroys the browser cookie.
 *
 * @return void
 */
function logoutUser(): void {
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
