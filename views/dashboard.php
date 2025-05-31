<?php
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// Cek role, jika admin redirect ke admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php?action=admin_dashboard");
    exit();
}

// Get user's events and stats
global $db;
require_once 'app/controllers/EventController.php';
$eventController = new EventController($db);
$userStats = $eventController->getUserDashboardStats($_SESSION['user_id']);

if ($_GET['action'] === 'submit_event') {
    require_once 'controllers/EventController.php';
    $controller = new EventController($db);
    $controller->submitEvent();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar {
            background-color: #1e1b4b;
        }
        .content-area {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .stat-card {
            background-color: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
        }
        .quick-link {
            background-color: rgba(255,255,255,0.10);
            backdrop-filter: blur(2px);
        }
    </style>
</head>
<body>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar w-64 text-white p-6">
        <div class="flex items-center mb-8">
            <i class="fas fa-user-circle text-2xl mr-3"></i>
            <h1 class="text-xl font-semibold">User Panel</h1>
        </div>
        <nav class="space-y-4">
            <a href="index.php?action=dashboard" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php?action=request_event" class="flex items-center text-gray-300 hover:text-white">
                <i class="fas fa-calendar-plus w-6"></i>
                <span>Request Event</span>
            </a>
            <a href="index.php?action=my_events" class="flex items-center text-gray-300 hover:text-white">
                <i class="fas fa-calendar-check w-6"></i>
                <span>My Events</span>
            </a>
        </nav>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Bar -->
        <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
            <span class="text-white font-semibold text-lg">Welcome, <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span>
            <a href="index.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
        <!-- Content Area -->
        <div class="p-6">
            <div class="content-area rounded-xl p-6">
                <h2 class="text-2xl text-white font-semibold mb-6">Dashboard Overview</h2>
                <!-- Statistics Cards -->
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-300">Total Pengajuan</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $userStats['stats']['total_pengajuan']; ?></h3>
                            </div>
                            <div class="text-3xl text-blue-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-300">Sedang Proses</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $userStats['stats']['menunggu']; ?></h3>
                            </div>
                            <div class="text-3xl text-yellow-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-300">Selesai</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $userStats['stats']['closed']; ?></h3>
                            </div>
                            <div class="text-3xl text-green-400">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Links -->
                <div class="grid grid-cols-3 gap-6 mt-6">
                    <a href="index.php?action=request_event" class="quick-link bg-blue-600 bg-opacity-20 rounded-xl p-6 text-white hover:bg-opacity-30">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-plus text-3xl mr-4"></i>
                            <div>
                                <h3 class="font-semibold">Request Event</h3>
                                <p class="text-sm text-gray-300">Ajukan event baru</p>
                            </div>
                        </div>
                    </a>
                    <a href="index.php?action=my_events" class="quick-link bg-green-600 bg-opacity-20 rounded-xl p-6 text-white hover:bg-opacity-30">
                        <div class="flex items-center">
                            <i class="fas fa-file-upload text-3xl mr-4"></i>
                            <div>
                                <h3 class="font-semibold">Upload Proposal</h3>
                                <p class="text-sm text-gray-300">Upload proposal event</p>
                            </div>
                        </div>
                    </a>
                    <a href="index.php?action=my_events" class="quick-link bg-purple-600 bg-opacity-20 rounded-xl p-6 text-white hover:bg-opacity-30">
                        <div class="flex items-center">
                            <i class="fas fa-list-alt text-3xl mr-4"></i>
                            <div>
                                <h3 class="font-semibold">Rekap Status</h3>
                                <p class="text-sm text-gray-300">Lihat status pengajuan</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html> 