<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

$message = '';
$message_type = '';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $ekskul = $_POST['ekskul'];

    // Validasi sederhana
    if (empty($username) || empty($password) || empty($role) || empty($nama_lengkap) || empty($ekskul)) {
        $message = "Semua field wajib diisi!";
        $message_type = "error";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username sudah ada
        $sql_check = "SELECT user_id FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = "Username sudah terdaftar. Silakan gunakan username lain.";
            $message_type = "error";
        } else {
            // Insert data user baru ke database
            $sql_insert = "INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);

            if ($stmt_insert) {
                $stmt_insert->bind_param("sssss", $username, $hashed_password, $role, $nama_lengkap, $ekskul);

                if ($stmt_insert->execute()) {
                    $message = "Pengguna baru berhasil ditambahkan!";
                    $message_type = "success";
                    // Clear form after success or redirect
                    // header("Location: admin_manage_users.php");
                    // exit();
                } else {
                    $message = "Error saat menambahkan pengguna: " . $stmt_insert->error;
                    $message_type = "error";
                }
                $stmt_insert->close();
            } else {
                 $message = "Error menyiapkan statement database: " . $conn->error;
                 $message_type = "error";
            }
        }

        $stmt_check->close();
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna Baru - Admin - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center mb-8">Tambah Pengguna Baru (Admin)</h1>
        
        <div class="mb-4">
            <a href="admin_manage_users.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-block"><- Kembali ke Manajemen Pengguna</a>
        </div>

        <?php if ($message): ?>
            <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form action="admin_add_user.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Username" name="username" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="********" name="password" required>
            </div>

             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_lengkap">
                    Nama Lengkap
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama_lengkap" type="text" placeholder="Nama Lengkap" name="nama_lengkap" required>
            </div>

             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="ekskul">
                    Nama Ekskul
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="ekskul" type="text" placeholder="Nama Ekskul" name="ekskul" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                    Role
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Tambah Pengguna
                </button>
            </div>
        </form>
    </div>

</body>
</html> 