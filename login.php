<?php
require_once 'db_connect.php';
session_start();

$error = '';

// Cek apakah form login sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    echo "DEBUG: Attempting login for username: " . $username . "\n"; // DEBUG 1

    // Query database untuk mendapatkan user berdasarkan username
    $sql = "SELECT user_id, username, password, role, nama_lengkap, Ekskul FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "DEBUG: Prepare statement failed: " . $conn->error . "\n"; // DEBUG 2
        $error = "Terjadi kesalahan sistem. Silakan coba lagi.";
    } else {
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            echo "DEBUG: Execute failed: " . $stmt->error . "\n"; // DEBUG 3
            $error = "Terjadi kesalahan sistem. Silakan coba lagi.";
        } else {
            $result = $stmt->get_result();
            echo "DEBUG: Number of rows found: " . $result->num_rows . "\n"; // DEBUG 4

            if ($result->num_rows > 0) {
                // User ditemukan, verifikasi password
                $user = $result->fetch_assoc();
                echo "DEBUG: User found. Role: " . $user['role'] . "\n"; // DEBUG 5
                echo "DEBUG: User details - ID: " . $user['user_id'] . ", Username: " . $user['username'] . ", Nama: " . $user['nama_lengkap'] . "\n"; // DEBUG 5.1
                
                // Asumsikan password di database di-hash menggunakan password_hash()
                if (password_verify($password, $user['password'])) {
                    echo "DEBUG: Password verified successfully.\n"; // DEBUG 6
                    // Password cocok, login berhasil
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['ekskul'] = $user['Ekskul'];

                    echo "DEBUG: Session variables set. Redirecting...\n"; // DEBUG 7
                    // Arahkan ke halaman dashboard sesuai role
                    if ($user['role'] == 'admin') {
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        // Asumsikan role selain 'admin' adalah user biasa (ketua ekskul)
                        header("Location: user_dashboard.php");
                        exit();
                    }
                } else {
                    echo "DEBUG: Password verification failed.\n"; // DEBUG 8
                    // Password salah
                    $error = "Username atau password salah!";
                }
            } else {
                echo "DEBUG: No user found with username: " . $username . "\n"; // DEBUG 9
                // User tidak ditemukan
                $error = "Username atau password salah!";
            }
        }
        $stmt->close();
    }
}

// Tutup koneksi database
if (isset($conn) && $conn) {
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styling for background gradient */
        .login-bg {
            background: linear-gradient(to bottom right, #a78bfa, #8b5cf6);
        }

        /* Optional: Add a simple fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-up {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100 login-bg flex items-center justify-center h-screen p-4">

    <div class="w-full max-w-md p-8 bg-white bg-opacity-90 rounded-xl shadow-2xl animate-fade-in-up backdrop-filter backdrop-blur-lg">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Selamat Datang Kembali</h2>
        <p class="text-center text-gray-600 mb-8">Silakan masuk ke akun Anda</p>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline font-semibold"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="username">
                    Username
                </label>
                <input class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 ease-in-out" id="username" type="text" placeholder="Masukkan Username Anda" name="username" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                    Password
                </label>
                <input class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 ease-in-out" id="password" type="password" placeholder="********" name="password" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-200 ease-in-out transform hover:scale-105" type="submit">
                    Masuk
                </button>
                
            </div>
        </form>
        <p class="text-center text-gray-600 text-xs mt-8">
            &copy; <?php echo date('Y'); ?> Aplikasi Pengajuan Event
        </p>
    </div>

</body>
</html> 