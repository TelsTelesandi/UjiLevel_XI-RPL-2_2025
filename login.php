<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $koneksi->query("SELECT * FROM users WHERE username='$username'");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['eskul'] = $user['Eskul'];
            // Redirect sesuai role
            if ($user['role'] == 'admin') {
                header("Location: dasboardadmin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $msg = "Password salah!";
        }
    } else {
        $msg = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 350px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            padding: 32px 24px;
        }
        h2 {
            text-align: center;
            color: #2980b9;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #2980b9;
            border-radius: 6px;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #2980b9;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #1c5980;
        }
        .link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #8e44ad;
            text-decoration: none;
        }
        .msg {
            text-align: center;
            margin-bottom: 12px;
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Akun</h2>
        <?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
        <a class="link" href="register.php">Belum punya akun? Register</a>
    </div>
</body>
</html>