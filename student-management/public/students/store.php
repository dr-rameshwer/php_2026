<?php
/**
 * Student Management System
 * Store Student Controller (CREATE Action)
 * 
 * Purpose: Handles POST submission, verifies CSRF token, executes strict
 * server-side validation, processes secure photo upload, and inserts record
 * into MySQL database using PDO prepared statements.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

// Enforce POST Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 1. Verify CSRF Token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    setFlash('danger', 'Security Alert: CSRF token validation failed! Please re-submit the form.');
    header("Location: create.php");
    exit();
}

// 2. Sanitize and Extract Form Fields
$name     = sanitizeInput($_POST['name'] ?? '');
$email    = strtolower(sanitizeInput($_POST['email'] ?? ''));
$phone    = sanitizeInput($_POST['phone'] ?? '');
$gender   = sanitizeInput($_POST['gender'] ?? 'Male');
$dob      = sanitizeInput($_POST['dob'] ?? '');
$course   = sanitizeInput($_POST['course'] ?? '');
$semester = (int)($_POST['semester'] ?? 1);
$address  = sanitizeInput($_POST['address'] ?? '');

$errors = [];

// 3. Server-Side Validation Rules
if (empty($name) || strlen($name) < 3) {
    $errors['name'] = 'Full Name must be at least 3 characters long.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid college email address is required.';
} else {
    // Check if email already exists in database
    $stmtCheck = $pdo->prepare("SELECT id FROM students WHERE email = :email LIMIT 1");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->fetch()) {
        $errors['email'] = 'This email address is already registered for another student.';
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
    $errors['semester'] = 'Semester must be an integer between 1 and 6.';
}

if (empty($address) || strlen($address) < 5) {
    $errors['address'] = 'Permanent Address must contain at least 5 characters.';
}

// 4. Handle Optional File Upload
$photoFilename = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadDir = __DIR__ . '/../uploads/';
    $photoFilename = handlePhotoUpload($_FILES['photo'], $errors, $uploadDir);
}

// 5. If Validation Fails: Redirect back with old input and error messages
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = [
        'name'     => $name,
        'email'    => $email,
        'phone'    => $phone,
        'gender'   => $gender,
        'dob'      => $dob,
        'course'   => $course,
        'semester' => $semester,
        'address'  => $address
    ];
    header("Location: create.php");
    exit();
}

// 6. Insert Student Record into MySQL using PDO Prepared Statement
try {
    $sql = "INSERT INTO students (name, email, phone, gender, dob, course, semester, address, photo)
            VALUES (:name, :email, :phone, :gender, :dob, :course, :semester, :address, :photo)";
    
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
        ':photo'    => $photoFilename
    ]);

    $newStudentId = $pdo->lastInsertId();
    setFlash('success', "Student record for '{$name}' created successfully with Roll ID #{$newStudentId}!");
    header("Location: index.php");
    exit();

} catch (PDOException $e) {
    error_log("[Student Store Error] " . $e->getMessage());
    // If an uploaded photo was stored but the DB query failed, clean up the orphaned file
    if ($photoFilename) {
        deleteUploadedPhoto($photoFilename, __DIR__ . '/../uploads/');
    }
    setFlash('danger', 'An unexpected database error occurred while creating the record.');
    header("Location: create.php");
    exit();
}
