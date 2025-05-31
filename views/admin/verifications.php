<?php
// Cek autentikasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=login");
    exit();
}

// Ambil data verifikasi dari DatabaseQueries
require_once __DIR__ . '/../../app/models/DatabaseQueries.php';
$queries = new DatabaseQueries($db);
$pendingEvents = $queries->getPendingEvents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Verifications - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
            min-height: 100vh;
        }
        .content-card {
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
                <i class="fas fa-shield-alt text-white text-2xl"></i>
                <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php?action=admin_dashboard" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_verifications" 
                   class="flex items-center space-x-2 text-white bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-check-circle"></i>
                    <span>Verifikasi Event</span>
                </a>
                <a href="index.php?action=manage_users" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-users"></i>
                    <span>Kelola Users</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Verifikasi Event</h2>
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
                <div class="content-card rounded-xl p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-white">
                            <thead>
                                <tr class="text-left border-b border-gray-700">
                                    <th class="pb-3">Event</th>
                                    <th class="pb-3">User</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingEvents as $event): ?>
                                <tr class="border-b border-gray-800">
                                    <td class="py-3">
                                        <div class="font-medium"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-medium"><?php echo htmlspecialchars($event['nama_lengkap']); ?></div>
                                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($event['ekskul']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            <?php 
                                            switch($event['status']) {
                                                case 'menunggu':
                                                    echo 'bg-yellow-500';
                                                    break;
                                                case 'disetujui':
                                                    echo 'bg-green-500';
                                                    break;
                                                case 'ditolak':
                                                    echo 'bg-red-500';
                                                    break;
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($event['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($event['status'] === 'menunggu'): ?>
                                        <button onclick="verifyEvent(<?php echo $event['event_id']; ?>, 'disetujui')" 
                                                class="text-green-400 hover:text-green-300 mr-3">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="verifyEvent(<?php echo $event['event_id']; ?>, 'ditolak')"
                                                class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function verifyEvent(eventId, status) {
        const keterangan = status === 'ditolak' ? prompt('Masukkan alasan penolakan:') : '';
        
        fetch('index.php?action=verify_event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `event_id=${eventId}&status=${status}&keterangan=${keterangan || ''}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload halaman setelah verifikasi
            } else {
                alert('Gagal memverifikasi event');
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html> 