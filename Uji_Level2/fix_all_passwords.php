<?php
require_once 'config/database.php';

// Data user dengan password yang benar
$users = [
    ['username' => 'admin', 'password' => '123456'],
    ['username' => 'Ron', 'password' => '123456'],
    ['username' => 'rehan', 'password' => '234567'],
    ['username' => 'biyu', 'password' => '345678'],
    ['username' => 'tegar', 'password' => '456789'],
    ['username' => 'juno', 'password' => '567890']
];

foreach ($users as $user) {
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, $user['username']]);
    echo "Password untuk {$user['username']} telah diperbarui!\n";
}

echo "\nSemua password telah diperbaiki. Gunakan password berikut untuk login:\n\n";
echo "admin  : 123456\n";
echo "Ron    : 123456\n";
echo "rehan  : 234567\n";
echo "biyu   : 345678\n";
echo "tegar  : 456789\n";
echo "juno   : 567890\n";
?> 