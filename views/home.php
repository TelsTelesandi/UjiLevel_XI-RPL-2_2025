<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php?action=login");
    exit;
}

// Redirect based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ./index.php?action=admin_dashboard");
        exit;
    } else {
        // For regular users, redirect to user dashboard or event list
        header("Location: ./index.php?action=user_dashboard");
        exit;
    }
}
?> 