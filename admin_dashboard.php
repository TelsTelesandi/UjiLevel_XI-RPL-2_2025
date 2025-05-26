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
$admin_username = $_SESSION['username'];

// Ambil data ringkasan pengajuan event berdasarkan status
$summary = [
    'total' => 0,
    'menunggu' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'closed' => 0,
];

$sql_summary = "SELECT status, COUNT(*) as count FROM event_pengajuan GROUP BY status";
$result_summary = $conn->query($sql_summary);

if ($result_summary->num_rows > 0) {
    while ($row = $result_summary->fetch_assoc()) {
        if (isset($summary[$row['status']])) {
            $summary[$row['status']] = $row['count'];
        }
        $summary['total'] += $row['count'];
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
    <title>Admin Dashboard - Aplikasi Pengajuan Event Ekstrakurikuler</title>
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

        /* Optional: Simple slide-in animation for sidebar items */
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

    <!-- Sidebar -->
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Selamat Datang, Admin <?php echo htmlspecialchars($admin_username); ?>!</h2>
            <p class="text-gray-600">Ini adalah dashboard admin Anda. Anda dapat melihat ringkasan pengajuan event dan mengelola aplikasi.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg shadow">
                <div class="font-bold text-lg">Total Pengajuan</div>
                <p class="text-xl"><?php echo $summary['total']; ?></p>
            </div>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg shadow">
                <div class="font-bold text-lg">Menunggu Verifikasi</div>
                <p class="text-xl"><?php echo $summary['menunggu']; ?></p>
            </div>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow">
                <div class="font-bold text-lg">Disetujui</div>
                <p class="text-xl"><?php echo $summary['disetujui']; ?></p>
            </div>
             <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow">
                <div class="font-bold text-lg">Ditolak</div>
                <p class="text-xl"><?php echo $summary['ditolak']; ?></p>
            </div>
        </div>

        <!-- Recent Requests (Optional - can add a table here later) -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Status Pengajuan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Pengajuan</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $summary['total']; ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Menunggu Verifikasi</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-700"><?php echo $summary['menunggu']; ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Disetujui</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-700"><?php echo $summary['disetujui']; ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Ditolak</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-700"><?php echo $summary['ditolak']; ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Selesai (Closed)</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700"><?php echo $summary['closed']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <p class="mt-4 text-gray-600">Untuk melihat detail lengkap pengajuan, kunjungi halaman <a href="admin_view_requests.php" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out">Pengajuan Event</a>.</p>
              <p class="mt-2 text-gray-600">Untuk melihat laporan detail, kunjungi halaman <a href="admin_reports.php" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out">Laporan</a>.</p>
        </div>

    </div>

</body>
</html> 