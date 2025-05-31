<?php
// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event - Event Management System</title>
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
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            z-index: 50;
            display: none;
        }
        .notification.success {
            background-color: #34D399;
            color: white;
        }
        .notification.error {
            background-color: #F87171;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Notification -->
    <div id="notification" class="notification">
        <span id="notificationMessage"></span>
    </div>

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
                   class="flex items-center space-x-2 text-white bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Event</span>
                </a>
                <a href="index.php?action=my_events" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Event Saya</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Ajukan Event</h2>
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
                <div class="dashboard-card rounded-xl p-6">
                    <form id="submitEventForm" action="index.php?action=submit_event_process" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Judul Event</label>
                                <input type="text" name="judul_event" required
                                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Jenis Kegiatan</label>
                                <select name="jenis_kegiatan" required
                                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="Lomba">Lomba</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Pelatihan">Pelatihan</option>
                                    <option value="Sosial">Sosial</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Total Pembiayaan</label>
                                <input type="number" name="total_pembiayaan" required min="0"
                                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">File Proposal (PDF)</label>
                                <input type="file" name="file_proposal" required accept=".pdf"
                                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-white file:bg-blue-600 hover:file:bg-blue-700">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-white mb-2">Deskripsi Event</label>
                                <textarea name="deskripsi" rows="4" required
                                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="index.php?action=dashboard" 
                               class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Submit Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notificationMessage');
        
        notification.className = 'notification ' + type;
        notificationMessage.textContent = message;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    document.getElementById('submitEventForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('index.php?action=submit_event_process', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Event berhasil diajukan!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php?action=my_events';
                }, 2000);
            } else {
                showNotification(result.message || 'Gagal mengajukan event', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat mengirim data', 'error');
        }
    });
    </script>
</body>
</html> 