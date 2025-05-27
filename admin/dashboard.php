<?php
// Tentukan jalur absolut ke root direktori aplikasi
$root_path = dirname(__DIR__);
require_once $root_path . '/config/database.php';
requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [];

// Total users
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total events
$query = "SELECT COUNT(*) as total FROM event_pengajuan";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Pending events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'menunggu'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['pending_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Approved events
$query = "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'disetujui'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['approved_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent events
$query = "SELECT ep.*, u.nama_lengkap, u.ekskul 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          ORDER BY ep.created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Pengajuan Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_users']; ?></p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Event</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_events']; ?></p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-calendar text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Event Menunggu</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_events']; ?></p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Event Disetujui</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['approved_events']; ?></p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-check-circle text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Events -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Event Terbaru</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($recent_events as $event): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($event['nama_lengkap']); ?> - <?php echo htmlspecialchars($event['ekskul']); ?></p>
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
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>