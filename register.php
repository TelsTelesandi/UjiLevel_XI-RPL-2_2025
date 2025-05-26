<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Sistem Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-rose-100 via-rose-200 to-pink-200 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl w-[480px] border border-rose-200">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-rose-600">Register Akun</h1>
            <p class="text-rose-400">Daftar untuk mengakses sistem</p>
        </div>
        
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-rose-600 text-sm font-medium mb-2">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
                </div>
                
                <div>
                    <label class="block text-rose-600 text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
                </div>
            </div>

            <div>
                <label class="block text-rose-600 text-sm font-medium mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required
                    class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-rose-600 text-sm font-medium mb-2">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-rose-600 text-sm font-medium mb-2">Ekskul</label>
                    <input type="text" name="ekskul" required
                        class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
                </div>
            </div>

            <button type="submit" name="register"
                class="w-full py-2 px-4 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg transition duration-200">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-rose-500">
            Sudah punya akun? 
            <a href="login.php" class="font-medium text-rose-600 hover:text-rose-700">Login</a>
        </p>
    </div>

    <?php
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        $ekskul = $_POST['ekskul'];

        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role, ekskul) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $nama_lengkap, $role, $ekskul);
        
        if ($stmt->execute()) {
            echo "<div class='fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>
                    Registrasi berhasil! Silakan login.
                  </div>";
            header("refresh:2;url=login.php");
        } else {
            echo "<div class='fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>
                    Registrasi gagal!
                  </div>";
        }
    }
    ?>
</body>
</html>
