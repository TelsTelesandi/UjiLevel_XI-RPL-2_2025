<?php
include 'koneksi.php';

$success = '';
$error = '';

if(isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];
    $eskul = $_POST['eskul'];
    $role = $_POST['role'];

    // Validasi input sederhana
    if($username === '' || $nama_lengkap === '' || $password === '' || $eskul === '' || $role === '') {
        $error = "Semua field wajib diisi!";
    } else {
        // Cek apakah username sudah ada
        $check_query = "SELECT * FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, nama_lengkap, password, eskul, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $username, $nama_lengkap, $hashed_password, $eskul, $role);

            if($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../folder.css/login.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            overflow-y: auto;
        }
        .container {
            max-width: 350px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .error-message {
            color: #dc3545;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="form-title">Register</h2>
        <?php if($success) { ?>
            <div class="success-message">
                <?php echo $success; ?> <a href="login.php">Login disini</a>
            </div>
        <?php } ?>
        <?php if($error) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="eskul">Ekstrakurikuler</label>
                <select id="eskul" name="eskul" required>
                    <option value="">Pilih Ekstrakurikuler</option>
                    <option value="Drumband">Drumband</option>
                    <option value="Marawis">Marawis</option>
                    <option value="Hadroh">Hadroh</option>
                    <option value="Paduan Suara">Paduan Suara</option>
                    <option value="Angklung">Angklung</option>
                </select>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="register" class="btn">Register</button>
            <div class="form-footer">
                Sudah punya akun? <a href="login.php">Login disini</a>
            </div>
        </form>
    </div>
</body>
</html>
