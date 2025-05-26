<?php
// Ensure the session is started (though this might already be in the including files)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin (redundant since already checked in main files, but for safety)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Determine the current page to set the active class
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?= $current_page == 'admin_users.php' ? 'active' : '' ?>">
            <a href="admin_users.php"><i class="fas fa-users"></i> Manajemen User</a>
        </li>
        <li class="<?= $current_page == 'admin_event.php' ? 'active' : '' ?>">
            <a href="admin_event.php"><i class="fas fa-calendar-check"></i> Approval Kegiatan</a>
        </li>
        <li class="<?= $current_page == 'admin_reports.php' ? 'active' : '' ?>">
            <a href="admin_reports.php"><i class="fas fa-file-alt"></i> Laporan</a>
        </li>
        <li class="<?= $current_page == 'logout.php' ? 'active' : '' ?>">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>