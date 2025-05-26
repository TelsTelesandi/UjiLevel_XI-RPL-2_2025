<?php
require_once 'config/database.php';

$password = "123456"; // Set password baru untuk Ron
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'Ron'");
$stmt->execute([$hashed_password]);

echo "Password untuk user Ron telah direset!\n";
echo "Username: Ron\n";
echo "Password baru: 123456";
?> 