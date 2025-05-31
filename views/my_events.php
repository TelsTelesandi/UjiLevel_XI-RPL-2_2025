<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

require_once 'app/config/database.php';
require_once 'app/controllers/EventController.php';

$database = new Database();
$db = $database->getConnection();
$eventController = new EventController($db);
$userEvents = $eventController->getUserDashboardStats($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar {
            background-color: #1e1b4b;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 16rem;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 16rem;
            min-height: 100vh;
        }
        .content-area {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .event-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        /* Styling scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1e1b4b;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4338ca;
            border-radius: 2px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white p-6">
            <div class="flex items-center mb-8">
                <i class="fas fa-user-circle text-2xl mr-3"></i>
                <h1 class="text-xl font-semibold">User Dashboard</h1>
            </div>
            
            <nav class="space-y-4">
                <a href="index.php?action=dashboard" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=request_event" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-calendar-plus w-6"></i>
                    <span>Request Event</span>
                </a>
                <a href="index.php?action=my_events" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>My Events</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <button class="text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span>
                    <a href="index.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <div class="content-area rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl text-white font-semibold">My Events</h2>
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                                <?php echo count($userEvents['recent_events']); ?> Events
                            </span>
                        </div>
                        <a href="index.php?action=request_event" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>Request New Event
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div class="event-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Total Events</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $userEvents['stats']['total_pengajuan']; ?></h3>
                                </div>
                                <div class="text-3xl text-blue-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>

                        <div class="event-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Pending</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $userEvents['stats']['menunggu']; ?></h3>
                                </div>
                                <div class="text-3xl text-yellow-400">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="event-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Approved</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $userEvents['stats']['closed']; ?></h3>
                                </div>
                                <div class="text-3xl text-green-400">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Table -->
                    <div class="bg-white/10 rounded-xl p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-white">
                                <thead class="bg-[#2D3250]">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Event Name</th>
                                        <th class="px-6 py-3 text-left">Jenis Kegiatan</th>
                                        <th class="px-6 py-3 text-left">Total Pembiayaan</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                        <th class="px-6 py-3 text-left">Tanggal</th>
                                        <th class="px-6 py-3 text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10">
                                    <?php foreach ($userEvents['recent_events'] as $event): ?>
                                    <tr class="hover:bg-white/5">
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                        <td class="px-6 py-4">Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs
                                                <?php 
                                                    echo match($event['status']) {
                                                        'menunggu' => 'bg-yellow-500',
                                                        'disetujui' => 'bg-green-500',
                                                        'ditolak' => 'bg-red-500',
                                                        default => 'bg-gray-500'
                                                    };
                                                ?>">
                                                <?php echo ucfirst($event['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick="viewEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)"
                                                    class="text-blue-400 hover:text-blue-300 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($event['status'] === 'menunggu'): ?>
                                            <button onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)"
                                                    class="text-yellow-400 hover:text-yellow-300">
                                                <i class="fas fa-edit"></i>
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
    </div>

    <!-- View Event Modal -->
    <div id="viewEventModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-[#2D3250] rounded-xl p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-white">Detail Event</h3>
                <button onclick="closeViewModal()" class="text-white/70 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4 text-white">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300">Judul Event</label>
                        <p id="viewJudulEvent" class="text-lg"></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300">Jenis Kegiatan</label>
                        <p id="viewJenisKegiatan" class="text-lg"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-300">Total Pembiayaan</label>
                    <p id="viewTotalPembiayaan" class="text-lg"></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-300">Deskripsi</label>
                    <p id="viewDeskripsi" class="text-lg"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300">Status</label>
                        <p id="viewStatus"></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300">Tanggal Pengajuan</label>
                        <p id="viewTanggalPengajuan" class="text-lg"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-300">Proposal</label>
                    <a id="viewProposal" href="#" class="text-blue-400 hover:text-blue-300" target="_blank">
                        <i class="fas fa-file-pdf mr-2"></i>
                        <span></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // View Event
        function viewEvent(event) {
            document.getElementById('viewJudulEvent').textContent = event.judul_event;
            document.getElementById('viewJenisKegiatan').textContent = event.jenis_kegiatan;
            document.getElementById('viewTotalPembiayaan').textContent = formatCurrency(event.total_pembiayaan);
            document.getElementById('viewDeskripsi').textContent = event.deskripsi;
            
            const statusElement = document.getElementById('viewStatus');
            let statusClass = '';
            switch(event.status.toLowerCase()) {
                case 'menunggu':
                    statusClass = 'bg-yellow-500 bg-opacity-20 text-yellow-500';
                    break;
                case 'disetujui':
                    statusClass = 'bg-green-500 bg-opacity-20 text-green-500';
                    break;
                case 'ditolak':
                    statusClass = 'bg-red-500 bg-opacity-20 text-red-500';
                    break;
            }
            statusElement.className = `inline-block px-3 py-1 rounded-full ${statusClass}`;
            statusElement.textContent = event.status;
            
            document.getElementById('viewTanggalPengajuan').textContent = 
                new Date(event.tanggal_pengajuan).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            
            const proposalLink = document.getElementById('viewProposal');
            proposalLink.href = `public/uploads/${event.proposal}`;
            proposalLink.querySelector('span').textContent = event.proposal;

            document.getElementById('viewEventModal').style.display = 'flex';
        }

        function closeViewModal() {
            document.getElementById('viewEventModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('viewEventModal');
            if (event.target === modal) {
                closeViewModal();
            }
        }
    </script>
</body>
</html>