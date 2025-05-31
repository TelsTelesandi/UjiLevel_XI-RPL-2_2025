<?php
// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// Ambil daftar event user
$userEvents = $queries->getUserEvents($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Saya - Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
            min-height: 100vh;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-900 min-h-screen p-4">
            <div class="flex items-center space-x-4 mb-6">
                <i class="fas fa-user text-white text-2xl"></i>
                <h1 class="text-white text-xl font-bold">User Panel</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php?action=dashboard" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=submit_event" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Event</span>
                </a>
                <a href="index.php?action=my_events" 
                   class="flex items-center space-x-2 text-white bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Event Saya</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Event Saya</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-white">
                        <span class="mr-2">Welcome,</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <a href="index.php?action=logout" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="p-8">
                <!-- Events Table -->
                <div class="dashboard-card rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-white">Daftar Event</h3>
                        <a href="index.php?action=submit_event" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Ajukan Event Baru
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-white">
                            <thead>
                                <tr class="text-left border-b border-gray-700">
                                    <th class="pb-3">Judul Event</th>
                                    <th class="pb-3">Jenis</th>
                                    <th class="pb-3">Total Biaya</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Tanggal</th>
                                    <th class="pb-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($userEvents)): ?>
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-400">
                                        Belum ada event yang diajukan
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($userEvents as $event): ?>
                                    <tr class="border-b border-gray-800">
                                        <td class="py-3">
                                            <div class="font-medium"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-gray-300"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-gray-300">Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></div>
                                        </td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 rounded-full text-xs 
                                                <?php 
                                                echo match($event['status']) {
                                                    'menunggu' => 'bg-yellow-500',
                                                    'disetujui' => 'bg-green-500',
                                                    'ditolak' => 'bg-red-500',
                                                    'selesai' => 'bg-gray-500',
                                                    default => 'bg-gray-500'
                                                };
                                                ?>">
                                                <?php echo htmlspecialchars($event['status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3"><?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                        <td class="py-3">
                                            <a href="index.php?action=view_event&id=<?php echo $event['event_id']; ?>" 
                                               class="text-blue-400 hover:text-blue-300 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($event['status'] === 'disetujui'): ?>
                                            <button onclick="closeEvent(<?php echo $event['event_id']; ?>)"
                                                    class="text-green-400 hover:text-green-300">
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
            </div>
        </div>
    </div>

    <script>
    function closeEvent(eventId) {
        if (confirm('Apakah Anda yakin ingin menyelesaikan event ini? Event yang sudah selesai tidak dapat diubah kembali.')) {
            fetch('index.php?action=close_event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + eventId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event berhasil diselesaikan');
                    location.reload();
                } else {
                    alert('Gagal menyelesaikan event: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyelesaikan event');
            });
        }
    }
    </script>
</body>
</html> 