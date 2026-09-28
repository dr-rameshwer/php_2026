<?php
/**
 * Student Management System
 * Add New Student Registration View
 * 
 * Purpose: Provides a validated Bootstrap 5 input form with file upload support
 * for registering new students into the system.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

// Retrieve any preserved old input or validation errors from session
$old = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['old_input'], $_SESSION['form_errors']);

$pageTitle = 'Add New Student - Student Management';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0 text-ptu-primary">
                <i class="bi bi-person-plus-fill me-2"></i> Register New Student
            </h3>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Directory
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <span class="text-muted small">Please complete all required fields marked with an asterisk (<span class="text-danger">*</span>)</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="store.php" enctype="multipart/form-data" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" value="<?php echo e($old['name'] ?? ''); ?>" 
                                   placeholder="e.g. Amanpreet Singh" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['name']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">College Email ID <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo e($old['email'] ?? ''); ?>" 
                                   placeholder="e.g. aman@ptu.ac.in" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                   id="phone" name="phone" value="<?php echo e($old['phone'] ?? ''); ?>" 
                                   placeholder="10-digit mobile number" required>
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
                                           <?php echo (($old['gender'] ?? 'Male') === 'Male') ? 'checked' : ''; ?>>
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
                                <option value="">-- Choose Program --</option>
                                <?php 
                                $courses = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science'];
                                foreach ($courses as $c): 
                                ?>
                                    <option value="<?php echo $c; ?>" <?php echo (($old['course'] ?? 'Computer Science') === $c) ? 'selected' : ''; ?>>
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
                                    <option value="<?php echo $s; ?>" <?php echo (($old['semester'] ?? '1') == $s) ? 'selected' : ''; ?>>
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
                                      id="address" name="address" rows="3" 
                                      placeholder="House number, street, city, pin code..." required><?php echo e($old['address'] ?? ''); ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?php echo e($errors['address']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Student Photo Upload -->
                        <div class="col-12">
                            <label for="photoInput" class="form-label">Student Photo Attachment (Optional)</label>
                            <input type="file" class="form-control <?php echo isset($errors['photo']) ? 'is-invalid' : ''; ?>" 
                                   id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Accepted formats: JPG, PNG, WEBP. Maximum size: 2MB.</div>
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
                                <i class="bi bi-check2-circle me-1"></i> Save Student Record
                            </button>
                            <a href="index.php" class="btn btn-light border px-4">
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
