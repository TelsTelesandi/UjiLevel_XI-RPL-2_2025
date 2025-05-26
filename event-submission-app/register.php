<?php

require_once('config/database.php');

$database = new Database();
$pdo = $database->getConnection(); // Mendapatkan koneksi PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $eskul = $_POST['eskul'] ?? '';

    // Validasi input
    if ($username && $nama_lengkap && $eskul && $_POST['password']) {
        // Simpan ke database
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, eskul) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $password, $nama_lengkap, $eskul])) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "Gagal mendaftar. Silakan coba lagi.";
        }
    } else {
        $error = "Mohon isi semua field!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center px-4" style="background-image: url('lapangan.jpeg'); background-size: cover; background-position: center;">
    <div class="bg-white shadow-xl rounded-xl p-8 max-w-md w-full space-y-6 border-t-4 border-blue-500">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-blue-800">Registrasi</h1>
            <p class="text-gray-600 mt-1">Silakan isi data di bawah ini</p>
        </div>

        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Jabatan Eskul</label>
                <input id="username" name="username" type="text" required 
                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       placeholder="Jabatan eskul">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required 
                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       placeholder="Masukkan password">
            </div>

            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" type="text" required 
                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       placeholder="Nama lengkap">
            </div>

            <div>
                <label for="eskul" class="block text-sm font-medium text-gray-700">Nama Eskul</label>
                <input id="eskul" name="eskul" type="text" required 
                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       placeholder="Nama eskul">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold shadow-md transition-all">
                <i class="fas fa-user-plus mr-2"></i> Daftar
            </button>
        </form>

        <div class="text-center text-sm mt-2">
            <p>Sudah punya akun? <a href="login.php" class="text-blue-600 font-medium hover:underline">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
