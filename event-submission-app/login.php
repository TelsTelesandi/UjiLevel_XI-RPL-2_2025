<?php
require_once 'config/database.php';
require_once 'config/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT user_id, username, password, role, nama_lengkap FROM users WHERE username = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$username]);
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    } else {
        $error = 'Mohon isi semua field!';
    }
}

$page_title = 'Login - Event Submission System';
include 'includes/header.php';
?>

<!-- Login Page -->
<div class="min-h-screen flex items-center justify-center px-4" style="background-image: url('lapangan.jpeg'); background-size: cover; background-position: center;">
    <div class="bg-white shadow-xl rounded-xl p-8 max-w-md w-full space-y-6 border-t-4 border-blue-500">
        <div class="text-center">
            <img src="Logo_SMK_Telekomunikasi_Telesandi_Bekasi.png" alt="Event Submission System" class="mx-auto w-16 mb-4">
            <h1 class="text-4xl font-extrabold text-blue-800">Pengajuan Event Ekstrakurikuler</h1>
            <p class="text-gray-600 mt-1">Silakan login untuk melanjutkan</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

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
            
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold shadow-md transition-all">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <div class="text-center text-sm mt-2">
            <p>Belum punya akun? <a href="register.php" class="text-blue-600 font-medium hover:underline">Daftar di sini</a></p>
        </div>
    </div>
</div>

