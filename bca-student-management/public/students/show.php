<?php
/**
 * BCA Student Management System
 * View Student Profile Controller & View (READ Action)
 * 
 * Purpose: Retrieves and displays a single student's complete profile
 * with photo display, metadata, download option, and management links.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

// Retrieve and validate student ID
$studentId = (int)($_GET['id'] ?? 0);
if ($studentId <= 0) {
    setFlash('warning', 'Invalid student identifier provided.');
    header("Location: index.php");
    exit();
}

// Fetch Student Record using PDO Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', "Student record #{$studentId} not found in the database.");
    header("Location: index.php");
    exit();
}

$pageTitle = e($student['name']) . ' - Student Profile';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Top Navigation Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Student List
            </a>
            <div class="d-flex gap-2">
                <a href="edit.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-warning fw-semibold text-dark shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                </a>
                <form method="POST" action="delete.php" class="d-inline confirm-delete-form" 
                      data-student-name="<?php echo e($student['name']); ?>">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$student['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Student Profile Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Banner -->
            <div class="bg-ptu-primary p-4 text-white text-center position-relative">
                <div class="mb-3">
                    <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/../uploads/' . $student['photo'])): ?>
                        <img src="../uploads/<?php echo e($student['photo']); ?>" 
                             alt="<?php echo e($student['name']); ?>" 
                             class="student-img-profile">
                    <?php else: ?>
                        <div class="avatar-circle-lg bg-warning text-dark mx-auto shadow">
                            <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h3 class="fw-bold mb-1"><?php echo e($student['name']); ?></h3>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <span class="badge bg-warning text-dark px-3 py-1 fs-6">
                        <?php echo e($student['course']); ?>
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1 fs-6">
                        Semester <?php echo (int)$student['semester']; ?>
                    </span>
                </div>
            </div>

            <!-- Profile Details Body -->
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <!-- Academic & ID Info -->
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Student Roll ID</div>
                        <div class="fs-5 fw-bold text-dark">#<?php echo (int)$student['id']; ?></div>
                    </div>

                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Gender</div>
                        <div class="fs-5 fw-semibold text-dark"><?php echo e($student['gender']); ?></div>
                    </div>

                    <!-- Contact Details -->
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Email Address</div>
                        <div class="fs-5 text-dark">
                            <a href="mailto:<?php echo e($student['email']); ?>" class="text-decoration-none">
                                <i class="bi bi-envelope me-1 text-primary"></i> <?php echo e($student['email']); ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Phone Number</div>
                        <div class="fs-5 text-dark">
                            <a href="tel:<?php echo e($student['phone']); ?>" class="text-decoration-none">
                                <i class="bi bi-telephone me-1 text-success"></i> <?php echo e($student['phone']); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Date of Birth</div>
                        <div class="fs-5 text-dark">
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            <?php echo formatDate($student['dob']); ?>
                        </div>
                    </div>

                    <!-- Admission Date -->
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Registration Date</div>
                        <div class="fs-5 text-dark">
                            <i class="bi bi-clock-history me-1 text-muted"></i>
                            <?php echo formatDate($student['created_at']); ?>
                        </div>
                    </div>

                    <!-- Permanent Address -->
                    <div class="col-12">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Permanent Residential Address</div>
                        <div class="p-3 bg-light rounded-3 text-secondary border">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <?php echo nl2br(e($student['address'])); ?>
                        </div>
                    </div>

                    <!-- File Attachment Download Section -->
                    <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/../uploads/' . $student['photo'])): ?>
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-image fs-2 text-primary me-3"></i>
                                    <div>
                                        <div class="fw-bold">Attached Profile Document / Photo</div>
                                        <small class="text-muted"><?php echo e($student['photo']); ?></small>
                                    </div>
                                </div>
                                <a href="download.php?file=<?php echo urlencode($student['photo']); ?>" 
                                   class="btn btn-outline-primary fw-semibold">
                                    <i class="bi bi-download me-1"></i> Safe Download
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer bg-light py-3 px-4 text-muted small d-flex justify-content-between">
                <span>Last Updated: <?php echo formatDate($student['updated_at'], 'd-M-Y H:i'); ?></span>
                <span>Record Status: <span class="badge bg-success">Active Enrolled</span></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
