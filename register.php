<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $username     = $koneksi->real_escape_string($_POST['username']);
    $nama_lengkap = $koneksi->real_escape_string($_POST['namalengkap']);
    $eskul        = $koneksi->real_escape_string($_POST['eskul']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = $koneksi->query("SELECT * FROM users WHERE username='$username'");
    if ($cek && $cek->num_rows > 0) {
        $msg = "Username sudah terdaftar!";
    } else {
        $insert = $koneksi->query("INSERT INTO users (username, password, role, nama_lengkap, Eskul) VALUES ('$username', '$password', 'user', '$nama_lengkap', '$eskul')");
        if ($insert) {
            $msg = "Registrasi berhasil! <a href='login.php'>Login</a>";
        } else {
            $msg = "Registrasi gagal! " . $koneksi->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 370px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            padding: 32px 24px;
        }
        h2 {
            text-align: center;
            color: #8e44ad;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #8e44ad;
            border-radius: 6px;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #8e44ad;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #6c3483;
        }
        .link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #2980b9;
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
        <h2>Register Akun</h2>
        <?php if (isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <form method="POST">
            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="namalengkap" required>
            </div>
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Ekstrakurikuler</label>
                <input type="text" name="eskul" required placeholder="Contoh: Pramuka, Paskibra, dll">
                <!--
                Jika ingin dropdown, ganti dengan:
                <select name="eskul" required>
                    <option value="">Pilih Ekstrakurikuler</option>
                    <option value="Pramuka">Pramuka</option>
                    <option value="Paskibra">Paskibra</option>
                    <option value="PMR">PMR</option>
                    <option value="Futsal">Futsal</option>
                </select>
                -->
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
        <a class="link" href="login.php">Sudah punya akun? Login</a>
    </div>
</body>
</html>