<?php
require_once '../config/database.php';
requireUser();

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $user_id = $_SESSION['user_id'];
                $judul_event = $_POST['judul_event'];
                $jenis_kegiatan = $_POST['jenis_kegiatan'];
                $total_pembiayaan = $_POST['total_pembiayaan'];
                $deskripsi = $_POST['deskripsi'];
                $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
                
                // Handle file upload
                $proposal_name = '';
                if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === 0) {
                    $upload_dir = '../uploads/proposals/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION);
                    $proposal_name = 'proposal_' . time() . '_' . $user_id . '.' . $file_extension;
                    $upload_path = $upload_dir . $proposal_name;
                    
                    if (move_uploaded_file($_FILES['proposal']['tmp_name'], $upload_path)) {
                        // File uploaded successfully
                    } else {
                        $error = 'Gagal mengupload file proposal';
                        break;
                    }
                } else {
                    $proposal_name = 'proposal_default.pdf';
                }
                
                $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_name, $deskripsi, $tanggal_pengajuan])) {
                    $message = 'Event berhasil diajukan';
                } else {
                    $error = 'Gagal mengajukan event';
                }
                break;
                
            case 'close':
                $event_id = $_POST['event_id'];
                $user_id = $_SESSION['user_id'];
                
                $query = "UPDATE event_pengajuan SET status = 'closed' WHERE event_id = ? AND user_id = ? AND status = 'disetujui'";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$event_id, $user_id])) {
                    $message = 'Event berhasil ditutup';
                } else {
                    $error = 'Gagal menutup event';
                }
                break;
        }
    }
}

// Get user events
$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM event_pengajuan WHERE user_id = ? AND (judul_event LIKE ? OR jenis_kegiatan LIKE ?) ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$search_param = "%$search%";
$stmt->execute([$user_id, $search_param, $search_param]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Saya - User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Event Saya</h1>
                <p class="text-gray-600">Kelola pengajuan event ekstrakurikuler Anda</p>
            </div>
            
            <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Daftar Event</h2>
                        <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Ajukan Event Baru
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <form method="GET" class="flex items-center space-x-2 flex-1">
                            <div class="relative flex-1 max-w-md">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Cari event..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kegiatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada event yang diajukan
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($event['jenis_kegiatan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?>
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
                                    <?php if ($event['status'] === 'disetujui'): ?>
                                    <button onclick="closeEvent(<?php echo $event['event_id']; ?>)" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add Event Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ajukan Event Baru</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Event</label>
                            <input type="text" name="judul_event" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan</label>
                            <input type="text" name="jenis_kegiatan" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Pembiayaan</label>
                            <input type="text" name="total_pembiayaan" placeholder="Rp 0" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengajuan</label>
                            <input type="date" name="tanggal_pengajuan" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Proposal</label>
                        <input type="file" name="proposal" accept=".pdf,.doc,.docx" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Event</label>
                        <textarea name="deskripsi" rows="4" required 
                                  placeholder="Jelaskan detail event yang akan diselenggarakan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAddModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Ajukan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
    
    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }
        
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
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <p class="text-sm text-gray-900 mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(event.status)}">
                            ${event.status.charAt(0).toUpperCase() + event.status.slice(1)}
                        </span>
                    </p>
                </div>
            `;
            
            document.getElementById('eventDetails').innerHTML = details;
            document.getElementById('viewModal').classList.remove('hidden');
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }
        
        function closeEvent(eventId) {
            if (confirm('Apakah Anda yakin ingin menutup event ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="close">
                    <input type="hidden" name="event_id" value="${eventId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function getStatusClass(status) {
            switch (status) {
                case 'menunggu':
                    return 'bg-yellow-100 text-yellow-800';
                case 'disetujui':
                    return 'bg-green-100 text-green-800';
                case 'ditolak':
                    return 'bg-red-100 text-red-800';
                case 'closed':
                    return 'bg-gray-100 text-gray-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }
    </script>
</body>
</html>