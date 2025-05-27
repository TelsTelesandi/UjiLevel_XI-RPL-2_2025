<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Make sure no output has been sent
if (headers_sent($filename, $linenum)) {
    die("Headers already sent in $filename on line $linenum");
}

// Include the session handler
require_once 'session_handler.php';

// Make sure we have an active session before destroying it
if (session_status() === PHP_SESSION_ACTIVE) {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect to login page with absolute path
header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php");
exit();
?>
