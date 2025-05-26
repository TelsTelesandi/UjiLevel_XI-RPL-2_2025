<?php
require_once 'config/database.php';

$password = "123456";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$hashed_password]);

echo "Password admin telah diperbarui!";
?> 