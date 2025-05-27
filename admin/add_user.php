<?php
include '../config.php';
include 'check_admin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    // Check if username already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if($check->get_result()->num_rows > 0) {
        header("Location: dashboard.php?error=username_exists");
        exit();
    }
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=user_added");
    } else {
        header("Location: dashboard.php?error=insert_failed");
    }
    exit();
} else {
    header("Location: dashboard.php");
    exit();
} 