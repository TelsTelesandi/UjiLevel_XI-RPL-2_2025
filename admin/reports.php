<?php
require_once '../config/database.php';
requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    
    $query = "SELECT ep.*, u.nama_lengkap, u.ekskul, ve.catatan_admin, ve.tanggal_verifikasi 
              FROM event_pengajuan ep 
              JOIN users u ON ep.user_id = u.user_id 
              LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id";
    
    $params = [];
    if ($status_filter) {
        $query .= " WHERE ep.status = ?";
        $params[] = $status_filter;
    }
    
    $query .= " ORDER BY ep.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_event_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, [
        'Judul Event',
        'Pengaju',
        'Ekstrakurikuler',
        'Jenis Kegiatan',
        'Total Pembiayaan',
        'Tanggal Pengajuan',
        'Status',
        'Catatan Admin',
        'Tanggal Verifikasi'
    ]);
    
    // CSV data
    foreach ($data as $row) {
        fputcsv($output, [
            $row['judul_event'],
            $row['nama_lengkap'],
            $row['ekskul'],
            $row['jenis_kegiatan'],
            $row['total_pembiayaan'],
            $row['tanggal_pengajuan'],
            $row['status'],
            $row['catatan_admin'] ?? '',
            $row['tanggal_verifikasi'] ?? ''
        ]);
    }
    
    fclose($output);
    exit();
}

// Get statistics
$stats = [];

// Total events
$query = "SELECT COUNT(*) as total FROM event_pengajuan";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Approved events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'disetujui'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['approved_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Closed events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'closed'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['closed_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Rejected events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'ditolak'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['rejected_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get report data
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT ep.*, u.nama_lengkap, u.ekskul, ve.catatan_admin, ve.tanggal_verifikasi 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id";

$params = [];
if ($status_filter) {
    $query .= " WHERE ep.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY ep.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Laporan</h1>
                        <p class="text-gray-600">Laporan pengajuan dan status event ekstrakurikuler</p>
                    </div>
                    <a href="?export=csv<?php echo $status_filter ? '&status=' . $status_filter : ''; ?>" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pengajuan</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_events']; ?></p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Event Disetujui</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['approved_events']; ?></p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Event Selesai</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['closed_events']; ?></p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-flag-checkered text-purple-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Event Ditolak</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['rejected_events']; ?></p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Data Laporan</h2>
                        <form method="GET" class="flex items-center space-x-2">
                            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="menunggu" <?php echo $status_filter === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="disetujui" <?php echo $status_filter === 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                                <option value="ditolak" <?php echo $status_filter === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($report_data)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data untuk ditampilkan
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($report_data as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['judul_event']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['jenis_kegiatan']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['nama_lengkap']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['ekskul']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d M Y', strtotime($item['tanggal_pengajuan'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($item['total_pembiayaan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    switch ($item['status']) {
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
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php if ($item['tanggal_verifikasi']): ?>
                                        <div class="text-xs text-gray-500">
                                            <?php echo date('d M Y H:i', strtotime($item['tanggal_verifikasi'])); ?>
                                        </div>
                                        <?php if ($item['catatan_admin']): ?>
                                        <div class="text-xs text-gray-600 mt-1 max-w-xs truncate" title="<?php echo htmlspecialchars($item['catatan_admin']); ?>">
                                            <?php echo htmlspecialchars($item['catatan_admin']); ?>
                                        </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
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
</body>
</html>