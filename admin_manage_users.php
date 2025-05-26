<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

// Data admin yang sedang login
$admin_id = $_SESSION['user_id'];
$admin_username = $_SESSION['username']; // Ambil juga username untuk tampilan

// Ambil semua data user dari database
$sql = "SELECT user_id, username, role, nama_lengkap, Ekskul FROM users ORDER BY user_id DESC";
$result = $conn->query($sql);

$users_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users_list[] = $row;
    }
}

$conn->close();

// Determine current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar for content */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Optional: Simple slide-in animation for sidebar items (copy from dashboard) */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-slide-in-left {
             animation: slideInLeft 0.4s ease-out;
        }
         /* Delay for staggered animation */
        .animate-slide-in-left.delay-100 { animation-delay: 0.1s; }
        .animate-slide-in-left.delay-200 { animation-delay: 0.2s; }
        .animate-slide-in-left.delay-300 { animation-delay: 0.3s; }
        .animate-slide-in-left.delay-400 { animation-delay: 0.4s; }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal flex h-screen overflow-hidden">

    <!-- Sidebar (Copy from admin_dashboard.php) -->
    <div class="bg-gray-900 text-gray-300 w-64 flex flex-col justify-between shadow-lg">
        <div class="py-6 px-4">
            <h1 class="text-3xl font-extrabold text-white mb-8">Admin Panel</h1>
            <nav class="space-y-3">
                <a href="admin_dashboard.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_dashboard.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left">
                    Dashboard
                </a>
                <a href="admin_view_requests.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_view_requests.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-100">
                    Pengajuan Event
                </a>
                <a href="admin_manage_users.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_manage_users.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-200">
                    Manajemen Pengguna
                </a>
                <a href="admin_reports.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_reports.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-300">
                    Laporan
                </a>
            </nav>
        </div>
        <div class="py-4 px-6 border-t border-gray-700">
             <a href="logout.php" class="block py-2.5 px-4 text-red-400 rounded-lg transition duration-200 hover:bg-gray-700 hover:text-red-500 animate-slide-in-left delay-400">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Manajemen Pengguna</h1>
            <p class="text-gray-600">Kelola akun pengguna (ketua ekskul dan admin).</p>
        </div>

        <div class="mb-6">
             <a href="admin_add_user.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out">+ Tambah Pengguna Baru</a>
        </div>

        <?php if (count($users_list) > 0): ?>
            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ekskul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users_list as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['Ekskul']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="admin_edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded-lg text-xs mr-2 transition duration-200 ease-in-out">Edit</a>
                                    <?php if ($user['role'] !== 'admin'): // Admin tidak bisa menghapus admin lain ?>
                                         <a href="admin_delete_user.php?user_id=<?php echo $user['user_id']; ?>" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded-lg text-xs transition duration-200 ease-in-out" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-700 text-center">Belum ada pengguna terdaftar selain mungkin admin.</p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html> 