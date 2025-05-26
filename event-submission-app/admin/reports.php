<?php
// Tambahkan link Font Awesome dan Tailwind CSS di sini
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle close request
if (isset($_POST['close_request'])) {
    $event_id = $_POST['event_id'];
    
    // Update status event menjadi closed
    $update_query = "UPDATE event_pengajuan SET status = 'closed' WHERE event_id = ?";
    $update_stmt = $db->prepare($update_query);
    
    if ($update_stmt->execute([$event_id])) {
        $success = 'Permintaan berhasil ditutup!';
    } else {
        $error = 'Gagal menutup permintaan!';
    }
}

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$status_filter = $_GET['status'] ?? 'all';

// Query conditions
$where_conditions = ["e.tanggal_pengajuan BETWEEN ? AND ?"];
$params = [$start_date, $end_date];

if ($status_filter != 'all') {
    $where_conditions[] = "e.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get events data
$events_query = "SELECT e.*, u.nama_lengkap, u.eskul, v.catatan_admin, v.tanggal_verifikasi, v.status as verification_status 
                 FROM event_pengajuan e
                 JOIN users u ON e.user_id = u.user_id
                 LEFT JOIN verifikasi_event v ON e.event_id = v.event_id
                 WHERE $where_clause
                 ORDER BY e.created_at DESC";
$events_stmt = $db->prepare($events_query);
$events_stmt->execute($params);
$events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT 
                    COUNT(*) as total_events,
                    SUM(CASE WHEN e.status = 'menunggu' THEN 1 ELSE 0 END) as pending_events,
                    SUM(CASE WHEN e.status = 'disetujui' THEN 1 ELSE 0 END) as approved_events,
                    SUM(CASE WHEN e.status = 'ditolak' THEN 1 ELSE 0 END) as rejected_events,
                    SUM(CASE WHEN v.status = 'closed' THEN 1 ELSE 0 END) as closed_events
                FROM event_pengajuan e
                LEFT JOIN verifikasi_event v ON e.event_id = v.event_id
                WHERE $where_clause";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute($params);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$page_title = 'Laporan Pengajuan Event';
include '../includes/header.php';
?>

<nav class="bg-white border-b border-blue-600 shadow-md px-6 py-8 flex items-center justify-between">
    <div class="flex items-center space-x-2">
        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        <span class="text-lg font-semibold text-gray-800">Event Submission System</span>
    </div>
    <div class="flex items-center space-x-6 text-sm text-gray-700">
        <a href="../dashboard.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="users.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-users mr-1"></i> Users
        </a>
        <a href="approvals.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-check-circle mr-1"></i> Approvals
        </a>
        <a href="reports.php" class="flex items-center text-blue-600 font-semibold">
            <i class="fas fa-file-alt mr-1"></i> Reports
        </a>
        <span class="flex items-center text-gray-700">
            <i class="fas fa-user mr-1"></i> Administrator
        </span>
        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center transition-colors">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-chart-bar mr-2"></i> Laporan Pengajuan Event</h1>
        <p class="text-gray-600 mt-1">Laporan berdasarkan periode dan status pengajuan</p>
    </div>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $success; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-lg shadow mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            <input type="date" name="start_date" value="<?= $start_date ?>"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring focus:border-blue-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
            <input type="date" name="end_date" value="<?= $end_date ?>"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring focus:border-blue-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring focus:border-blue-300">
                <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>Semua</option>
                <option value="menunggu" <?= $status_filter == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="disetujui" <?= $status_filter == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                <option value="ditolak" <?= $status_filter == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <?php
        $stat_cards = [
            ['label' => 'Total', 'count' => $stats['total_events'], 'color' => 'blue', 'icon' => 'calendar'],
            ['label' => 'Menunggu', 'count' => $stats['pending_events'], 'color' => 'yellow', 'icon' => 'clock'],
            ['label' => 'Disetujui', 'count' => $stats['approved_events'], 'color' => 'green', 'icon' => 'check-circle'],
            ['label' => 'Ditolak', 'count' => $stats['rejected_events'], 'color' => 'red', 'icon' => 'times-circle'],
            ['label' => 'Closed', 'count' => $stats['closed_events'], 'color' => 'gray', 'icon' => 'archive'],
        ];
        foreach ($stat_cards as $card):
        ?>
        <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
            <div class="p-3 rounded-full bg-<?= $card['color'] ?>-100 text-<?= $card['color'] ?>-600">
                <i class="fas fa-<?= $card['icon'] ?> text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600"><?= $card['label'] ?></p>
                <p class="text-xl font-bold text-gray-800"><?= $card['count'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-lg shadow">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-900">Tabel Laporan Event</h2>
            <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-download mr-2"></i>Export CSV
            </button>
        </div>
        <div class="p-6 overflow-x-auto">
            <?php if (empty($events)): ?>
                <p class="text-gray-500 text-center py-6">Tidak ada data untuk periode ini.</p>
            <?php else: ?>
                <table id="eventsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Event</th>
                            <th class="px-4 py-3 text-left">Pengaju</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Pembiayaan</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Tgl Pengajuan</th>
                            <th class="px-4 py-3 text-left">Tgl Verifikasi</th>
                            <th class="px-4 py-3 text-left">Verifikasi</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <?php foreach ($events as $e): ?>
                        <tr>
                            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($e['judul_event']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['nama_lengkap']) ?><br><span class="text-xs text-gray-500"><?= htmlspecialchars($e['eskul']) ?></span></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['jenis_kegiatan']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['total_pembiayaan']) ?></td>
                            <td class="px-4 py-2">
                                <?php
                                $colorMap = [
                                    'menunggu' => 'yellow',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red'
                                ];
                                $color = $colorMap[$e['status']] ?? 'gray';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                    <?= ucfirst($e['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2"><?= date('d/m/Y', strtotime($e['tanggal_pengajuan'])) ?></td>
                            <td class="px-4 py-2"><?= $e['tanggal_verifikasi'] ? date('d/m/Y H:i', strtotime($e['tanggal_verifikasi'])) : '-' ?></td>
                            <td class="px-4 py-2">
                                <?php if ($e['verification_status']): ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $e['verification_status'] == 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?= ucfirst($e['verification_status']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php if ($e['status'] != 'closed'): ?>
                                <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menutup permintaan ini?')">
                                    <input type="hidden" name="event_id" value="<?= $e['event_id'] ?>">
                                    <button type="submit" name="close_request" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i> Close Request
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Export Script -->
<script>
function exportToCSV() {
    const table = document.getElementById('eventsTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = Array.from(cols).map(col => `"${col.innerText.replace(/"/g, '""')}"`);
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', 'laporan_event_<?php echo date("Ymd"); ?>.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php include '../includes/footer.php'; ?>


