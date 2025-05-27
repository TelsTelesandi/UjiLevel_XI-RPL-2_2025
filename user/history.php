<?php
require_once '../config/database.php';
requireUser();

$database = new Database();
$db = $database->getConnection();

// Get user event history with verification details
$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT ep.*, ve.catatan_admin, ve.tanggal_verifikasi 
          FROM event_pengajuan ep 
          LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id 
          WHERE ep.user_id = ? AND (ep.judul_event LIKE ? OR ep.jenis_kegiatan LIKE ?) 
          ORDER BY ep.created_at DESC";

$stmt = $db->prepare($query);
$search_param = "%$search%";
$stmt->execute([$user_id, $search_param, $search_param]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Event - User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Event</h1>
                <p class="text-gray-600">Riwayat pengajuan event ekstrakurikuler Anda</p>
            </div>
            
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan</h2>
                        <span class="text-sm text-gray-500">Total: <?php echo count($history); ?> event</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <form method="GET" class="flex items-center space-x-2 flex-1">
                            <div class="relative flex-1 max-w-md">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Cari riwayat event..." 
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Admin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada riwayat event
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($history as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['judul_event']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['total_pembiayaan']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($item['jenis_kegiatan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d M Y', strtotime($item['tanggal_pengajuan'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    $status_icon = '';
                                    switch ($item['status']) {
                                        case 'menunggu':
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                            $status_icon = 'fas fa-clock';
                                            break;
                                        case 'disetujui':
                                            $status_class = 'bg-green-100 text-green-800';
                                            $status_icon = 'fas fa-check-circle';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'bg-red-100 text-red-800';
                                            $status_icon = 'fas fa-times-circle';
                                            break;
                                        case 'closed':
                                            $status_class = 'bg-gray-100 text-gray-800';
                                            $status_icon = 'fas fa-flag-checkered';
                                            break;
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full <?php echo $status_class; ?>">
                                        <i class="<?php echo $status_icon; ?> mr-1"></i>
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        <?php if ($item['catatan_admin']): ?>
                                            <p class="truncate" title="<?php echo htmlspecialchars($item['catatan_admin']); ?>">
                                                <?php echo htmlspecialchars($item['catatan_admin']); ?>
                                            </p>
                                            <?php if ($item['tanggal_verifikasi']): ?>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <?php echo date('d M Y H:i', strtotime($item['tanggal_verifikasi'])); ?>
                                            </p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewEventHistory(<?php echo htmlspecialchars(json_encode($item)); ?>)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
    
    <!-- View History Modal -->
    <div id="historyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Riwayat Event</h3>
                <div id="historyDetails" class="space-y-4">
                    <!-- History details will be populated here -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeHistoryModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function viewEventHistory(item) {
            const statusInfo = getStatusInfo(item.status);
            
            const details = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Judul Event</label>
                        <p class="text-sm text-gray-900">${item.judul_event}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Jenis Kegiatan</label>
                        <p class="text-sm text-gray-900">${item.jenis_kegiatan}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Total Pembiayaan</label>
                        <p class="text-sm text-gray-900">${item.total_pembiayaan}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                        <p class="text-sm text-gray-900">${new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID')}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <p class="text-sm text-gray-900 mt-1">${item.deskripsi}</p>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">File Proposal</label>
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-file-pdf mr-1"></i>${item.proposal}
                    </p>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <p class="text-sm text-gray-900 mt-1">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${statusInfo.class}">
                            <i class="${statusInfo.icon} mr-1"></i>
                            ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                        </span>
                    </p>
                </div>
                ${item.catatan_admin ? `
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-700">Catatan Admin</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-900">${item.catatan_admin}</p>
                        ${item.tanggal_verifikasi ? `
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            ${new Date(item.tanggal_verifikasi).toLocaleString('id-ID')}
                        </p>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
            `;
            
            document.getElementById('historyDetails').innerHTML = details;
            document.getElementById('historyModal').classList.remove('hidden');
        }
        
        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }
        
        function getStatusInfo(status) {
            switch (status) {
                case 'menunggu':
                    return {
                        class: 'bg-yellow-100 text-yellow-800',
                        icon: 'fas fa-clock'
                    };
                case 'disetujui':
                    return {
                        class: 'bg-green-100 text-green-800',
                        icon: 'fas fa-check-circle'
                    };
                case 'ditolak':
                    return {
                        class: 'bg-red-100 text-red-800',
                        icon: 'fas fa-times-circle'
                    };
                case 'closed':
                    return {
                        class: 'bg-gray-100 text-gray-800',
                        icon: 'fas fa-flag-checkered'
                    };
                default:
                    return {
                        class: 'bg-gray-100 text-gray-800',
                        icon: 'fas fa-question-circle'
                    };
            }
        }
    </script>
</body>
</html>