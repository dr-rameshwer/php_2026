<?php
/**
 * Student Management System
 * Secure File Download Controller
 * 
 * Purpose: Streams uploaded student photos or attachments safely to the client.
 * Implements strict path-traversal prevention via basename() and enforces
 * binary attachment download headers.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

// Route Guard
requireLogin('../login.php');

$rawFilename = (string)($_GET['file'] ?? '');

if (empty($rawFilename)) {
    setFlash('warning', 'No filename specified for download.');
    header("Location: index.php");
    exit();
}

// SECURITY DEFENSE: Strip any directory navigation characters (e.g., ../../)
$safeFilename = basename($rawFilename);
$filePath = __DIR__ . '/../uploads/' . $safeFilename;

// Verify file existence and readability
if (!file_exists($filePath) || !is_file($filePath)) {
    setFlash('danger', 'The requested file does not exist on the server storage.');
    header("Location: index.php");
    exit();
}

// Determine MIME Content Type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

if (!$mimeType) {
    $mimeType = 'application/octet-stream';
}

// Clear any existing output buffers to prevent corrupt binary files
if (ob_get_level()) {
    ob_end_clean();
}

// Send Standard HTTP Binary Download Headers
header("Content-Description: File Transfer");
header("Content-Type: {$mimeType}");
header("Content-Disposition: attachment; filename=\"{$safeFilename}\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
header("Content-Length: " . (string)filesize($filePath));

// Stream file directly to client and terminate
readfile($filePath);
exit();
