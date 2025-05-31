<?php
// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Event - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'bounce-slow': 'bounce 3s linear infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'scale': 'scale 2s ease-in-out infinite',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        scale: {
                            '0%, 100%': { transform: 'scale(1)' },
                            '50%': { transform: 'scale(1.05)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
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
        .verification-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .verification-card:hover {
            transform: translateY(-5px);
        }
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .table-row:hover {
            background-color: rgba(255, 255, 255, 0.05);
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
        .event-table th {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .event-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .event-table tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .status-pending, .status-menunggu {
            background-color: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        .status-approved, .status-disetujui {
            background-color: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        .status-rejected, .status-ditolak {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        .filter-button {
            @apply px-4 py-2 rounded-lg text-white transition-all;
        }
        .filter-button.active {
            @apply bg-indigo-600;
        }
        .filter-button:not(.active) {
            @apply bg-white bg-opacity-10 hover:bg-opacity-20;
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
                <a href="index.php?action=admin_dashboard" class="flex items-center text-gray-300 hover:text-white">
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
                <a href="index.php?action=admin_verifications" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
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
                    <span class="text-white">Welcome, <?php echo $_SESSION['username'] ?? 'admin@admin.com'; ?></span>
                    <a href="index.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <div class="content-area rounded-xl p-6">
                    <h2 class="text-2xl text-white font-semibold mb-6">Laporan Pengajuan Event</h2>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-4 gap-6 mb-8">
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <h3 class="text-gray-300 text-sm">Total Pengajuan</h3>
                            <p class="text-2xl text-white font-bold mt-2" id="totalEvents">-</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <h3 class="text-gray-300 text-sm">Pending</h3>
                            <p class="text-2xl text-yellow-400 font-bold mt-2" id="pendingEvents">-</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <h3 class="text-gray-300 text-sm">Approved</h3>
                            <p class="text-2xl text-green-400 font-bold mt-2" id="approvedEvents">-</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <h3 class="text-gray-300 text-sm">Rejected</h3>
                            <p class="text-2xl text-red-400 font-bold mt-2" id="rejectedEvents">-</p>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex space-x-5 mb-5 text-white">
                        <button class="filter-button active" data-filter="all">Semua</button>
                        <button class="filter-button" data-filter="approved">Approved</button>
                        <button class="filter-button" data-filter="rejected">Rejected</button>
                        <button class="filter-button" data-filter="pending">Pending</button>
                    </div>

                    <!-- Events Table -->
                    <div class="overflow-x-auto">
                        <table class="event-table min-w-full text-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ekskul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Pembiayaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Pengajuan</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTableBody">
                                <!-- Data will be loaded here by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Export & Print Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button onclick="exportToExcel()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                        <button onclick="printReport()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <button onclick="goBack()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let events = [];
    let currentFilter = 'all';

    document.addEventListener('DOMContentLoaded', () => {
        loadEvents();
        setupFilterButtons();
    });

    function setupFilterButtons() {
        document.querySelectorAll('.filter-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                // Add active class to clicked button
                button.classList.add('active');
                // Update filter and refresh table
                currentFilter = button.dataset.filter;
                renderEvents();
            });
        });
    }

    function loadEvents() {
        fetch('./index.php?action=get_events')
            .then(response => response.json())
            .then(data => {
                events = data;
                updateStatistics();
                renderEvents();
            })
            .catch(error => console.error('Error:', error));
    }

    function updateStatistics() {
        const stats = {
            total: events.length,
            pending: events.filter(e => e.status.toLowerCase() === 'menunggu').length,
            approved: events.filter(e => e.status.toLowerCase() === 'disetujui').length,
            rejected: events.filter(e => e.status.toLowerCase() === 'ditolak').length
        };

        document.getElementById('totalEvents').textContent = stats.total;
        document.getElementById('pendingEvents').textContent = stats.pending;
        document.getElementById('approvedEvents').textContent = stats.approved;
        document.getElementById('rejectedEvents').textContent = stats.rejected;
    }

    function renderEvents() {
        const tbody = document.getElementById('eventsTableBody');
        tbody.innerHTML = '';

        const filteredEvents = events.filter(event => {
            if (currentFilter === 'all') return true;
            const status = event.status.toLowerCase();
            return (currentFilter === 'pending' && status === 'menunggu') ||
                   (currentFilter === 'approved' && status === 'disetujui') ||
                   (currentFilter === 'rejected' && status === 'ditolak');
        });

        filteredEvents.forEach(event => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${event.event_id}</td>
                <td class="px-6 py-4 whitespace-nowrap">${event.judul_event}</td>
                <td class="px-6 py-4 whitespace-nowrap">${event.ekskul}</td>
                <td class="px-6 py-4 whitespace-nowrap">${event.jenis_kegiatan}</td>
                <td class="px-6 py-4 whitespace-nowrap">Rp ${formatNumber(event.total_pembiayaan)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="${getStatusClass(event.status)}">
                        ${capitalizeFirst(event.status)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">${formatDate(event.tanggal_pengajuan)}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'menunggu': return 'status-menunggu';
            case 'disetujui': return 'status-disetujui';
            case 'ditolak': return 'status-ditolak';
            default: return '';
        }
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    function exportToExcel() {
        // Implement Excel export functionality
        alert('Export to Excel functionality will be implemented here');
    }

    function printReport() {
        window.print();
    }

    function goBack() {
        window.history.back();
    }
    </script>
</body>
</html> 