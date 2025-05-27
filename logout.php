<?php
require_once 'config/database.php';
startSession();

// Destroy session
session_destroy();

// Redirect to login page
header('Location: index.php');
exit();
?>