<?php
session_start();
require_once 'koneksi_admin.php';

// Jika sudah login sebagai admin, langsung redirect ke dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard_admin.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) && $user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard_admin.php");
            exit();
        } else {
            $error = "Username atau password salah, atau Anda bukan admin!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Poppins', sans-serif; }
        .container { max-width: 400px; margin: 5rem auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 2rem; }
        h2 { color: #1a237e; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input[type="text"], input[type="password"] { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #1a237e; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background: #283593; }
        .error { color: #c62828; background: #ffebee; padding: 0.5rem; border-radius: 5px; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Admin</h2>
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
