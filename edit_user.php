<?php
include 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $ekskul = $_POST['ekskul'];
    $role = $_POST['role'];

    // Check if password should be updated
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, nama_lengkap=?, ekskul=?, role=? WHERE user_id=?");
        $stmt->bind_param("sssssi", $username, $password, $nama_lengkap, $ekskul, $role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, ekskul=?, role=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $username, $nama_lengkap, $ekskul, $role, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data pengguna berhasil diperbarui!";
        header("Location: user_crud.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui data pengguna!";
    }
}

// Get user data
$stmt = $conn->prepare("SELECT username, nama_lengkap, ekskul, role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "Pengguna tidak ditemukan!";
    header("Location: user_crud.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-rose-50 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-rose-100 to-pink-100 shadow-xl p-6">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-rose-600">Admin Panel</h2>
            <p class="text-sm text-rose-500"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
        </div>

        <nav class="space-y-4">
            <a href="dashboard_admin.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="user_crud.php" class="flex items-center text-rose-600 hover:text-rose-700 transition font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola User
            </a>
            <a href="laporan.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan
            </a>
            <a href="logout.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-rose-600">Edit Pengguna</h1>
            </div>

            <!-- Message Display -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Edit User Form -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <form action="" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ekskul</label>
                            <input type="text" name="ekskul" value="<?= htmlspecialchars($user['ekskul']) ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500">
                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="user_crud.php"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-rose-500 text-white px-4 py-2 rounded-md hover:bg-rose-600 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>