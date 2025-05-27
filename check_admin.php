<?php
include 'config.php';

$query = "SELECT user_id, username, role FROM users WHERE role = 'admin'";
$result = $conn->query($query);

echo "Daftar User Admin:<br>";
echo "==================<br>";
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['user_id'] . "<br>";
    echo "Username: " . $row['username'] . "<br>";
    echo "Role: " . $row['role'] . "<br>";
    echo "==================<br>";
}

$conn->close(); 