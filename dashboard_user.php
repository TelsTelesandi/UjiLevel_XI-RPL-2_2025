<?php


include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$getUser = $conn->query("SELECT nama_lengkap, ekskul FROM users WHERE user_id = $user_id");
$userData = $getUser->fetch_assoc();
$nama = $userData['nama_lengkap'];
$ekskul = $userData['ekskul'];

$result = $conn->query("SELECT * FROM event_pengajuan WHERE user_id = $user_id ORDER BY tanggal_pengajuan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User - Sistem Event</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-rose-50 to-purple-50 min-h-screen flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-rose-100 to-pink-100 shadow-xl p-6">
    <div class="mb-8">
      <h2 class="text-xl font-bold text-rose-600">Menu User</h2>
      <p class="text-sm text-rose-500"><?= htmlspecialchars($nama) ?></p>
      <p class="text-xs text-purple-500"><?= htmlspecialchars($ekskul) ?></p>
    </div>
    
    <nav class="space-y-4">
      <a href="dashboard_user.php" class="flex items-center text-rose-600 hover:text-rose-700 transition font-bold">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </a>
      <a href="event_form.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Ajukan Event
      </a>
      <a href="logout.php" class="flex items-center text-rose-600 hover:text-rose-700 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Logout
      </a>
    </nav>
  </aside>

  <!-- Content -->
  <main class="flex-1 p-8">
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-rose-600 mb-2">Selamat datang, <?= htmlspecialchars($nama) ?>!</h1>
      <p class="text-purple-600">Berikut adalah daftar pengajuan event dari ekskul <?= htmlspecialchars($ekskul) ?></p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <a href="event_form.php" 
         class="inline-flex items-center px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Ajukan Event Baru
      </a>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="p-6 bg-gradient-to-r from-rose-50 to-purple-50 border-b">
        <h2 class="text-lg font-semibold text-rose-600">Daftar Pengajuan Event</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Event</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['judul_event']) ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500"><?= htmlspecialchars($row['jenis_kegiatan']) ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500"><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                      <?php
                        echo match($row['status']) {
                          'disetujui' => 'bg-green-100 text-green-800',
                          'ditolak' => 'bg-red-100 text-red-800',
                          'selesai' => 'bg-purple-100 text-purple-800',
                          default => 'bg-yellow-100 text-yellow-800'
                        };
                      ?>">
                      <?= ucfirst(htmlspecialchars($row['status'])) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">Rp<?= number_format($row['total_pembiayaan'], 0, ',', '.') ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="uploads/<?= $row['proposal'] ?>" target="_blank" 
                       class="text-purple-600 hover:text-purple-900 mr-3">Proposal</a>
                    <?php if($row['status'] == 'disetujui'): ?>
                      <a href="#" onclick="confirmCloseEvent(<?= $row['event_id'] ?>, '<?= htmlspecialchars($row['judul_event']) ?>')"
                         class="text-rose-600 hover:text-rose-900">Tutup Event</a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  Belum ada pengajuan event. <a href="event_form.php" class="text-rose-600 hover:underline">Ajukan event baru</a>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Confirmation Modal -->
  <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Tutup Event</h3>
      <p class="text-gray-600 mb-6" id="modalMessage"></p>
      <div class="flex justify-end space-x-4">
        <button onclick="closeModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
          Batal
        </button>
        <button onclick="proceedToCloseEvent()"
                class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition">
          Ya, Tutup Event
        </button>
      </div>
    </div>
  </div>

  <script>
  let eventToClose = null;

  function confirmCloseEvent(eventId, eventTitle) {
    eventToClose = eventId;
    const modal = document.getElementById('confirmModal');
    const message = document.getElementById('modalMessage');
    
    message.textContent = `Apakah Anda yakin ingin menutup event "${eventTitle}"? 
                          Anda akan diminta untuk mengupload laporan dan dokumentasi kegiatan.`;
    
    modal.classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    eventToClose = null;
  }

  function proceedToCloseEvent() {
    if (eventToClose) {
      window.location.href = `close_request.php?event_id=${eventToClose}`;
    }
  }

  // Close modal when clicking outside
  document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeModal();
    }
  });
  </script>
</body>
</html>