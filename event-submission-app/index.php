<?php
require_once 'config/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

header("Location: login.php");
exit();
?>
