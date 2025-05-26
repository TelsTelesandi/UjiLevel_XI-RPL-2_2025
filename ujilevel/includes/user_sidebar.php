<?php
// Ensure the session is started (though this might already be in the including files)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (redirect if not logged in, regardless of role)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Determine the current page to set the active class
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>User Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li class="<?= $current_page == 'user_dashboard.php' ? 'active' : '' ?>">
            <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?= $current_page == 'logout.php' ? 'active' : '' ?>">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>