<?php
/**
 * BCA Student Management System
 * Edit Student Profile Controller & View
 * 
 * Purpose: Pre-populates existing student data into a validated form,
 * allowing updates to academic, contact, and photo attachment details.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

$studentId = (int)($_GET['id'] ?? 0);
if ($studentId <= 0) {
    setFlash('warning', 'Invalid student identifier provided.');
    header("Location: index.php");
    exit();
}

// Fetch Student Record from MySQL using PDO
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', "Student record #{$studentId} not found in database.");
    header("Location: index.php");
    exit();
}

// Check for any validation errors from previous update attempt
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? $student;
unset($_SESSION['form_errors'], $_SESSION['old_input']);

$pageTitle = 'Edit Student: ' . e($student['name']);
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0 text-ptu-primary">
                <i class="bi bi-pencil-square me-2"></i> Edit Student Profile
            </h3>
            <a href="show.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Cancel & View Profile
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="text-muted small">Editing Record for Roll ID: <strong>#<?php echo (int)$student['id']; ?></strong></span>
                <span class="badge bg-secondary-subtle text-secondary">Registered: <?php echo formatDate($student['created_at']); ?></span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="update.php" enctype="multipart/form-data" novalidate>
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$student['id']; ?>">

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" value="<?php echo e($old['name'] ?? ''); ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['name']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">College Email ID <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo e($old['email'] ?? ''); ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                   id="phone" name="phone" value="<?php echo e($old['phone'] ?? ''); ?>" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['phone']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Gender Radio Selection -->
                        <div class="col-md-4">
                            <label class="form-label d-block">Gender <span class="text-danger">*</span></label>
                            <div class="pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" 
                                           <?php echo (($old['gender'] ?? '') === 'Male') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female"
                                           <?php echo (($old['gender'] ?? '') === 'Female') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other"
                                           <?php echo (($old['gender'] ?? '') === 'Other') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="genderOther">Other</label>
                                </div>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-4">
                            <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php echo isset($errors['dob']) ? 'is-invalid' : ''; ?>" 
                                   id="dob" name="dob" value="<?php echo e($old['dob'] ?? ''); ?>" required>
                            <?php if (isset($errors['dob'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['dob']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Course -->
                        <div class="col-md-6">
                            <label for="course" class="form-label">Academic Program <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($errors['course']) ? 'is-invalid' : ''; ?>" 
                                    id="course" name="course" required>
                                <?php 
                                $courses = ['BCA', 'MCA', 'B.Tech IT', 'B.Sc Computer Science'];
                                foreach ($courses as $c): 
                                ?>
                                    <option value="<?php echo $c; ?>" <?php echo (($old['course'] ?? '') === $c) ? 'selected' : ''; ?>>
                                        <?php echo $c; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['course'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['course']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6">
                            <label for="semester" class="form-label">Current Semester <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($errors['semester']) ? 'is-invalid' : ''; ?>" 
                                    id="semester" name="semester" required>
                                <?php for ($s = 1; $s <= 6; $s++): ?>
                                    <option value="<?php echo $s; ?>" <?php echo ((int)($old['semester'] ?? 1) === $s) ? 'selected' : ''; ?>>
                                        Semester <?php echo $s; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <?php if (isset($errors['semester'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['semester']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label">Permanent Address <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" 
                                      id="address" name="address" rows="3" required><?php echo e($old['address'] ?? ''); ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['address']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Photo Replacement Section -->
                        <div class="col-12">
                            <label for="photoInput" class="form-label">Replace Student Photo (Leave blank to keep existing)</label>
                            
                            <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/../uploads/' . $student['photo'])): ?>
                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded border">
                                    <img src="../uploads/<?php echo e($student['photo']); ?>" 
                                         alt="Current Photo" class="student-img-thumbnail me-3">
                                    <div>
                                        <div class="small fw-semibold">Current Stored Photo:</div>
                                        <span class="text-muted small"><?php echo e($student['photo']); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <input type="file" class="form-control <?php echo isset($errors['photo']) ? 'is-invalid' : ''; ?>" 
                                   id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Uploading a new image will automatically replace the old file. Max size: 2MB.</div>
                            <?php if (isset($errors['photo'])): ?>
                                <div class="invalid-feedback d-block"><?php echo e($errors['photo']); ?></div>
                            <?php endif; ?>

                            <!-- Live Image Preview Box -->
                            <div class="mt-2">
                                <img id="imagePreviewBox" src="" alt="Upload Preview">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 mt-4 pt-2 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Update Student Record
                            </button>
                            <a href="show.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-light border px-4">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
