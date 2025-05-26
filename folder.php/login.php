<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $eskul = $_POST['eskul'];
    
    $query = "SELECT * FROM users WHERE username = ? AND password = ? AND eskul = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password, $eskul);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
        $_SESSION['eskul'] = $row['eskul'];
        header("Location: dashboard.php");
    } else {
        $error = "Username, password, atau ekstrakurikuler tidak sesuai!";
    }
}

// Ambil pesan sukses dari session jika ada
$success_message = "";
if(isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Ekstrakurikuler</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../folder.css/login.css">
    <style>
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
        <h2 class="form-title">Login Ekstrakurikuler</h2>
        <?php if($success_message != "") { ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php } ?>
        <?php if(isset($error)) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username">
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
            <button type="submit" name="login" class="btn">Masuk</button>
            <div class="form-footer">
                Belum punya akun? <a href="register.php">Daftar disini</a><br>
                <a href="../admin.php/login_admin.php" style="color:#1a237e;font-weight:500;">Login sebagai Admin</a>
            </div>
        </form>
    </div>
</body>
</html>
