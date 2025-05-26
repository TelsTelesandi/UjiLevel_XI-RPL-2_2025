<nav class="dashboard-navbar">
    <ul>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="../admin/events.php" <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'class="active"' : ''; ?>><i class="fas fa-calendar-alt"></i> Kelola Event</a></li>
            <li><a href="../admin/users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> Kelola User</a></li>
        <?php else: ?>
            <li><a href="../users/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../users/event_form.php" <?php echo basename($_SERVER['PHP_SELF']) == 'event_form.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus"></i> Ajukan Event</a></li>
            <li><a href="../users/my_events.php" <?php echo basename($_SERVER['PHP_SELF']) == 'my_events.php' ? 'class="active"' : ''; ?>><i class="fas fa-list"></i> Event Saya</a></li>
        <?php endif; ?>
        <li><a href="../proses/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>