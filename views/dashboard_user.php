<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../index.php");
    exit;
}
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Rekap status
$rekap = [
    'menunggu' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'closed' => 0
];
$q = mysqli_query($conn, "SELECT status, COUNT(*) as jml FROM event_pengajuan WHERE user_id='$user_id' GROUP BY status");
while ($row = mysqli_fetch_assoc($q)) {
    $status = strtolower($row['status']);
    if (isset($rekap[$status])) $rekap[$status] = $row['jml'];
}

// Hitung closed (jika ada status closed, jika tidak, bisa pakai status 'closed' pada enum atau tambahkan field baru)
if (isset($rekap['closed'])) {
    // Sudah ada
} else {
    $rekap['closed'] = 0;
}

// Ambil daftar event user
$events = mysqli_query($conn, "SELECT * FROM event_pengajuan WHERE user_id='$user_id' ORDER BY event_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ekskul Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#F59E0B',
                        success: '#10B981',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white hidden md:block">
            <div class="p-4 flex items-center space-x-2 border-b border-blue-700">
                <i class="fas fa-calendar-alt text-2xl"></i>
                <span class="text-xl font-bold">EventEkskul</span>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-700">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="form_pengajuan.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-calendar-plus w-5"></i>
                            <span>Ajukan Event</span>
                        </a>
                    </li>
                </ul>
                
                <div class="mt-8 pt-4 border-t border-blue-700">
                    <a href="../controllers/logout.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Header -->
            <header class="bg-white shadow-sm md:hidden">
                <div class="flex justify-between items-center p-4">
                    <button id="sidebarToggle" class="text-gray-600">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">EventEkskul</h1>
                    <div class="w-8"></div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="container mx-auto">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            Event Ekskul
                        </h1>
                        <a href="form_pengajuan.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition">
                            <i class="fas fa-plus mr-2"></i>
                            Ajukan Event
                        </a>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <!-- Menunggu Card -->
                        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Menunggu</p>
                                    <h3 class="text-2xl font-bold text-gray-800"><?= $rekap['menunggu'] ?></h3>
                                </div>
                                <div class="bg-yellow-100 p-3 rounded-full">
                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Disetujui Card -->
                        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Disetujui</p>
                                    <h3 class="text-2xl font-bold text-gray-800"><?= $rekap['disetujui'] ?></h3>
                                </div>
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Ditolak Card -->
                        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-400">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Ditolak</p>
                                    <h3 class="text-2xl font-bold text-gray-800"><?= $rekap['ditolak'] ?></h3>
                                </div>
                                <div class="bg-red-100 p-3 rounded-full">
                                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Table -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-lg font-semibold text-gray-800">Daftar Pengajuan Event</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Event</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while($row = mysqli_fetch_assoc($events)): ?>
                                        <?php
                                        $verif_status = strtolower($row['verif_status'] ?? '');
                                        $status = strtolower($row['status']);
                                        $statusColor = [
                                            'menunggu' => 'bg-yellow-100 text-yellow-800',
                                            'disetujui' => 'bg-green-100 text-green-800',
                                            'ditolak'   => 'bg-red-100 text-red-800',
                                            'closed'    => 'bg-gray-100 text-gray-800'
                                        ];

                                        // Jika sudah di-close oleh user, status tampil closed
                                        if ($verif_status == 'closed') {
                                            $status = 'closed';
                                        }
                                        ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900"><?= htmlspecialchars($row['judul_event'] ?? '') ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <?= htmlspecialchars($row['jenis_kegiatan'] ?? '') ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500"><?= htmlspecialchars($row['tanggal_pengajuan'] ?? '') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusColor[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button onclick='openModalDetail(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Menampilkan <span class="font-medium">1</span> dari <span class="font-medium">1</span> event
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </button>
                                <button class="px-3 py-1 border rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    1
                                </button>
                                <button class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Detail Event -->
    <div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-xl font-bold mb-4">Detail Event</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Judul Event</p>
                    <p class="font-medium" id="eventTitle">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Kegiatan</p>
                    <p class="font-medium" id="eventJenis">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                    <p class="font-medium" id="eventTanggal">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pembiayaan</p>
                    <p class="font-medium" id="eventBiaya">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium" id="eventStatus">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Deskripsi</p>
                    <p class="font-medium" id="eventDeskripsi">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Proposal</p>
                    <a id="eventProposal" href="#" class="text-blue-600 hover:text-blue-800 underline" target="_blank">Download Proposal</a>
                </div>
                <?php if(isset($_GET['catatan_admin']) && !empty($_GET['catatan_admin'])): ?>
                <div>
                    <p class="text-sm text-gray-500">Catatan Admin</p>
                    <p class="font-medium text-red-600"><?= htmlspecialchars($_GET['catatan_admin']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Toggle mobile sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.w-64').classList.toggle('hidden');
        });

        // Modal logic
        function openModalDetail(row) {
            document.getElementById('eventTitle').textContent = row.judul_event;
            document.getElementById('eventJenis').textContent = row.jenis_kegiatan;
            document.getElementById('eventTanggal').textContent = row.tanggal_pengajuan;
            document.getElementById('eventBiaya').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(row.total_pembiayaan);
            document.getElementById('eventStatus').textContent = row.status.charAt(0).toUpperCase() + row.status.slice(1);
            document.getElementById('eventDeskripsi').textContent = row.deskripsi;
            document.getElementById('eventProposal').href = "../uploads/" + row.proposal;
            document.getElementById('modalDetail').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }
    </script>

    <?php if (isset($_GET['close'])): ?>
      <?php if ($_GET['close'] == 'success'): ?>
        <div id="notif-success" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50">
          <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-down">
            <i class="fas fa-check-circle text-2xl"></i>
            <span class="font-semibold">Event berhasil di-close!</span>
            <button onclick="document.getElementById('notif-success').remove()" class="ml-4 text-white hover:text-green-200">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      <?php elseif ($_GET['close'] == 'invalid'): ?>
        <div id="notif-error" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50">
          <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-down">
            <i class="fas fa-times-circle text-2xl"></i>
            <span class="font-semibold">Event tidak bisa di-close!</span>
            <button onclick="document.getElementById('notif-error').remove()" class="ml-4 text-white hover:text-red-200">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      <?php endif; ?>
      <script>
        setTimeout(() => {
          const notif = document.getElementById('notif-success') || document.getElementById('notif-error');
          if (notif) notif.remove();
        }, 4000);
      </script>
      <style>
      @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px) scale(0.95);}
        100% { opacity: 1; transform: translateY(0) scale(1);}
      }
      .animate-fade-in-down {
        animation: fade-in-down 0.5s;
      }
      </style>
    <?php endif; ?>
</body>
</html>