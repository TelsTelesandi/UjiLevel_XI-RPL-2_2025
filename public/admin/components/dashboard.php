<?php
// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php?action=login");
    exit;
}

require_once(__DIR__ . '/../../../config/database.php');

// Statistik
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_events = $pdo->query("SELECT COUNT(*) FROM event_pengajuan")->fetchColumn();
$pending_events = $pdo->query("SELECT COUNT(*) FROM event_pengajuan WHERE status = 'menunggu' OR status = 'Pending'")->fetchColumn();
$approved_events = $pdo->query("SELECT COUNT(*) FROM event_pengajuan WHERE status = 'approved' OR status = 'disetujui'")->fetchColumn();

// Ambil data event terbaru tanpa join
$recent_events = $pdo->query("
    SELECT * FROM event_pengajuan 
    ORDER BY tanggal_pengajuan DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil statistik bulanan dengan query yang direvisi
$monthly_stats = $pdo->query("
    SELECT 
        DATE_FORMAT(tanggal_pengajuan, '%Y-%m') as yearmonth,
        DATE_FORMAT(tanggal_pengajuan, '%M') as month,
        COUNT(*) as total 
    FROM event_pengajuan 
    WHERE tanggal_pengajuan >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY yearmonth, month
    ORDER BY yearmonth DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
        .dashboard-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
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
        
        /* Modal Styles */
        .modal {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .modal-content {
            animation: slideIn 0.3s ease-out;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-10%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .gradient-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
        }

        .form-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white p-6">
            <div class="flex items-center mb-8">
                <i class="fas fa-shield-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-semibold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-4">
                <a href="index.php?action=admin_dashboard" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_users" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-users w-6"></i>
                    <span>Kelola User</span>
                </a>
                <a href="index.php?action=admin_events" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-calendar w-6"></i>
                    <span>Kelola Event</span>
                </a>
                <a href="index.php?action=admin_verifications" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-check-circle w-6"></i>
                    <span>Verifikasi Event</span>
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
                    <span class="text-white">Welcome, <?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
                    <a href="index.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <div class="content-area rounded-xl p-6">
                    <h2 class="text-2xl text-white font-semibold mb-6">Dashboard Overview</h2>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-4 gap-6 mb-8">
                        <div class="dashboard-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Total Users</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $total_users; ?></h3>
                                </div>
                                <div class="text-3xl text-blue-400">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Total Events</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $total_events; ?></h3>
                                </div>
                                <div class="text-3xl text-green-400">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Pending Events</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $pending_events; ?></h3>
                                </div>
                                <div class="text-3xl text-yellow-400">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-300">Approved Events</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo $approved_events; ?></h3>
                                </div>
                                <div class="text-3xl text-purple-400">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="chart-container rounded-xl p-6">
                            <h3 class="text-xl font-semibold text-white mb-4">Event Statistics</h3>
                            <canvas id="eventChart" height="200"></canvas>
                        </div>
                        <div class="chart-container rounded-xl p-6">
                            <h3 class="text-xl font-semibold text-white mb-4">Monthly Trend</h3>
                            <canvas id="trendChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Recent Events Table -->
                    <div class="bg-white bg-opacity-10 rounded-xl p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-white">Recent Events</h3>
                            <a href="index.php?action=admin_events" class="text-blue-400 hover:text-blue-300">
                                View All <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-white">
                                <thead class="bg-indigo-900">
                                    <tr class="text-left">
                                        <th class="px-6 py-3">Event Name</th>
                                        <th class="px-6 py-3">Jenis Kegiatan</th>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_events as $event): ?>
                                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                        <td class="px-6 py-4"><?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs 
                                                <?php echo $event['status'] === 'menunggu' ? 'bg-yellow-500' : 
                                                    ($event['status'] === 'disetujui' ? 'bg-green-500' : 'bg-red-500'); ?>">
                                                <?php echo ucfirst($event['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <button onclick="showEventModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" 
                                                        class="text-blue-400 hover:text-blue-300">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="showEditModal(<?php echo htmlspecialchars(json_encode($event)); ?>)"
                                                        class="text-yellow-400 hover:text-yellow-300">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
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

    <!-- Modal View Event -->
    <div id="viewEventModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="modal-content w-full max-w-2xl mx-4 rounded-xl overflow-hidden">
            <div class="gradient-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-white">Detail Event</h3>
                    <button onclick="closeViewModal()" class="text-white hover:text-gray-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4 text-white">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Judul Event</label>
                            <p id="viewJudulEvent" class="mt-1 text-lg"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Jenis Kegiatan</label>
                            <p id="viewJenisKegiatan" class="mt-1 text-lg"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Total Pembiayaan</label>
                        <p id="viewTotalPembiayaan" class="mt-1 text-lg"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Deskripsi</label>
                        <p id="viewDeskripsi" class="mt-1 text-lg"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Status</label>
                            <p id="viewStatus" class="mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Tanggal Pengajuan</label>
                            <p id="viewTanggalPengajuan" class="mt-1 text-lg"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Proposal</label>
                        <p id="viewProposal" class="mt-1">
                            <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors" target="_blank">
                                <i class="fas fa-file-pdf mr-2"></i><span></span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Event -->
    <div id="editEventModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="modal-content w-full max-w-2xl mx-4 rounded-xl overflow-hidden">
            <div class="gradient-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-white">Edit Event</h3>
                    <button onclick="closeEditModal()" class="text-white hover:text-gray-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editEventForm" class="space-y-4">
                    <input type="hidden" id="editEventId" name="event_id">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Judul Event</label>
                            <input type="text" id="editJudulEvent" name="judul_event" 
                                   class="form-input mt-1 block w-full rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Jenis Kegiatan</label>
                            <input type="text" id="editJenisKegiatan" name="jenis_kegiatan" 
                                   class="form-input mt-1 block w-full rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Total Pembiayaan</label>
                        <input type="text" id="editTotalPembiayaan" name="total_pembiayaan" 
                               class="form-input mt-1 block w-full rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Deskripsi</label>
                        <textarea id="editDeskripsi" name="deskripsi" rows="3" 
                                  class="form-input mt-1 block w-full rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">File Proposal</label>
                        <input type="file" id="editFileProposal" name="file_proposal" 
                               class="form-input mt-1 block w-full" accept=".pdf">
                        <p class="mt-1 text-sm text-gray-400">Current file: <span id="currentProposal"></span></p>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()" 
                                class="btn-secondary px-4 py-2 rounded-lg text-white">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white">
                            Save Changes
                        </button>
                    </div>
                </form>
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

        // View Event Modal
        function showEventModal(event) {
            document.getElementById('viewJudulEvent').textContent = event.judul_event;
            document.getElementById('viewJenisKegiatan').textContent = event.jenis_kegiatan;
            document.getElementById('viewTotalPembiayaan').textContent = formatCurrency(event.total_pembiayaan);
            document.getElementById('viewDeskripsi').textContent = event.deskripsi;
            
            // Status dengan styling
            const statusElement = document.getElementById('viewStatus');
            let statusClass = '';
            switch(event.status.toLowerCase()) {
                case 'menunggu':
                case 'pending':
                    statusClass = 'bg-yellow-500 bg-opacity-20 text-yellow-500';
                    break;
                case 'disetujui':
                case 'approved':
                    statusClass = 'bg-green-500 bg-opacity-20 text-green-500';
                    break;
                case 'ditolak':
                case 'rejected':
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
            
            // Set proposal link dengan icon
            const proposalLink = document.querySelector('#viewProposal a');
            proposalLink.href = `/public/uploads/${event.proposal}`;
            proposalLink.querySelector('span').textContent = event.proposal;

            document.getElementById('viewEventModal').style.display = 'flex';
        }

        function closeViewModal() {
            document.getElementById('viewEventModal').style.display = 'none';
        }

        // Edit Event Modal
        function showEditModal(event) {
            document.getElementById('editEventId').value = event.event_id;
            document.getElementById('editJudulEvent').value = event.judul_event;
            document.getElementById('editJenisKegiatan').value = event.jenis_kegiatan;
            document.getElementById('editTotalPembiayaan').value = event.total_pembiayaan;
            document.getElementById('editDeskripsi').value = event.deskripsi;
            document.getElementById('currentProposal').textContent = event.proposal;

            document.getElementById('editEventModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editEventModal').style.display = 'none';
        }

        // Handle form submission
        document.getElementById('editEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('index.php?action=update_event', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Event berhasil diupdate!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert(result.message || 'Terjadi kesalahan saat mengupdate event');
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            const viewModal = document.getElementById('viewEventModal');
            const editModal = document.getElementById('editEventModal');
            
            if (event.target === viewModal) {
                closeViewModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Event Statistics Chart
        const eventCtx = document.getElementById('eventChart').getContext('2d');
        new Chart(eventCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Other'],
                datasets: [{
                    data: [<?php echo $pending_events; ?>, <?php echo $approved_events; ?>, 
                          <?php echo $total_events - ($pending_events + $approved_events); ?>],
                    backgroundColor: ['#EAB308', '#22C55E', '#6366F1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff'
                        }
                    }
                }
            }
        });

        // Monthly Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_stats, 'month')); ?>,
                datasets: [{
                    label: 'Events',
                    data: <?php echo json_encode(array_column($monthly_stats, 'total')); ?>,
                    borderColor: '#60A5FA',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 