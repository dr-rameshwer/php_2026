<?php
/**
 * BCA Student Management System
 * Admin Dashboard Controller & View
 * 
 * Purpose: Provides a high-level summary overview of enrolled students,
 * aggregate statistical metrics, quick actions, and recent registrations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

// Route Guard: Enforce authentication
requireLogin('login.php');

$currentUser = getCurrentUser();

// Fetch Aggregate Statistics using PDO
try {
    // 1. Total Student Count
    $totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

    // 2. BCA Specific Count
    $stmtBca = $pdo->prepare("SELECT COUNT(*) FROM students WHERE course = 'BCA'");
    $stmtBca->execute();
    $totalBca = (int)$stmtBca->fetchColumn();

    // 3. Semester 1 Students Count
    $stmtSem1 = $pdo->prepare("SELECT COUNT(*) FROM students WHERE semester = 1");
    $stmtSem1->execute();
    $totalSem1 = (int)$stmtSem1->fetchColumn();

    // 4. Distinct Courses Count
    $totalCourses = (int)$pdo->query("SELECT COUNT(DISTINCT course) FROM students")->fetchColumn();

    // 5. Fetch Recent 5 Registered Students
    $stmtRecent = $pdo->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
    $recentStudents = $stmtRecent->fetchAll();

} catch (PDOException $e) {
    error_log("[Dashboard Query Error] " . $e->getMessage());
    $totalStudents = $totalBca = $totalSem1 = $totalCourses = 0;
    $recentStudents = [];
}

$pageTitle = 'Dashboard - BCA Student Management System';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Welcome Banner -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1 text-dark">
            Welcome back, <?php echo e($currentUser['name']); ?>!
        </h2>
        <p class="text-muted mb-0">IKGPTU Academic Portal &bull; Department of Computer Applications</p>
    </div>
    <div class="d-flex gap-2">
        <a href="students/create.php" class="btn btn-warning fw-semibold shadow-sm text-dark">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Student
        </a>
        <a href="students/index.php" class="btn btn-outline-primary fw-semibold">
            <i class="bi bi-table me-1"></i> View All Students
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Students Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Students</span>
                    <h3 class="fw-bold mb-0"><?php echo $totalStudents; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- BCA Enrolled Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">BCA Students</span>
                    <h3 class="fw-bold mb-0"><?php echo $totalBca; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 1st Semester Freshmen Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">1st Semester</span>
                    <h3 class="fw-bold mb-0"><?php echo $totalSem1; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Courses Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Active Programs</span>
                    <h3 class="fw-bold mb-0"><?php echo $totalCourses; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Registrations Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-ptu-primary">
            <i class="bi bi-clock-history me-2"></i> Recently Registered Students
        </h5>
        <a href="students/index.php" class="btn btn-sm btn-light border text-primary fw-semibold">
            View All Directory <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentStudents)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                <p>No student records found in database.</p>
                <a href="students/create.php" class="btn btn-sm btn-primary">Add First Student</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Contact</th>
                            <th>Registration Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentStudents as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($student['photo'])): ?>
                                            <img src="uploads/<?php echo e($student['photo']); ?>" 
                                                 alt="<?php echo e($student['name']); ?>" 
                                                 class="student-img-thumbnail me-3">
                                        <?php else: ?>
                                            <div class="avatar-circle-sm bg-primary text-white fw-bold me-3">
                                                <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="students/show.php?id=<?php echo (int)$student['id']; ?>" class="fw-semibold text-dark text-decoration-none">
                                                <?php echo e($student['name']); ?>
                                            </a>
                                            <div class="text-muted small"><?php echo e($student['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <?php echo e($student['course']); ?>
                                    </span>
                                </td>
                                <td>Semester <?php echo (int)$student['semester']; ?></td>
                                <td><?php echo e($student['phone']); ?></td>
                                <td><?php echo formatDate($student['created_at']); ?></td>
                                <td class="text-end pe-4">
                                    <a href="students/show.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="students/edit.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit Student">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
