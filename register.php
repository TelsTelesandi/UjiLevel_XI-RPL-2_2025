<?php
require_once 'config/database.php';
startSession();

// Redirect if already logged in
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

$error = '';
$success = '';

// List ekstrakurikuler
$ekskul_list = [
    'Pramuka', 'PMR', 'Paskibra', 'Basket', 'Futsal', 'Voli', 
    'Paduan Suara', 'Teater', 'Tari', 'Karya Ilmiah', 'Robotik', 'Fotografi'
];

if ($_POST) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $ekskul = $_POST['ekskul'];
    
    // Validasi
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($confirm_password) || empty($ekskul)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Cek username sudah dipakai atau belum
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username sudah digunakan';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $query = "INSERT INTO users (username, password, nama_lengkap, ekskul, role, created_at) 
                      VALUES (?, ?, ?, ?, 'user', NOW())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $username);
            $stmt->bindParam(2, $hashed_password);
            $stmt->bindParam(3, $nama_lengkap);
            $stmt->bindParam(4, $ekskul);
            
            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login dengan akun baru Anda';
            } else {
                $error = 'Terjadi kesalahan, silakan coba lagi';
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
    <title>Registrasi - Sistem Pengajuan Event Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Sistem Pengajuan Event</h1>
                <p class="text-gray-600">Ekstrakurikuler Sekolah</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-xl p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-center text-gray-900">Registrasi Akun</h2>
                    <p class="text-center text-gray-600 mt-2">Buat akun baru untuk mengajukan event</p>
                </div>
                
                <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php echo $success; ?>
                    <p class="mt-2">
                        <a href="index.php" class="text-green-700 font-medium underline">Login sekarang</a>
                    </p>
                </div>
                <?php else: ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan username">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan password (min. 6 karakter)">
                            <button type="button" onclick="togglePassword('password')" 
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Konfirmasi password">
                            <button type="button" onclick="togglePassword('confirm_password')" 
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label for="ekskul" class="block text-sm font-medium text-gray-700 mb-2">Ekstrakurikuler</label>
                        <select id="ekskul" name="ekskul" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Ekstrakurikuler</option>
                            <?php foreach ($ekskul_list as $ekskul_item): ?>
                            <option value="<?php echo $ekskul_item; ?>"><?php echo $ekskul_item; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar
                    </button>
                </form>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah memiliki akun? 
                        <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 