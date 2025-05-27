<?php
require_once '../config/database.php';
requireUser();

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$user_id = $_SESSION['user_id'];

// Total events by user
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$total_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Pending events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = ? AND status = 'menunggu'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$pending_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Approved events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = ? AND status = 'disetujui'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$approved_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Closed events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = ? AND status = 'closed'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$closed_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent events
$query = "SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Pengajuan Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?> - <?php echo $_SESSION['ekskul']; ?></p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Event</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $total_events; ?></p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-calendar text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Menunggu</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $pending_events; ?></p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disetujui</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $approved_events; ?></p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Selesai</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $closed_events; ?></p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-flag-checkered text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Events -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Event Terbaru</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php if (empty($recent_events)): ?>
                            <p class="text-gray-500 text-center py-4">Belum ada event yang diajukan</p>
                            <?php else: ?>
                            <?php foreach ($recent_events as $event): ?>
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?></p>
                                </div>
                                <div>
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
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="events.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Event →
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Guidelines -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Panduan Pengajuan</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0">
                                    1
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Siapkan proposal lengkap</p>
                                    <p class="text-xs text-gray-600">Pastikan proposal berisi tujuan, jadwal, dan anggaran yang detail</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0">
                                    2
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Ajukan minimal 2 minggu sebelumnya</p>
                                    <p class="text-xs text-gray-600">Berikan waktu yang cukup untuk proses persetujuan</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0">
                                    3
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pantau status pengajuan</p>
                                    <p class="text-xs text-gray-600">Cek secara berkala status persetujuan event Anda</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0">
                                    4
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Tutup event setelah selesai</p>
                                    <p class="text-xs text-gray-600">Jangan lupa mengubah status menjadi "closed" setelah event selesai</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="events.php" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 text-center block">
                                <i class="fas fa-plus mr-2"></i>Ajukan Event Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>