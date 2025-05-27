<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
include '../config/db.php';

// Ambil semua event beserta nama user
$events = mysqli_query($conn, "
    SELECT e.*, u.nama_lengkap 
    FROM event_pengajuan e 
    JOIN users u ON e.user_id = u.user_id
    ORDER BY e.event_id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval & Report Event | Admin</title>
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
                <i class="fas fa-calendar-check text-2xl"></i>
                <span class="text-xl font-bold">EventEkskul</span>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard_admin.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="approval_report.php" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-700">
                            <i class="fas fa-clipboard-check w-5"></i>
                            <span>Approval & Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="manajemen_user.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-users-cog w-5"></i>
                            <span>Manajemen User</span>
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
                <div class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Approval & Report Event</h1>
                            <p class="text-gray-600">Kelola pengajuan event dan lihat rekap laporan pengajuan</p>
                        </div> 
                    </div>

                    <!-- Table Approval & Report -->
                    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Judul Event</th>
                                    <th class="py-3 px-4 text-left">Ketua Ekskul</th>
                                    <th class="py-3 px-4 text-left">Jenis</th>
                                    <th class="py-3 px-4 text-left">Tanggal</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($events)): ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= htmlspecialchars($row['judul_event'] ?? '-') ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($row['jenis_kegiatan'] ?? '-') ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($row['tanggal_pengajuan'] ?? '-') ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            $status = strtolower($row['status'] ?? '');
                                            $statusColor = [
                                                'menunggu' => 'bg-yellow-100 text-yellow-800',
                                                'disetujui' => 'bg-green-100 text-green-800',
                                                'ditolak'   => 'bg-red-100 text-red-800',
                                                'closed'    => 'bg-gray-100 text-gray-800'
                                            ];
                                            ?>
                                            <span class="px-2 py-1 rounded <?= $statusColor[$status] ?? 'bg-gray-100 text-gray-800' ?> text-xs"><?= ucfirst($status) ?></span>
                                        </td>
                                        <td class="py-3 px-4 flex gap-2">
                                            <button onclick='openModalDetail(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="px-3 py-1 bg-primary text-white rounded hover:bg-blue-700 text-xs flex items-center gap-1">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <?php if($status == 'menunggu'): ?>
                                            <button onclick="openApprove(<?= $row['event_id'] ?>, '<?= htmlspecialchars(addslashes($row['judul_event'])) ?>')" class="px-3 py-1 bg-success text-white rounded hover:bg-green-700 text-xs flex items-center gap-1">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                            <button onclick="openReject(<?= $row['event_id'] ?>, '<?= htmlspecialchars(addslashes($row['judul_event'])) ?>')" class="px-3 py-1 bg-danger text-white rounded hover:bg-red-700 text-xs flex items-center gap-1">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Export/Report -->
                    <div class="flex justify-end mt-6">
                        <a href="../controllers/export_controller.php" class="px-5 py-2 bg-secondary text-white rounded-lg hover:bg-yellow-600 flex items-center gap-2">
                            <i class="fas fa-file-export"></i> Export Laporan
                        </a>
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
            <h2 class="text-xl font-bold mb-2">Detail Event</h2>
            <div class="mb-4">
                <p><span class="font-semibold">Judul:</span> <span id="eventTitle">-</span></p>
                <p><span class="font-semibold">Ketua Ekskul:</span> <span id="eventKetua">-</span></p>
                <p><span class="font-semibold">Jenis:</span> <span id="eventJenis">-</span></p>
                <p><span class="font-semibold">Tanggal:</span> <span id="eventTanggal">-</span></p>
                <p><span class="font-semibold">Total Pembiayaan:</span> Rp <span id="eventBiaya">-</span></p>
                <p><span class="font-semibold">Status:</span> <span id="eventStatus">-</span></p>
                <p><span class="font-semibold">Deskripsi:</span> <span id="eventDeskripsi">-</span></p>
                <p><span class="font-semibold">Proposal:</span> <a id="eventProposal" href="#" class="text-primary underline" target="_blank">Download</a></p>
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div id="modalApprove" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
            <button onclick="closeApprove()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-xl font-bold mb-4">Setujui Event</h2>
            <p>Apakah Anda yakin ingin <span class="font-semibold text-success">menyetujui</span> event <span id="approveTitle" class="font-semibold"></span>?</p>
            <form class="mt-4" action="../controllers/approval_controller.php" method="POST">
                <input type="hidden" name="event_id" id="approveEventId">
                <label class="block text-sm font-medium mb-1">Catatan Admin (opsional)</label>
                <textarea name="catatan_admin" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4" rows="3"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeApprove()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" name="approve" class="px-4 py-2 bg-success text-white rounded hover:bg-green-700">Setujui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reject -->
    <div id="modalReject" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
            <button onclick="closeReject()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-xl font-bold mb-4">Tolak Event</h2>
            <p>Apakah Anda yakin ingin <span class="font-semibold text-danger">menolak</span> event <span id="rejectTitle" class="font-semibold"></span>?</p>
            <form class="mt-4" action="../controllers/approval_controller.php" method="POST">
                <input type="hidden" name="event_id" id="rejectEventId">
                <label class="block text-sm font-medium mb-1">Catatan Admin (wajib)</label>
                <textarea name="catatan_admin" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4" rows="3" required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeReject()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" name="reject" class="px-4 py-2 bg-danger text-white rounded hover:bg-red-700">Tolak</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50">
      <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-down">
        <i class="fas fa-check-circle text-2xl"></i>
        <span class="font-semibold">
          <?php if ($_GET['success'] == 'approve') echo "Event berhasil disetujui!"; else echo "Event berhasil ditolak!"; ?>
        </span>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-green-200">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <script>
      setTimeout(() => {
        const notif = document.querySelector('.fixed.top-6');
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

    <script>
        // Sidebar toggle (mobile)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.w-64').classList.toggle('hidden');
        });

        // Modal logic
        function openModalDetail(row) {
            // row adalah object event, tampilkan detail di modal
            document.getElementById('eventTitle').textContent = row.judul_event;
            document.getElementById('eventKetua').textContent = row.nama_lengkap;
            document.getElementById('eventJenis').textContent = row.jenis_kegiatan;
            document.getElementById('eventTanggal').textContent = row.tanggal_pengajuan;
            document.getElementById('eventBiaya').textContent = row.total_pembiayaan;
            document.getElementById('eventStatus').textContent = row.status;
            document.getElementById('eventDeskripsi').textContent = row.deskripsi;
            document.getElementById('eventProposal').href = "../uploads/" + row.proposal;
            document.getElementById('modalDetail').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }
        function openApprove(eventId, judul) {
            document.getElementById('approveEventId').value = eventId;
            document.getElementById('approveTitle').textContent = judul;
            document.getElementById('modalApprove').classList.remove('hidden');
        }
        function closeApprove() {
            document.getElementById('modalApprove').classList.add('hidden');
        }
        function openReject(eventId, judul) {
            document.getElementById('rejectEventId').value = eventId;
            document.getElementById('rejectTitle').textContent = judul;
            document.getElementById('modalReject').classList.remove('hidden');
        }
        function closeReject() {
            document.getElementById('modalReject').classList.add('hidden');
        }
    </script>
</body>
</html>
