<?php
// Mulai sesi untuk menyimpan data login
session_start();

// Data pengguna dari tabel
$users = [
    ['username' => 'dzaki', 'password' => '1', 'role' => 'Admin', 'nama_lengkap' => 'Muhammad dzaki', 'ekskul' => ''],
    ['username' => 'Jamal', 'password' => '1', 'role' => 'user', 'nama_lengkap' => 'Jamal Ikhsan', 'ekskul' => 'Futsal'],
    ['username' => 'Andi', 'password' => '1', 'role' => 'user', 'nama_lengkap' => 'Andi Saputra', 'ekskul' => 'Futsal'],
    ['username' => 'Salman', 'password' => '1', 'role' => 'user', 'nama_lengkap' => 'Salman Alfarizi', 'ekskul' => 'Futsal'],
    ['username' => 'Rafyadi', 'password' => '1', 'role' => 'user', 'nama_lengkap' => 'Rafly Adi', 'ekskul' => 'Sepak Bola'],
];

// Logout handling
if (isset($_GET['logout'])) {
    session_destroy();
    $logout_message = 'Anda telah berhasil logout. Silakan login kembali.';
}

// Pesan error
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Cek apakah username dan password cocok
    $login_success = false;
    $current_user = null;

    foreach ($users as $user) {
        if ($user['username'] == $input_username && $user['password'] == $input_password) {
            $login_success = true;
            $current_user = $user;
            break;
        }
    }

    if ($login_success) {
        // Simpan data pengguna ke sesi
        $_SESSION['username'] = $current_user['username'];
        $_SESSION['role'] = $current_user['role'];
        $_SESSION['nama_lengkap'] = $current_user['nama_lengkap'];
        $_SESSION['ekskul'] = $current_user['ekskul'];

        // Redirect berdasarkan role
        if ($current_user['role'] == 'Admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: dashboard_user.php');
        }
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #6b46c1;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2d3748;
        }
        .login-container p {
            font-size: 14px;
            color: #718096;
            margin-bottom: 20px;
        }
        .success-message {
            background-color: #c6f6d5;
            color: #22543d;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: <?php echo isset($logout_message) ? 'block' : 'none'; ?>;
        }
        .error {
            color: #e53e3e;
            margin-bottom: 20px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            font-size: 14px;
            color: #4a5568;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #63b3ed;
            box-shadow: 0 0 0 2px rgba(99, 179, 237, 0.2);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #48bb78;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #38a169;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Masuk ke Akun</h2>
        <p>Masukkan username dan password untuk mengakses dashboard</p>
        <?php if (isset($logout_message)): ?>
            <div class="success-message"><?php echo $logout_message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>