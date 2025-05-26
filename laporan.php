<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle approve/reject actions
if (isset($_POST['action']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $action = $_POST['action'];
    $status = ($action === 'approve') ? 'disetujui' : 'ditolak';
    $catatan = $_POST['catatan'] ?? '';
    $admin_id = $_SESSION['user_id'];

    // Update event status
    $stmt = $conn->prepare("UPDATE event_pengajuan SET status = ? WHERE event_id = ?");
    $stmt->bind_param("si", $status, $event_id);

    if ($stmt->execute()) {
        // Insert verification record
        $stmt2 = $conn->prepare("INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, status) VALUES (?, ?, NOW(), ?, ?)");
        $stmt2->bind_param("iiss", $event_id, $admin_id, $catatan, $status);
        $stmt2->execute();

        header("Location: laporan.php?success=Status event berhasil diperbarui");
        exit;
    }
}

// Get all events with user details
$result = $conn->query("SELECT e.*, u.nama_lengkap, u.ekskul 
                       FROM event_pengajuan e 
                       JOIN users u ON e.user_id = u.user_id 
                       ORDER BY e.tanggal_pengajuan DESC");

if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Event - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-rose-50 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-rose-100 to-pink-100 shadow-xl p-6 min-h-screen">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-rose-600">Admin Panel</h2>
            <p class="text-sm text-rose-500"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
        </div>

        <nav class="space-y-2">
            <a href="dashboard_admin.php" class="block px-4 py-2 rounded-lg text-rose-600 hover:bg-white hover:shadow transition">
                Dashboard
            </a>
            <a href="laporan.php" class="block px-4 py-2 rounded-lg bg-white text-rose-600 shadow">
                Laporan
            </a>
            <a href="logout.php" class="block px-4 py-2 rounded-lg text-rose-600 hover:bg-white hover:shadow transition">
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-rose-600 mb-2">Laporan Event</h1>
                <p class="text-purple-600">Kelola dan verifikasi pengajuan event</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ekskul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengaju</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($row['judul_event']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($row['jenis_kegiatan']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?= htmlspecialchars($row['ekskul']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?= htmlspecialchars($row['nama_lengkap']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php
                                                switch ($row['status']) {
                                                    case 'disetujui':
                                                        echo 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'ditolak':
                                                        echo 'bg-red-100 text-red-800';
                                                        break;
                                                    case 'selesai':
                                                        echo 'bg-blue-100 text-blue-800';
                                                        break;
                                                    default:
                                                        echo 'bg-yellow-100 text-yellow-800';
                                                }
                                                ?>">
                                                <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            Rp<?= number_format($row['total_pembiayaan'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium space-y-2">
                                            <!-- Download Documents -->
                                            <a href="download_file.php?file=<?= urlencode($row['proposal']) ?>"
                                                class="text-purple-600 hover:text-purple-900 block">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Proposal
                                                </div>
                                            </a>

                                            <?php if ($row['status'] === 'menunggu'): ?>
                                                <!-- Approve/Reject Buttons -->
                                                <div class="flex space-x-2 mt-2">
                                                    <button onclick="showActionModal('approve', <?= $row['event_id'] ?>, '<?= htmlspecialchars($row['judul_event']) ?>')"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Setujui
                                                    </button>
                                                    <button onclick="showActionModal('reject', <?= $row['event_id'] ?>, '<?= htmlspecialchars($row['judul_event']) ?>')"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        Tolak
                                                    </button>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($row['status'] === 'selesai'): ?>
                                                <!-- Download Completed Event Files -->
                                                <a href="download_file.php?file=<?= urlencode($row['laporan']) ?>"
                                                    class="text-blue-600 hover:text-blue-900 block mt-2">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Laporan
                                                    </div>
                                                </a>
                                                <a href="download_file.php?file=<?= urlencode($row['dokumentasi']) ?>"
                                                    class="text-blue-600 hover:text-blue-900 block">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Dokumentasi
                                                    </div>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada pengajuan event.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle"></h3>
            <form id="actionForm" method="POST">
                <input type="hidden" name="event_id" id="eventId">
                <input type="hidden" name="action" id="actionType">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Admin
                    </label>
                    <textarea name="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-rose-500"
                        placeholder="Tambahkan catatan untuk pengaju event..."></textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-white transition" id="submitButton">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showActionModal(action, eventId, eventTitle) {
            const modal = document.getElementById('actionModal');
            const modalTitle = document.getElementById('modalTitle');
            const actionForm = document.getElementById('actionForm');
            const eventIdInput = document.getElementById('eventId');
            const actionTypeInput = document.getElementById('actionType');
            const submitButton = document.getElementById('submitButton');

            const isApprove = action === 'approve';
            modalTitle.textContent = `${isApprove ? 'Setujui' : 'Tolak'} Event: ${eventTitle}`;
            eventIdInput.value = eventId;
            actionTypeInput.value = action;

            submitButton.className = `px-4 py-2 rounded-lg text-white transition ${
            isApprove ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'
        }`;
            submitButton.textContent = isApprove ? 'Setujui' : 'Tolak';

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('actionModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>

</html>