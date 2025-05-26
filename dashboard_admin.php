<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get admin name
$admin_id = $_SESSION['user_id'];
$getAdmin = $conn->query("SELECT nama_lengkap FROM users WHERE user_id = $admin_id");
$adminData = $getAdmin->fetch_assoc();
$nama = $adminData ? $adminData['nama_lengkap'] : 'Admin';

// Get statistics
$total_pengajuan = $conn->query("SELECT COUNT(*) as total FROM event_pengajuan")->fetch_assoc()['total'];
$menunggu = $conn->query("SELECT COUNT(*) as total FROM event_pengajuan WHERE status='menunggu'")->fetch_assoc()['total'];
$disetujui = $conn->query("SELECT COUNT(*) as total FROM event_pengajuan WHERE status='disetujui'")->fetch_assoc()['total'];
$ditolak = $conn->query("SELECT COUNT(*) as total FROM event_pengajuan WHERE status='ditolak'")->fetch_assoc()['total'];

// Get latest submissions
$latest = $conn->query("SELECT e.*, u.nama_lengkap, u.ekskul 
                       FROM event_pengajuan e 
                       JOIN users u ON e.user_id=u.user_id 
                       ORDER BY tanggal_pengajuan DESC LIMIT 10");

// Get monthly statistics
$monthly = $conn->query("SELECT DATE_FORMAT(tanggal_pengajuan, '%M') as bulan, 
                        COUNT(*) as total 
                        FROM event_pengajuan 
                        GROUP BY DATE_FORMAT(tanggal_pengajuan, '%M'), MONTH(tanggal_pengajuan)
                        ORDER BY MONTH(tanggal_pengajuan) DESC 
                        LIMIT 6");
$labels = [];
$data = [];
while ($row = $monthly->fetch_assoc()) {
    $labels[] = $row['bulan'];
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-rose-50 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-rose-100 to-pink-100 shadow-lg p-6 min-h-screen">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-rose-600">Admin Panel</h2>
            <p class="text-sm text-rose-500"><?= htmlspecialchars($nama) ?></p>
        </div>
        <ul class="space-y-4">
            <li><a href="dashboard_admin.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Dashboard
                </a></li>
            <li><a href="user_crud.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Kelola User
                </a></li>
            <li><a href="laporan.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan
                </a></li>
            <li><a href="logout.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-rose-600 mb-2">Dashboard Admin</h1>
            <p class="text-rose-400">Overview statistik pengajuan event</p>

            <!-- Message Display -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-gray-500 text-sm">Total Pengajuan</h3>
                        <p class="text-2xl font-bold text-purple-700"><?= $total_pengajuan ?></p>
                    </div>
                    <div class="text-purple-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-gray-500 text-sm">Menunggu</h3>
                        <p class="text-2xl font-bold text-yellow-500"><?= $menunggu ?></p>
                    </div>
                    <div class="text-yellow-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-gray-500 text-sm">Disetujui</h3>
                        <p class="text-2xl font-bold text-green-500"><?= $disetujui ?></p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-gray-500 text-sm">Ditolak</h3>
                        <p class="text-2xl font-bold text-red-500"><?= $ditolak ?></p>
                    </div>
                    <div class="text-red-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-purple-700 mb-4">Statistik Status</h3>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-purple-700 mb-4">Trend Bulanan</h3>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- Latest Submissions -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-rose-100 to-purple-100">
                <h3 class="text-lg font-semibold text-purple-700">Pengajuan Terbaru</h3>
            </div>
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ekskul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $latest->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><?= htmlspecialchars($row['judul_event']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['ekskul']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php
                                echo match ($row['status']) {
                                    'disetujui' => 'bg-green-100 text-green-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    default => 'bg-yellow-100 text-yellow-800'
                                };
                                ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                            <td class="px-6 py-4">
                                <a href="#" onclick="confirmDelete(<?= $row['event_id'] ?>)"
                                    class="text-red-600 hover:text-red-800 transition">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Pie Chart
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: ['Menunggu', 'Disetujui', 'Ditolak'],
                datasets: [{
                    data: [<?= $menunggu ?>, <?= $disetujui ?>, <?= $ditolak ?>],
                    backgroundColor: ['#fbbf24', '#34d399', '#ef4444']
                }]
            }
        });

        // Line Chart
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_reverse($labels)) ?>,
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: <?= json_encode(array_reverse($data)) ?>,
                    borderColor: '#8b5cf6',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Delete confirmation
        function confirmDelete(eventId) {
            if (confirm('Apakah Anda yakin ingin menghapus event ini?')) {
                window.location.href = 'delete_event.php?event_id=' + eventId;
            }
        }
    </script>
</body>

</html>