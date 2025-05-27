<?php
// Set session cookie parameters before starting the session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Set session garbage collection parameters
ini_set('session.gc_maxlifetime', 1800); // 30 minutes
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Set session name
session_name('event_system');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Handle session regeneration
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
        session_regenerate_id(true);
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        $_SESSION['last_regeneration'] = time();
        session_regenerate_id(true);
    }
}

// Function to safely destroy session
function destroySession() {
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
}

// Don't register the cleanup function automatically
// Only call destroySession() when explicitly logging out 