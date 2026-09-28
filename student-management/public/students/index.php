<?php
/**
 * BCA Student Management System
 * Students Directory Controller & View
 * 
 * Features: Multi-field Search, Course/Semester Filtering,
 * Dynamic Pagination, and Secure POST Deletion.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

// Retrieve Filter & Search Parameters from GET
$search   = sanitizeInput($_GET['search'] ?? '');
$course   = sanitizeInput($_GET['course'] ?? '');
$semester = sanitizeInput($_GET['semester'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 5; // Records per page for pagination

// Build Dynamic SQL Query with PDO Parameters
$whereClauses = [];
$params       = [];

if (!empty($search)) {
    $whereClauses[] = "(name LIKE :search_name OR email LIKE :search_email OR phone LIKE :search_phone)";
    $params[':search_name']  = "%{$search}%";
    $params[':search_email'] = "%{$search}%";
    $params[':search_phone'] = "%{$search}%";
}

if (!empty($course)) {
    $whereClauses[] = "course = :course";
    $params[':course'] = $course;
}

if (!empty($semester)) {
    $whereClauses[] = "semester = :semester";
    $params[':semester'] = (int)$semester;
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Count Total Matching Records for Pagination
$countSql = "SELECT COUNT(*) FROM students {$whereSql}";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();

// Calculate Pagination Bounds
$totalPages = max(1, (int)ceil($totalRecords / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// Fetch Current Page Records
$dataSql = "SELECT * FROM students {$whereSql} ORDER BY id DESC LIMIT :limit OFFSET :offset";
$dataStmt = $pdo->prepare($dataSql);

// Bind filter parameters
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
// Bind integer limit and offset
$dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$students = $dataStmt->fetchAll();

// Fetch Distinct Courses for Filter Dropdown
$coursesList = $pdo->query("SELECT DISTINCT course FROM students ORDER BY course ASC")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Student Directory - BCA Management';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-0 text-ptu-primary">
            <i class="bi bi-people-fill me-2"></i> Student Directory
        </h3>
        <p class="text-muted small mb-0">Showing <?php echo count($students); ?> of <?php echo $totalRecords; ?> enrolled students</p>
    </div>
    <a href="create.php" class="btn btn-warning fw-semibold shadow-sm text-dark">
        <i class="bi bi-person-plus-fill me-1"></i> Add New Student
    </a>
</div>

<!-- Search & Filtering Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="index.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, email, or phone..." 
                           value="<?php echo e($search); ?>">
                </div>
            </div>

            <div class="col-md-3">
                <select name="course" class="form-select">
                    <option value="">-- All Programs --</option>
                    <?php foreach ($coursesList as $cOption): ?>
                        <option value="<?php echo e($cOption); ?>" <?php echo ($course === $cOption) ? 'selected' : ''; ?>>
                            <?php echo e($cOption); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="semester" class="form-select">
                    <option value="">-- Semester --</option>
                    <?php for ($s = 1; $s <= 6; $s++): ?>
                        <option value="<?php echo $s; ?>" <?php echo ($semester === (string)$s) ? 'selected' : ''; ?>>
                            Sem <?php echo $s; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Filter
                </button>
                <?php if (!empty($search) || !empty($course) || !empty($semester)): ?>
                    <a href="index.php" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Students Records Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($students)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <h5>No Matching Students Found</h5>
                <p class="small">Try adjusting your search criteria or clear the filters.</p>
                <a href="index.php" class="btn btn-sm btn-outline-primary">View All Students</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($row['photo'])): ?>
                                            <img src="../uploads/<?php echo e($row['photo']); ?>" 
                                                 alt="<?php echo e($row['name']); ?>" 
                                                 class="student-img-thumbnail me-3">
                                        <?php else: ?>
                                            <div class="avatar-circle-sm bg-primary text-white fw-bold me-3">
                                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="show.php?id=<?php echo (int)$row['id']; ?>" class="fw-bold text-dark text-decoration-none">
                                                <?php echo e($row['name']); ?>
                                            </a>
                                            <div class="text-muted small"><?php echo e($row['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <?php echo e($row['course']); ?>
                                    </span>
                                </td>
                                <td>Semester <?php echo (int)$row['semester']; ?></td>
                                <td>
                                    <?php if ($row['gender'] === 'Male'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">Male</span>
                                    <?php elseif ($row['gender'] === 'Female'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Female</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border">Other</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone text-muted me-1"></i><?php echo e($row['phone']); ?></div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="show.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-outline-info" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-outline-warning" title="Edit Student">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <!-- Secure POST Delete Form -->
                                        <form method="POST" action="delete.php" class="d-inline confirm-delete-form" 
                                              data-student-name="<?php echo e($row['name']); ?>">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Student" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination Footer -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <span class="text-muted small">
                Page <strong><?php echo $page; ?></strong> of <strong><?php echo $totalPages; ?></strong>
            </span>
            <nav aria-label="Student Directory Pages">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Page -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($course); ?>&semester=<?php echo urlencode($semester); ?>&page=<?php echo $page - 1; ?>">
                            &laquo; Prev
                        </a>
                    </li>

                    <!-- Page Number Loops -->
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($course); ?>&semester=<?php echo urlencode($semester); ?>&page=<?php echo $p; ?>">
                                <?php echo $p; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Page -->
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($course); ?>&semester=<?php echo urlencode($semester); ?>&page=<?php echo $page + 1; ?>">
                            Next &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
