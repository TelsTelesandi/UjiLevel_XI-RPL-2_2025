<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
include '../config/db.php';

// Rekap status
$rekap = [
    'menunggu' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'closed' => 0
];
$q = mysqli_query($conn, "SELECT status, COUNT(*) as jml FROM event_pengajuan GROUP BY status");
while ($row = mysqli_fetch_assoc($q)) {
    $status = strtolower($row['status']);
    if (isset($rekap[$status])) $rekap[$status] = $row['jml'];
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin</title>
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
              <a href="dashboard_admin.php" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-700">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li>
              <a href="approval_report.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
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
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">
          <!-- Ringkasan Status -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow text-center p-6">
              <h6 class="text-gray-500 mb-1">Menunggu</h6>
              <h3 class="text-2xl font-bold text-yellow-500"><?= $rekap['menunggu'] ?></h3>
            </div>
            <div class="bg-white rounded-xl shadow text-center p-6">
              <h6 class="text-gray-500 mb-1">Disetujui</h6>
              <h3 class="text-2xl font-bold text-green-500"><?= $rekap['disetujui'] ?></h3>
            </div>
            <div class="bg-white rounded-xl shadow text-center p-6">
              <h6 class="text-gray-500 mb-1">Ditolak</h6>
              <h3 class="text-2xl font-bold text-red-500"><?= $rekap['ditolak'] ?></h3>
            </div>
          </div>
          <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4">
            <h5 class="text-lg font-bold text-gray-800">Daftar Pengajuan Event</h5>
            <a href="manajemen_user.php" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-yellow-600 text-sm flex items-center gap-2"><i class="fas fa-users-cog"></i> Manajemen User</a>
          </div>
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
                  <?php
                  $verif_status = strtolower($row['verif_status'] ?? '');
                  $status = strtolower($row['status']);
                  $statusColor = [
                      'menunggu' => 'bg-yellow-100 text-yellow-800',
                      'disetujui' => 'bg-green-100 text-green-800',
                      'ditolak'   => 'bg-red-100 text-red-800',
                      'closed'    => 'bg-gray-100 text-gray-800'
                  ];

                  // Jika sudah di-close oleh user/admin, status tampil closed
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
                        <?= htmlspecialchars($row['nama_lengkap'] ?? '') ?>
                      </span>
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
                      <a href="#" onclick='openModalDetail(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-eye mr-1"></i> Detail
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script>
    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.w-64').classList.toggle('hidden');
    });
  </script>
  <!-- Modal Detail Event -->
  <div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
            <i class="fas fa-times text-xl"></i>
        </button>
        <h2 class="text-xl font-bold mb-2">Detail Event</h2>
        <div class="mb-4">
            <p><span class="font-semibold">Judul:</span> <span id="eventTitle">-</span></p>
            <p><span class="font-semibold">Jenis:</span> <span id="eventJenis">-</span></p>
            <p><span class="font-semibold">Tanggal:</span> <span id="eventTanggal">-</span></p>
            <p><span class="font-semibold">Status:</span> <span id="eventStatus">-</span></p>
            <p><span class="font-semibold">Deskripsi:</span> <span id="eventDeskripsi">-</span></p>
            <p><span class="font-semibold">Proposal:</span> <a id="eventProposal" href="#" class="text-primary underline" target="_blank">Download</a></p>
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
        </div>
    </div>
  </div>
  <script>
    function openModalDetail(row) {
        document.getElementById('eventTitle').textContent = row.judul_event;
        document.getElementById('eventJenis').textContent = row.jenis_kegiatan;
        document.getElementById('eventTanggal').textContent = row.tanggal_pengajuan;
        document.getElementById('eventStatus').textContent = row.status;
        document.getElementById('eventDeskripsi').textContent = row.deskripsi;
        document.getElementById('eventProposal').href = "../uploads/" + row.proposal;
        document.getElementById('modalDetail').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modalDetail').classList.add('hidden');
    }
  </script>
</body>
</html>