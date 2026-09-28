<?php
/**
 * Student Management System
 * Delete Student Controller (DELETE Action)
 * 
 * Purpose: Enforces POST-only deletion, verifies CSRF token, removes record
 * from database using PDO prepared statements, and cleans up uploaded photo.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

// Security: Enforce POST method (Prevent CSRF & browser pre-fetch deletions)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid deletion request method.');
    header("Location: index.php");
    exit();
}

// 1. Verify CSRF Token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    setFlash('danger', 'Security Alert: CSRF token validation failed! Deletion aborted.');
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

try {
    // 3. Fetch record to retrieve student name and photo filename
    $stmtFetch = $pdo->prepare("SELECT name, photo FROM students WHERE id = :id LIMIT 1");
    $stmtFetch->execute([':id' => $studentId]);
    $student = $stmtFetch->fetch();

    if (!$student) {
        setFlash('warning', 'Student record not found or already deleted.');
        header("Location: index.php");
        exit();
    }

    // 4. Delete Record from Database using PDO Prepared Statement
    $stmtDelete = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $stmtDelete->execute([':id' => $studentId]);

    // 5. Clean up attached photo file from server storage
    if (!empty($student['photo'])) {
        deleteUploadedPhoto($student['photo'], __DIR__ . '/../uploads/');
    }

    setFlash('success', "Student record for '{$student['name']}' has been permanently deleted.");
    header("Location: index.php");
    exit();

} catch (PDOException $e) {
    error_log("[Student Deletion Error] " . $e->getMessage());
    setFlash('danger', 'Database error occurred while attempting to delete the record.');
    header("Location: index.php");
    exit();
}
