<?php
/**
 * BCA Student Management System
 * Administrator Login Controller & View
 * 
 * Purpose: Authenticates administrative users using PDO prepared statements,
 * password_verify() Bcrypt verification, and session regeneration.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

// If user is already logged in, redirect straight to dashboard
requireGuest('dashboard.php');

$error = '';
$email = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        $error = 'Security Alert: Invalid session token. Please try again.';
    } else {
        // 2. Sanitize and retrieve inputs
        $email    = sanitizeInput($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // 3. Basic validation
        if (empty($email) || empty($password)) {
            $error = 'Please enter both your college email and password.';
        } else {
            // 4. Query user by email using PDO Prepared Statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // 5. Verify Password with password_verify()
            if ($user && password_verify($password, $user['password'])) {
                // Authentication Successful
                loginUser($user);
                setFlash('success', "Welcome back, {$user['name']}! You have logged in successfully.");
                header("Location: dashboard.php");
                exit();
            } else {
                // Generic error message to prevent username harvesting
                $error = 'Invalid email address or password combination.';
            }
        }
    }
}

$pageTitle = 'Administrator Login - University BCA Portal';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center my-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <!-- Card Header -->
            <div class="bg-ptu-primary p-4 text-center text-white">
                <i class="bi bi-shield-lock-fill fs-1 text-warning mb-2 d-inline-block"></i>
                <h4 class="fw-bold mb-1">Administrative Login</h4>
                <p class="text-white-50 small mb-0">University BCA Student Portal Management</p>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        <span><?php echo e($error); ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Default Seed Credentials Helper Callout -->
                <div class="alert alert-info py-2 small mb-4">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>Demo Admin Credentials:</strong><br>
                    Email: <code>admin@bca.edu</code><br>
                    Password: <code>AdminPassword123</code>
                </div>

                <form method="POST" action="login.php" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">College Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo e($email); ?>" placeholder="admin@bca.edu" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
                        </button>
                    </div>
                </form>
            </div>
            <!-- Card Footer -->
            <div class="card-footer bg-light py-3 text-center text-muted small border-0">
                <i class="bi bi-lock me-1"></i> Secure 256-bit Session Authentication
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
