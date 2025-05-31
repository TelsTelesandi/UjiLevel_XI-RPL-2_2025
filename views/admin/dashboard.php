<?php
// Cek autentikasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=login");
    exit();
}

// Ambil data dashboard dari DatabaseQueries
require_once __DIR__ . '/../../app/models/DatabaseQueries.php';
$queries = new DatabaseQueries($db);
$stats = $queries->getDashboardStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event Management System</title>
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
                <i class="fas fa-shield-alt text-white text-2xl"></i>
                <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php?action=admin_dashboard" 
                   class="flex items-center space-x-2 text-white bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_verifications" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
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
                <h2 class="text-2xl font-bold text-white">Dashboard Overview</h2>
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
                <!-- Statistics Cards -->
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="dashboard-card p-6 rounded-xl text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-300">Total Events</p>
                                <h3 class="text-3xl font-bold"><?php echo $stats['total_events']; ?></h3>
                            </div>
                            <div class="text-4xl text-blue-400">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card p-6 rounded-xl text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-300">Pending Events</p>
                                <h3 class="text-3xl font-bold"><?php echo $stats['pending_events']; ?></h3>
                            </div>
                            <div class="text-4xl text-yellow-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card p-6 rounded-xl text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-300">Approved Events</p>
                                <h3 class="text-3xl font-bold"><?php echo $stats['approved_events']; ?></h3>
                            </div>
                            <div class="text-4xl text-green-400">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card p-6 rounded-xl text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-300">Rejected Events</p>
                                <h3 class="text-3xl font-bold"><?php echo $stats['rejected_events']; ?></h3>
                            </div>
                            <div class="text-4xl text-red-400">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events Table -->
                <div class="dashboard-card rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-white">Recent Events</h3>
                        <a href="index.php?action=admin_verifications" class="text-blue-400 hover:text-blue-300">
                            View All <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
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
                            <tbody id="eventsTableBody">
                                <!-- Data akan diisi melalui AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Fungsi untuk memuat data events
    function loadEvents() {
        fetch('index.php?action=get_verifications')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('eventsTableBody');
                tableBody.innerHTML = '';

                data.forEach(event => {
                    const row = document.createElement('tr');
                    row.className = 'border-b border-gray-800';
                    row.innerHTML = `
                        <td class="py-3">
                            <div class="font-medium">${event.judul_event}</div>
                            <div class="text-sm text-gray-400">${event.jenis_kegiatan}</div>
                        </td>
                        <td class="py-3">
                            <div class="font-medium">${event.nama_lengkap}</div>
                            <div class="text-sm text-gray-400">${event.ekskul}</div>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded-full text-xs 
                                ${event.status === 'pending' ? 'bg-yellow-500' : 
                                  event.status === 'approved' ? 'bg-green-500' : 'bg-red-500'}">
                                ${event.status}
                            </span>
                        </td>
                        <td class="py-3">${new Date(event.created_at).toLocaleDateString()}</td>
                        <td class="py-3">
                            <button onclick="verifyEvent(${event.event_id}, 'approved')" 
                                    class="text-green-400 hover:text-green-300 mr-3">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="verifyEvent(${event.event_id}, 'rejected')"
                                    class="text-red-400 hover:text-red-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Fungsi untuk verifikasi event
    function verifyEvent(eventId, status) {
        const keterangan = status === 'rejected' ? prompt('Masukkan alasan penolakan:') : '';
        
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
                loadEvents(); // Reload data setelah verifikasi
            } else {
                alert('Gagal memverifikasi event');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Load events saat halaman dimuat
    document.addEventListener('DOMContentLoaded', loadEvents);
    </script>
</body>
</html> 