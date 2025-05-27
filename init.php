<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include session handler first
require_once 'session_handler.php';

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "re_nabil_efriansyah";

// Create database connection with error handling
try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper character handling
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Connection error: " . $e->getMessage());
}
?> 