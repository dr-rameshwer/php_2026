<?php
/**
 * Student Management System
 * Global Utility Functions & Security Helpers
 * 
 * Purpose: Contains reusable sanitization, CSRF token defenses, input validation,
 * date formatting, and secure file upload/delete functions.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escapes HTML characters for secure output in views (XSS Defense).
 *
 * @param mixed $value Raw data string
 * @return string Safe sanitized HTML string
 */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generates or retrieves the active Anti-CSRF token from the session.
 *
 * @return string 64-character hexadecimal token
 */
function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Renders a hidden CSRF token input field for HTML forms.
 *
 * @return string HTML hidden input element
 */
function csrfField(): string {
    $token = getCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Verifies that the submitted CSRF token matches the session token.
 *
 * @param string|null $token Submitted token from $_POST
 * @return bool True if valid, false otherwise
 */
function verifyCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Strips whitespace and normalizes text input.
 *
 * @param mixed $value
 * @return string
 */
function sanitizeInput($value): string {
    if (is_null($value)) {
        return '';
    }
    return trim((string)$value);
}

/**
 * Formats a MySQL DATE or TIMESTAMP into human-friendly format.
 *
 * @param string|null $dateString MySQL date string (YYYY-MM-DD)
 * @param string $format Target PHP date format
 * @return string
 */
function formatDate(?string $dateString, string $format = 'd-M-Y'): string {
    if (empty($dateString) || $dateString === '0000-00-00') {
        return 'N/A';
    }
    $timestamp = strtotime($dateString);
    if ($timestamp === false) {
        return 'Invalid Date';
    }
    return date($format, $timestamp);
}

/**
 * Handles secure file uploads for student photos.
 * Performs rigorous multi-layer validation:
 * 1. Checks upload error codes
 * 2. Enforces maximum file size limit (2MB)
 * 3. Enforces allowed extensions whitelist (.jpg, .jpeg, .png, .webp)
 * 4. Verifies genuine binary MIME signature with finfo (prevents PHP shell injection)
 * 5. Generates randomized, unguessable, collision-free filename
 *
 * @param array $file The $_FILES['photo'] array
 * @param array $errors Reference to errors array to record validation failures
 * @param string $uploadDir Destination directory path
 * @return string|null Generated filename on success, or null on failure/no file
 */
function handlePhotoUpload(array $file, array &$errors, string $uploadDir): ?string {
    // If no file was selected, return null (photo is optional)
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // 1. Check for standard PHP upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors['photo'] = 'Selected file is too large! Maximum limit is 2MB.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors['photo'] = 'File upload was interrupted. Please try again.';
                break;
            default:
                $errors['photo'] = 'File upload error occurred (Code: ' . $file['error'] . ').';
                break;
        }
        return null;
    }

    // 2. Validate maximum file size (2 Megabytes = 2 * 1024 * 1024 bytes)
    $maxBytes = 2 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        $errors['photo'] = 'File size exceeds 2MB limit.';
        return null;
    }

    // 3. Validate file extension whitelist
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        $errors['photo'] = 'Invalid file extension! Only JPG, JPEG, PNG, and WEBP images are allowed.';
        return null;
    }

    // 4. Validate true binary MIME type using PHP Fileinfo extension
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        $errors['photo'] = 'Security Alert: File content does not match a valid image format!';
        return null;
    }

    // 5. Ensure target directory exists with proper permissions
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            $errors['photo'] = 'Server configuration error: Upload directory cannot be created.';
            return null;
        }
    }

    // 6. Generate randomized collision-free filename
    $uniqueName = 'std_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
    $targetPath = rtrim($uploadDir, '/') . '/' . $uniqueName;

    // 7. Move file from temporary directory to permanent destination
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $errors['photo'] = 'Failed to move uploaded file to destination.';
        return null;
    }

    return $uniqueName;
}

/**
 * Deletes an uploaded photo file from disk when a record is deleted or updated.
 *
 * @param string|null $filename Filename to delete
 * @param string $uploadDir Upload directory path
 * @return void
 */
function deleteUploadedPhoto(?string $filename, string $uploadDir): void {
    if (empty($filename)) {
        return;
    }
    // Prevent directory traversal attacks using basename
    $safeName = basename($filename);
    $fullPath = rtrim($uploadDir, '/') . '/' . $safeName;

    if (file_exists($fullPath) && is_file($fullPath)) {
        @unlink($fullPath);
    }
}
