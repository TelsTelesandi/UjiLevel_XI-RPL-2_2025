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

// Ambil data pengajuan event beserta info user dari database
// Menampilkan semua status agar admin bisa melihat riwayat
$sql = "SELECT ";
$sql .= "ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.total_pembiyaan, ep.Proposal, ep.deskripsi, ep.tanggal_pengajuan, ep.status, ";
$sql .= "u.username, u.nama_lengkap, u.Ekskul ";
$sql .= "FROM event_pengajuan ep ";
$sql .= "JOIN users u ON ep.user_id = u.user_id ";
$sql .= "ORDER BY ep.tanggal_pengajuan DESC";

$result = $conn->query($sql);

$pengajuan_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pengajuan_list[] = $row;
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
    <title>Daftar Pengajuan Event - Admin - Aplikasi Pengajuan Event Ekstrakurikuler</title>
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
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Daftar Pengajuan Event</h1>
            <p class="text-gray-600">Berikut adalah daftar seluruh pengajuan event yang masuk.</p>
        </div>

        <?php if (count($pengajuan_list) > 0): ?>
            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Event</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju (Ekskul)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($pengajuan_list as $pengajuan): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($pengajuan['nama_lengkap'] . ' (' . $pengajuan['Ekskul'] . ')'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($pengajuan['tanggal_pengajuan']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                     <?php 
                                        $status_class = '';
                                        $status_text = ucfirst($pengajuan['status']);
                                        switch ($pengajuan['status']) {
                                            case 'menunggu':
                                                $status_class = 'text-yellow-600';
                                                break;
                                            case 'disetujui':
                                                $status_class = 'text-green-600';
                                                break;
                                            case 'ditolak':
                                                $status_class = 'text-red-600';
                                                break;
                                            case 'closed':
                                                $status_class = 'text-gray-600';
                                                break;
                                        }
                                     ?>
                                     <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($status_text); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($pengajuan['Proposal']): ?>
                                        <a href="<?php echo htmlspecialchars($pengajuan['Proposal']); ?>" target="_blank" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out">Lihat Proposal</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($pengajuan['status'] === 'menunggu'): ?>
                                            <a href="admin_process_request.php?action=approve&event_id=<?php echo $pengajuan['event_id']; ?>" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out">Setujui</a>
                                            <a href="admin_process_request.php?action=reject&event_id=<?php echo $pengajuan['event_id']; ?>" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out" onclick="return confirm('Tolak pengajuan ini?');">Tolak</a>
                                        <?php endif; ?>
                                        <a href="admin_edit_request.php?id=<?php echo $pengajuan['event_id']; ?>" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out">Edit</a>
                                        <a href="admin_process_request.php?action=delete&event_id=<?php echo $pengajuan['event_id']; ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan event ini?');">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-700 text-center">Belum ada pengajuan event.</p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html> 