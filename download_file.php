<?php
include 'config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    header("Location: login.php");
    exit;
}

// Validate file parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("File parameter is required");
}

$filename = basename($_GET['file']);
$filepath = "uploads/closed/" . $filename;

// If file not found in closed directory, try regular uploads
if (!file_exists($filepath)) {
    $filepath = "uploads/" . $filename;
}

// Basic security check to prevent directory traversal
if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
    die("Invalid file path");
}

// Check if file exists
if (!file_exists($filepath)) {
    die("File not found");
}

// Get file extension
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

// Allow PDF and ZIP files
if (!in_array($ext, ['pdf', 'zip'])) {
    die("Invalid file type");
}

// Set appropriate headers based on file type
if ($ext === 'pdf') {
    header('Content-Type: application/pdf');
} else {
    header('Content-Type: application/zip');
}

header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file
readfile($filepath);
exit;
