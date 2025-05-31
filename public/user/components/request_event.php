<?php
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Event - User Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="min-h-screen animated-gradient text-white">
    <div class="min-h-screen flex transition-all duration-300">
        <!-- Sidebar -->
        <div class="bg-gray-900 bg-opacity-80 backdrop-blur-lg w-64 py-4 flex-shrink-0 animate-slide-up shadow-2xl">
            <div class="px-4 py-4 border-b border-gray-700">
                <h2 class="text-xl font-semibold flex items-center space-x-2">
                    <i class="fas fa-user-circle animate-bounce-slow"></i>
                    <span>User Panel</span>
                </h2>
            </div>
            <nav class="mt-4">
                <ul class="space-y-2">
                    <li class="px-4 py-2 hover:bg-white hover:bg-opacity-10 transition-all duration-200 rounded-lg mx-2">
                        <a href="./index.php?action=user_dashboard" class="flex items-center space-x-2 hover:translate-x-1 transition-transform">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="px-4 py-2 bg-white bg-opacity-20 shadow-lg rounded-lg mx-2 transform hover:scale-105 transition-all duration-200">
                        <a href="./index.php?action=request_event" class="flex items-center space-x-2">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Request Event</span>
                        </a>
                    </li>
                    <li class="px-4 py-2 hover:bg-white hover:bg-opacity-10 transition-all duration-200 rounded-lg mx-2">
                        <a href="./index.php?action=my_events" class="flex items-center space-x-2 hover:translate-x-1 transition-transform">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 transition-all duration-300">
            <!-- Header -->
            <header class="bg-white bg-opacity-10 backdrop-blur-lg shadow-lg animate-slide-up">
                <div class="flex justify-between items-center px-6 py-4">
                    <button id="sidebar-toggle" class="text-white hover:text-gray-200 transform hover:scale-110 transition-all">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <span class="text-white">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="./index.php?action=logout" 
                           class="bg-red-500 bg-opacity-90 backdrop-blur-lg text-white px-4 py-2 rounded-lg hover:bg-red-600 transform hover:scale-105 transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Request Event Form -->
            <div class="p-6">
                <div class="gradient-card rounded-xl p-6 animate-slide-up max-w-2xl mx-auto">
                    <h2 class="text-2xl font-semibold mb-6">Request Event</h2>
                    
                    <form id="requestEventForm" class="space-y-6" enctype="multipart/form-data">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Judul Event</label>
                            <input type="text" name="judul_event" required
                                   class="w-full px-3 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-20 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-opacity-50 transition-all text-white">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Jenis Kegiatan</label>
                            <select name="jenis_kegiatan" required
                                    class="w-full px-3 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-20 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-opacity-50 transition-all text-white">
                                <option value="">Pilih Jenis Kegiatan</option>
                                <option value="lomba">Lomba</option>
                                <option value="seminar">Seminar</option>
                                <option value="workshop">Workshop</option>
                                <option value="pelatihan">Pelatihan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Total Pembiayaan</label>
                            <input type="number" name="total_pembiayaan" required
                                   class="w-full px-3 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-20 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-opacity-50 transition-all text-white"
                                   placeholder="Masukkan jumlah dalam Rupiah">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" rows="4" required
                                      class="w-full px-3 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-20 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-opacity-50 transition-all text-white"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Upload Proposal (PDF)</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" name="proposal" accept=".pdf" required
                                       class="w-full px-3 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-20 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-opacity-50 transition-all text-white">
                            </div>
                            <p class="text-xs text-gray-300">Max file size: 5MB</p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="window.history.back()" 
                                    class="px-4 py-2 bg-gray-500 bg-opacity-90 text-white rounded-lg hover:bg-gray-600 transform hover:scale-105 transition-all duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 bg-opacity-90 text-white rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-200">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission handler
        document.getElementById('requestEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalContent = submitButton.innerHTML;
            
            try {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        <span>Submitting...</span>
                    </div>
                `;
                
                const formData = new FormData(this);
                
                const response = await fetch('./index.php?action=submit_event_request', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Event request submitted successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = './index.php?action=my_events';
                    }, 2000);
                } else {
                    showAlert(result.message || 'Failed to submit request', 'error');
                }
            } catch (error) {
                showAlert('Error submitting request', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalContent;
            }
        });

        // Utility function for showing alerts
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            alertDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                alertDiv.classList.add('translate-x-full');
                setTimeout(() => {
                    alertDiv.remove();
                }, 300);
            }, 3000);
        }

        // Toggle sidebar
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.flex-1');
            
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('ml-0');
            
            if (sidebar.classList.contains('hidden')) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '16rem';
            }
        });
    </script>
</body>
</html> 