<?php
require_once '../config/database.php';
requireAdmin();

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Handle approval/rejection
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve') {
        $event_id = $_POST['event_id'];
        $status = $_POST['status'];
        $catatan = $_POST['catatan'];
        $admin_id = $_SESSION['user_id'];
        
        // Update event status
        $query = "UPDATE event_pengajuan SET status = ? WHERE event_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$status, $event_id]);
        
        // Insert verification record
        $query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$event_id, $admin_id, date('Y-m-d H:i:s'), $catatan, 'verified']);
        
        $message = 'Status event berhasil diperbarui';
    }
}

// Get all events with user info
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT ep.*, u.nama_lengkap, u.ekskul 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          WHERE (ep.judul_event LIKE ? OR u.nama_lengkap LIKE ? OR u.ekskul LIKE ?)";

$params = ["%$search%", "%$search%", "%$search%"];

if ($status_filter) {
    $query .= " AND ep.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY ep.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Event - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Event</h1>
                <p class="text-gray-600">Kelola pengajuan event ekstrakurikuler</p>
            </div>
            
            <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Pengajuan Event</h2>
                    
                    <div class="flex flex-col md:flex-row gap-4">
                        <form method="GET" class="flex items-center space-x-2 flex-1">
                            <div class="relative flex-1 max-w-md">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Cari event..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="menunggu" <?php echo $status_filter === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="disetujui" <?php echo $status_filter === 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                                <option value="ditolak" <?php echo $status_filter === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Filter
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['nama_lengkap']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['ekskul']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($event['total_pembiayaan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    switch ($event['status']) {
                                        case 'menunggu':
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'disetujui':
                                            $status_class = 'bg-green-100 text-green-800';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'bg-red-100 text-red-800';
                                            break;
                                        case 'closed':
                                            $status_class = 'bg-gray-100 text-gray-800';
                                            break;
                                    }
                                    ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $status_class; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($event['status'] === 'menunggu'): ?>
                                    <button onclick="approveEvent(<?php echo $event['event_id']; ?>, '<?php echo htmlspecialchars($event['judul_event']); ?>')" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <!-- View Event Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Event</h3>
                <div id="eventDetails" class="space-y-4">
                    <!-- Event details will be populated here -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeViewModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Approval Modal -->
    <div id="approvalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Persetujuan Event</h3>
                <form method="POST" id="approvalForm">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="event_id" id="approval_event_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                        <p id="approval_event_title" class="text-sm text-gray-900 bg-gray-50 p-2 rounded"></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan</label>
                        <select name="status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih keputusan</option>
                            <option value="disetujui">Setujui</option>
                            <option value="ditolak">Tolak</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                        <textarea name="catatan" rows="3" 
                                  placeholder="Berikan catatan atau alasan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeApprovalModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function viewEvent(event) {
            const details = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Judul Event</label>
                        <p class="text-sm text-gray-900">${event.judul_event}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Jenis Kegiatan</label>
                        <p class="text-sm text-gray-900">${event.jenis_kegiatan}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Pengaju</label>
                        <p class="text-sm text-gray-900">${event.nama_lengkap}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                        <p class="text-sm text-gray-900">${event.ekskul}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Total Pembiayaan</label>
                        <p class="text-sm text-gray-900">${event.total_pembiayaan}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                        <p class="text-sm text-gray-900">${new Date(event.tanggal_pengajuan).toLocaleDateString('id-ID')}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <p class="text-sm text-gray-900 mt-1">${event.deskripsi}</p>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">File Proposal</label>
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-file-pdf mr-1"></i>${event.proposal}
                    </p>
                </div>
            `;
            
            document.getElementById('eventDetails').innerHTML = details;
            document.getElementById('viewModal').classList.remove('hidden');
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }
        
        function approveEvent(eventId, eventTitle) {
            document.getElementById('approval_event_id').value = eventId;
            document.getElementById('approval_event_title').textContent = eventTitle;
            document.getElementById('approvalModal').classList.remove('hidden');
        }
        
        function closeApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
        }
    </script>
</body>
</html>