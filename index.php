<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Event Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-rose-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-rose-600 mb-6">
                Sistem Manajemen Event Ekstrakurikuler
            </h1>
            <p class="text-lg text-purple-700 mb-12">
                Platform modern untuk mengelola dan mengajukan event ekstrakurikuler sekolah
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="login.php" class="inline-flex items-center justify-center px-8 py-3 text-base font-medium text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk
                </a>
                <a href="register.php" class="inline-flex items-center justify-center px-8 py-3 text-base font-medium text-rose-600 bg-white border-2 border-rose-200 hover:border-rose-300 rounded-lg transition duration-300 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Daftar
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid md:grid-cols-3 gap-8 mt-20">
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-rose-600 mb-2">Pengajuan Event</h3>
                <p class="text-purple-600">Ajukan event ekstrakurikuler dengan mudah dan terstruktur</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-rose-600 mb-2">Verifikasi Cepat</h3>
                <p class="text-purple-600">Proses verifikasi yang cepat dan transparan</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-rose-600 mb-2">Laporan Lengkap</h3>
                <p class="text-purple-600">Pantau dan kelola semua event dalam satu dashboard</p>
            </div>
        </div>
    </div>
</body>
</html>
