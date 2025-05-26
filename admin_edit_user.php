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
$user_data = null;
$user_id_to_edit = null;

// Ambil user_id dari parameter URL
if (isset($_GET['user_id'])) {
    $user_id_to_edit = intval($_GET['user_id']);

    // Ambil data user yang akan diedit
    $sql_get_user = "SELECT user_id, username, role, nama_lengkap, Ekskul FROM users WHERE user_id = ?";
    $stmt_get_user = $conn->prepare($sql_get_user);
    if ($stmt_get_user) {
        $stmt_get_user->bind_param("i", $user_id_to_edit);
        $stmt_get_user->execute();
        $result_get_user = $stmt_get_user->get_result();

        if ($result_get_user->num_rows === 1) {
            $user_data = $result_get_user->fetch_assoc();
        } else {
            $message = "Pengguna tidak ditemukan!";
            $message_type = "error";
             $user_id_to_edit = null; // Reset jika user tidak ditemukan
        }
        $stmt_get_user->close();
    } else {
         $message = "Error menyiapkan statement database: " . $conn->error;
         $message_type = "error";
          $user_id_to_edit = null; // Reset jika error
    }
} else {
    $message = "ID Pengguna tidak spesifik!";
    $message_type = "error";
}

// Cek jika form disubmit untuk update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id_to_update = intval($_POST['user_id']);
    $username = $_POST['username'];
    $role = $_POST['role'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $ekskul = $_POST['ekskul'];
    $password = $_POST['password']; // Password baru (opsional)

    // Validasi sederhana
    if (empty($username) || empty($role) || empty($nama_lengkap) || empty($ekskul)) {
        $message = "Username, Role, Nama Lengkap, dan Ekskul wajib diisi!";
        $message_type = "error";
    } else {
        // Siapkan query UPDATE
        $sql_update = "UPDATE users SET username = ?, role = ?, nama_lengkap = ?, Ekskul = ?";
        $params = [ $username, $role, $nama_lengkap, $ekskul];
        $types = "ssss";

        // Tambahkan password jika diisi
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_update .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $sql_update .= " WHERE user_id = ?";
        $params[] = $user_id_to_update;
        $types .= "i";

        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update) {
            // Bind parameter secara dinamis
            $stmt_update->bind_param($types, ...$params);

            if ($stmt_update->execute()) {
                $message = "Data pengguna berhasil diperbarui!";
                $message_type = "success";
                // Refresh user data after update
                 $sql_get_user_after_update = "SELECT user_id, username, role, nama_lengkap, Ekskul FROM users WHERE user_id = ?";
                 $stmt_get_user_after_update = $conn->prepare($sql_get_user_after_update);
                 $stmt_get_user_after_update->bind_param("i", $user_id_to_update);
                 $stmt_get_user_after_update->execute();
                 $result_get_user_after_update = $stmt_get_user_after_update->get_result();
                 $user_data = $result_get_user_after_update->fetch_assoc();
                 $stmt_get_user_after_update->close();

                // Opsional: Redirect setelah sukses
                // header("Location: admin_manage_users.php");
                // exit();
            } else {
                $message = "Error saat memperbarui data pengguna: " . $stmt_update->error;
                $message_type = "error";
            }
            $stmt_update->close();
        } else {
            $message = "Error menyiapkan statement database: " . $conn->error;
            $message_type = "error";
        }
    }
     // Tutup koneksi hanya jika request adalah POST (setelah update)
    $conn->close();
} else if ($_SERVER["REQUEST_METHOD"] !== "POST" && $conn) {
     // Tutup koneksi jika request bukan POST (untuk tampilan form awal)
     $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Admin - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center mb-8">Edit Pengguna (Admin)</h1>
        
        <div class="mb-4">
            <a href="admin_manage_users.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-block"><- Kembali ke Manajemen Pengguna</a>
        </div>

        <?php if ($message): ?>
            <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($user_data): ?>
        <form action="admin_edit_user.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_data['user_id']); ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>

             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_lengkap">
                    Nama Lengkap
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama_lengkap" type="text" placeholder="Nama Lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" required>
            </div>

             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="ekskul">
                    Nama Ekskul
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="ekskul" type="text" placeholder="Nama Ekskul" name="ekskul" value="<?php echo htmlspecialchars($user_data['Ekskul']); ?>" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                    Role
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="role" name="role" required>
                    <option value="user" <?php echo ($user_data['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user_data['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

             <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Ganti Password (Opsional)
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Kosongkan jika tidak ingin mengganti" name="password">
                <p class="text-gray-600 text-xs italic">Isi hanya jika ingin mengubah password.</p>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Pengguna
                </button>
            </div>
        </form>
        <?php else: // Tampilkan pesan jika user data tidak ditemukan ?>
             <p class="text-red-600 text-center">Gagal memuat data pengguna untuk diedit.</p>
        <?php endif; ?>
    </div>

</body>
</html> 