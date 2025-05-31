<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'bounce-in': 'bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)', opacity: '0.8' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
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
        }
        .content-area {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
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
        .gradient-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
        }
        .event-card {
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px) scale(1.02);
        }
        .chart-container {
            transition: all 0.3s ease;
        }
        .chart-container:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-gradient {
            background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #3b82f6);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        .search-box {
            transition: all 0.3s ease;
        }
        .search-box:focus {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
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
                <a href="index.php?action=admin_events" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
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
        <div class="flex-1">
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
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl text-white font-semibold">Daftar Pengajuan Event</h2>
                        </div>
                        <div class="flex space-x-4">
                            <div class="relative">
                                <input type="text" placeholder="Search events..." 
                                       class="bg-white bg-opacity-20 text-white placeholder-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute right-3 top-3 text-gray-300"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Events Table -->
                    <div class="overflow-x-auto">
                        <table class="event-table min-w-full text-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Pembiayaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTableBody">
                                <!-- Data will be loaded here by JavaScript -->
                            </tbody>
                        </table>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Review Event</h3>
                <div id="eventDetails" class="space-y-4">
                    <!-- Event details will be loaded here -->
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button onclick="closeReviewModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Close
                    </button>
                    <button onclick="pendingEvent()" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                        Pending
                    </button>
                    <button onclick="approveEvent()" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                        Approve
                    </button>
                    <button onclick="rejectEvent()" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let events = [];

        document.addEventListener('DOMContentLoaded', loadEvents);

    function loadEvents() {
        fetch('./index.php?action=get_events')
            .then(response => response.json())
            .then(data => {
                events = data;
                renderEvents();
            })
            .catch(error => console.error('Error:', error));
    }

    function renderEvents() {
        const tbody = document.getElementById('eventsTableBody');
        tbody.innerHTML = '';

        events.forEach(event => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${event.event_id}</td>
                <td class="px-6 py-4 whitespace-nowrap">${event.judul_event}</td>
                <td class="px-6 py-4 whitespace-nowrap">${event.jenis_kegiatan}</td>
                <td class="px-6 py-4 whitespace-nowrap">Rp ${formatNumber(event.total_pembiayaan)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="${getStatusClass(event.status)}">
                        ${capitalizeFirst(event.status)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">${formatDate(event.tanggal_pengajuan)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="showReviewModal(${event.event_id})" 
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                        Review
                    </button>
                </td>
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

    function showReviewModal(eventId) {
        const event = events.find(e => e.event_id === eventId);
        if (!event) return;

        // Store the current event ID for approve/reject actions
        window.currentEventId = eventId;

        document.getElementById('eventDetails').innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Judul Event</p>
                    <p class="text-gray-900">${event.judul_event}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Jenis Kegiatan</p>
                    <p class="text-gray-900">${event.jenis_kegiatan}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pembiayaan</p>
                    <p class="text-gray-900">Rp ${formatNumber(event.total_pembiayaan)}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                    <p class="text-gray-900">${event.deskripsi || '-'}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-500">Proposal</p>
                    <a href="./index.php?action=view_proposals" class="text-blue-600 hover:underline">
                        Lihat Proposal
                    </a>
                </div>
                <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-500">Status Saat Ini</p>
                    <p class="text-gray-900">${capitalizeFirst(event.status)}</p>
                </div>
            </div>
        `;
            
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        window.currentEventId = null;
    }

    function approveEvent() {
        if (!window.currentEventId) return;
        
        fetch('./index.php?action=update_event_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: window.currentEventId,
                status: 'approved'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the event in the local array
                const eventIndex = events.findIndex(e => e.event_id === window.currentEventId);
                if (eventIndex !== -1) {
                    events[eventIndex].status = 'disetujui';
                    renderEvents(); // Re-render the table
                }
                
                // Show success message
                showAlert('Event berhasil disetujui!', 'success');
                closeReviewModal();
            } else {
                showAlert('Gagal menyetujui event: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memproses permintaan', 'error');
        });
    }

    function rejectEvent() {
        if (!window.currentEventId) return;
        
        fetch('./index.php?action=update_event_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: window.currentEventId,
                status: 'rejected'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the event in the local array
                const eventIndex = events.findIndex(e => e.event_id === window.currentEventId);
                if (eventIndex !== -1) {
                    events[eventIndex].status = 'ditolak';
                    renderEvents(); // Re-render the table
                }
                
                // Show success message
                showAlert('Event telah ditolak', 'success');
                closeReviewModal();
            } else {
                showAlert('Gagal menolak event: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memproses permintaan', 'error');
        });
    }

    function pendingEvent() {
        if (!window.currentEventId) return;
        
        fetch('./index.php?action=update_event_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: window.currentEventId,
                status: 'pending'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the event in the local array
                const eventIndex = events.findIndex(e => e.event_id === window.currentEventId);
                if (eventIndex !== -1) {
                    events[eventIndex].status = 'menunggu';
                    renderEvents(); // Re-render the table
                }
                
                // Show success message
                showAlert('Status event diubah menjadi menunggu', 'success');
                closeReviewModal();
            } else {
                showAlert('Gagal mengubah status: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memproses permintaan', 'error');
        });
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } animate-fade-in`;
        
        alertDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Remove after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    </script>
</body>
</html> 