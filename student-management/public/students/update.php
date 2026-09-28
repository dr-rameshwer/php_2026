<?php
/**
 * Student Management System
 * Update Student Controller (UPDATE Action)
 * 
 * Purpose: Validates POST update submission, ensures email uniqueness across
 * other records, processes optional photo replacement (cleans up old photo),
 * and executes UPDATE statement using PDO prepared statements.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 1. Verify CSRF Token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    setFlash('danger', 'Security Alert: CSRF token validation failed! Please retry.');
    header("Location: index.php");
    exit();
}

// 2. Validate Student Identifier
$studentId = (int)($_POST['id'] ?? 0);
if ($studentId <= 0) {
    setFlash('danger', 'Invalid student identifier provided.');
    header("Location: index.php");
    exit();
}

// Fetch current record to verify existence and retrieve existing photo filename
$stmtCurrent = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
$stmtCurrent->execute([':id' => $studentId]);
$currentStudent = $stmtCurrent->fetch();

if (!$currentStudent) {
    setFlash('danger', 'The student record you are attempting to update no longer exists.');
    header("Location: index.php");
    exit();
}

// 3. Sanitize and Extract Form Fields
$name     = sanitizeInput($_POST['name'] ?? '');
$email    = strtolower(sanitizeInput($_POST['email'] ?? ''));
$phone    = sanitizeInput($_POST['phone'] ?? '');
$gender   = sanitizeInput($_POST['gender'] ?? 'Male');
$dob      = sanitizeInput($_POST['dob'] ?? '');
$course   = sanitizeInput($_POST['course'] ?? '');
$semester = (int)($_POST['semester'] ?? 1);
$address  = sanitizeInput($_POST['address'] ?? '');

$errors = [];

// 4. Server-Side Validation Rules
if (empty($name) || strlen($name) < 3) {
    $errors['name'] = 'Full Name must be at least 3 characters long.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid college email address is required.';
} else {
    // Check if email already belongs to ANOTHER student (excluding current student ID)
    $stmtCheck = $pdo->prepare("SELECT id FROM students WHERE email = :email AND id != :id LIMIT 1");
    $stmtCheck->execute([':email' => $email, ':id' => $studentId]);
    if ($stmtCheck->fetch()) {
        $errors['email'] = 'This email address is already registered to another student.';
    }
}

if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number (10 to 15 numeric digits).';
}

if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
    $errors['gender'] = 'Please select a valid gender option.';
}

if (empty($dob) || strtotime($dob) === false) {
    $errors['dob'] = 'Please enter a valid Date of Birth.';
}

$validCourses = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science'];
if (!in_array($course, $validCourses, true)) {
    $errors['course'] = 'Please select an authorized academic program.';
}

if ($semester < 1 || $semester > 6) {
    $errors['semester'] = 'Semester must be between 1 and 6.';
}

if (empty($address) || strlen($address) < 5) {
    $errors['address'] = 'Permanent Address must contain at least 5 characters.';
}

// 5. Handle Photo Replacement (if a new file was uploaded)
$uploadDir = __DIR__ . '/../uploads/';
$newPhotoFilename = null;
$hasNewPhoto = false;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $newPhotoFilename = handlePhotoUpload($_FILES['photo'], $errors, $uploadDir);
    if ($newPhotoFilename) {
        $hasNewPhoto = true;
    }
}

// 6. If Validation Fails: Redirect back to edit form with error messages
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = [
        'id'       => $studentId,
        'name'     => $name,
        'email'    => $email,
        'phone'    => $phone,
        'gender'   => $gender,
        'dob'      => $dob,
        'course'   => $course,
        'semester' => $semester,
        'address'  => $address
    ];
    header("Location: edit.php?id={$studentId}");
    exit();
}

// Determine final photo filename (use new photo if uploaded, otherwise keep existing)
$finalPhoto = $hasNewPhoto ? $newPhotoFilename : $currentStudent['photo'];

// 7. Update Record in MySQL using PDO Prepared Statement
try {
    $sql = "UPDATE students 
            SET name = :name, email = :email, phone = :phone, gender = :gender, 
                dob = :dob, course = :course, semester = :semester, 
                address = :address, photo = :photo
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name'     => $name,
        ':email'    => $email,
        ':phone'    => $phone,
        ':gender'   => $gender,
        ':dob'      => $dob,
        ':course'   => $course,
        ':semester' => $semester,
        ':address'  => $address,
        ':photo'    => $finalPhoto,
        ':id'       => $studentId
    ]);

    // If new photo was uploaded and successfully saved, delete old orphaned photo from disk
    if ($hasNewPhoto && !empty($currentStudent['photo'])) {
        deleteUploadedPhoto($currentStudent['photo'], $uploadDir);
    }

    setFlash('success', "Student profile for '{$name}' updated successfully!");
    header("Location: show.php?id={$studentId}");
    exit();

} catch (PDOException $e) {
    error_log("[Student Update Error] " . $e->getMessage());
    if ($hasNewPhoto && $newPhotoFilename) {
        deleteUploadedPhoto($newPhotoFilename, $uploadDir);
    }
    setFlash('danger', 'Database error occurred while updating the student record.');
    header("Location: edit.php?id={$studentId}");
    exit();
}
