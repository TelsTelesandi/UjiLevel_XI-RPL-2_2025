<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ekskul Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#F59E0B',
                        success: '#10B981',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <?php if (isset($_GET['success'])): ?>
    <div id="notif-success" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50">
      <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-down">
        <i class="fas fa-check-circle text-2xl"></i>
        <span class="font-semibold">Pengajuan event berhasil dikirim!</span>
        <button onclick="document.getElementById('notif-success').remove()" class="ml-4 text-white hover:text-green-200">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <script>
      setTimeout(() => {
        const notif = document.getElementById('notif-success');
        if (notif) notif.remove();
      }, 4000);
    </script>
    <style>
    @keyframes fade-in-down {
      0% { opacity: 0; transform: translateY(-20px) scale(0.95);}
      100% { opacity: 1; transform: translateY(0) scale(1);}
    }
    .animate-fade-in-down {
      animation: fade-in-down 0.5s;
    }
    </style>
    <?php endif; ?>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white hidden md:block">
            <div class="p-4 flex items-center space-x-2 border-b border-blue-700">
                <i class="fas fa-calendar-alt text-2xl"></i>
                <span class="text-xl font-bold">EventEkskul</span>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard_user.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="form_pengajuan.php" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-700">
                            <i class="fas fa-calendar-plus w-5"></i>
                            <span>Ajukan Event</span>
                        </a>
                    </li>
                </ul>
                <div class="mt-8 pt-4 border-t border-blue-700">
                    <a href="../controllers/logout.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Header -->
            <header class="bg-white shadow-sm md:hidden">
                <div class="flex justify-between items-center p-4">
                    <button id="sidebarToggle" class="text-gray-600">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">EventEkskul</h1>
                    <div class="w-8"></div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Form Header -->
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Ajukan Event Baru</h1>
                            <p class="text-gray-600">Isi formulir berikut untuk mengajukan kegiatan ekstrakurikuler</p>
                        </div>
                        <a href="dashboard_user.php" class="text-sm text-primary hover:underline flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>

                    <!-- Event Form -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <form class="p-6" action="../controllers/event_controller.php" method="POST" enctype="multipart/form-data">
                            <!-- Judul Event -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Event*</label>
                                <input type="text" name="judul_event" maxlength="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                            </div>

                            <!-- Jenis Kegiatan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan*</label>
                                <input type="text" name="jenis_kegiatan" maxlength="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                            </div>

                            <!-- Total Pembiayaan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Pembiayaan (Rp)*</label>
                                <input type="number" name="total_pembiayaan" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                            </div>

                            <!-- Tanggal Pengajuan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengajuan*</label>
                                <input type="date" name="tanggal_pengajuan" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kegiatan*</label>
                                <textarea name="deskripsi" rows="4" maxlength="500" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required></textarea>
                            </div>

                            <!-- Upload Proposal -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Proposal (PDF)*</label>
                                <input type="file" name="proposal" accept=".pdf" class="w-full" required>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 pt-4 border-t">
                                <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset Form</button>
                                <button type="submit" name="ajukan_event" class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Ajukan Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle mobile sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.w-64').classList.toggle('hidden');
        });

        // File upload handling
        const dropzone = document.querySelector('#uploadContent').parentElement;
        const fileInput = document.getElementById('proposal');
        const uploadContent = document.getElementById('uploadContent');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressPercent = document.getElementById('progressPercent');
        const fileName = document.getElementById('fileName');

        dropzone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect(e) {
            const file = e.target.files[0];
            
            // Validate file
            if (file.type !== 'application/pdf') {
                alert('Hanya file PDF yang diperbolehkan');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                return;
            }
            
            // Show file name
            fileName.textContent = `File terpilih: ${file.name}`;
            fileName.classList.remove('hidden');
            
            // Show upload progress (simulated)
            uploadContent.classList.add('hidden');
            uploadProgress.classList.remove('hidden');
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                
                progressBar.style.width = `${progress}%`;
                progressPercent.textContent = Math.round(progress);
            }, 200);
        }

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropzone.classList.add('border-primary');
            dropzone.classList.remove('border-gray-300');
        }

        function unhighlight() {
            dropzone.classList.remove('border-primary');
            dropzone.classList.add('border-gray-300');
        }

        dropzone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                handleFileSelect({ target: fileInput });
            }
        }

        // Reset form
        document.getElementById('resetForm').addEventListener('click', function() {
            document.querySelector('form').reset();
            uploadContent.classList.remove('hidden');
            uploadProgress.classList.add('hidden');
            fileName.classList.add('hidden');
        });
    </script>
</body>
</html>