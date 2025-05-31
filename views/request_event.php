<?php
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
    <title>Request Event - User Dashboard</title>
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
        .form-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
                <a href="index.php?action=request_event" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
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
                        <h2 class="text-2xl text-white font-semibold">Request Event Baru</h2>
                    </div>

                    <!-- Request Event Form -->
                    <div class="form-card rounded-xl p-6">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="bg-green-500 bg-opacity-20 text-green-500 px-4 py-2 rounded-lg mb-4">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="bg-red-500 bg-opacity-20 text-red-500 px-4 py-2 rounded-lg mb-4">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="index.php?action=submit_event" method="POST" enctype="multipart/form-data" class="space-y-6" id="eventForm">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <input type="hidden" name="status" value="menunggu">
                            <input type="hidden" name="tanggal_pengajuan" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            
                            <div>
                                <label class="block text-white mb-2">Judul Event</label>
                                <input type="text" name="judul_event" required
                                       class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                              placeholder-white/50 focus:outline-none focus:border-blue-500
                                              transition-all duration-200"
                                       placeholder="Masukkan judul event">
                            </div>

                            <div>
                                <label class="block text-white mb-2">Jenis Kegiatan</label>
                                <select name="jenis_kegiatan" required
                                        class="w-full px-4 py-2.5 rounded-lg bg-[#2D3250] border border-white/20 text-white 
                                               focus:outline-none focus:border-blue-500 transition-all duration-200">
                                    <option value="" class="bg-[#2D3250]">Pilih Jenis Kegiatan</option>
                                    <option value="Lomba" class="bg-[#2D3250]">Lomba</option>
                                    <option value="Workshop" class="bg-[#2D3250]">Workshop</option>
                                    <option value="Seminar" class="bg-[#2D3250]">Seminar</option>
                                    <option value="Pelatihan" class="bg-[#2D3250]">Pelatihan</option>
                                    <option value="Lainnya" class="bg-[#2D3250]">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-white mb-2">Total Pembiayaan</label>
                                <input type="number" name="total_pembiayaan" required
                                       class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                              placeholder-white/50 focus:outline-none focus:border-blue-500
                                              transition-all duration-200"
                                       placeholder="Masukkan jumlah dalam Rupiah">
                            </div>

                            <div>
                                <label class="block text-white mb-2">Deskripsi</label>
                                <textarea name="deskripsi" rows="4" required
                                          class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                                 placeholder-white/50 focus:outline-none focus:border-blue-500
                                                 transition-all duration-200"
                                          placeholder="Jelaskan detail event"></textarea>
                            </div>

                            <div>
                                <label class="block text-white mb-2">Upload Proposal (PDF)</label>
                                <input type="file" name="file_proposal" accept=".pdf" required
                                       class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                              file:text-white file:bg-blue-500 file:hover:bg-blue-600
                                              transition-all duration-200">
                                <p class="mt-1 text-sm text-white/70">Maksimal ukuran file: 5MB</p>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="window.history.back()" 
                                        class="px-6 py-2.5 bg-gray-600/50 hover:bg-gray-600 text-white rounded-lg
                                               transition-colors duration-200">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg
                                               transition-colors duration-200">
                                    Submit Event
                                </button>
                            </div>
                        </form>
                    </div>

                    <script>
                        document.getElementById('eventForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const formData = new FormData(this);
                            
                            fetch('index.php?action=submit_event', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Event berhasil diajukan!');
                                    window.location.href = 'index.php?action=my_events';
                                } else {
                                    alert(data.message || 'Terjadi kesalahan saat mengajukan event');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan saat mengirim data');
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 