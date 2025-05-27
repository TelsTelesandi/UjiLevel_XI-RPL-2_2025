<?php
require_once dirname(__DIR__) . '/session.php';

function isActive($page) {
    return (basename($_SERVER['PHP_SELF']) === $page) ? 'active' : '';
}

// Get the root URL for the application
$rootUrl = '';
$currentDir = dirname($_SERVER['PHP_SELF']);
if (strpos($currentDir, '/admin') !== false) {
    $rootUrl = '..';
} else {
    $rootUrl = '.';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $rootUrl; ?>/index.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('home.php'); ?>" href="<?php echo $rootUrl; ?>/home.php">Home</a>
                    </li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isActive('dashboard.php'); ?>" href="<?php echo $rootUrl; ?>/admin/dashboard.php">Admin Panel</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $rootUrl; ?>/logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('login.php'); ?>" href="<?php echo $rootUrl; ?>/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('register.php'); ?>" href="<?php echo $rootUrl; ?>/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 