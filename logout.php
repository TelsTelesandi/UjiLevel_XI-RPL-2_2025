<?php
// filepath: c:\laragon\www\uji_level2\logout.php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>