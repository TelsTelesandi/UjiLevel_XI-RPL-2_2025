<aside id="sidebar" class="w-64 bg-gray-800 min-h-screen fixed lg:relative lg:translate-x-0 transform -translate-x-full transition-transform duration-200 ease-in-out z-30">
    <div class="p-6">
        <h2 class="text-white text-lg font-semibold">Admin Panel</h2>
    </div>
    
    <nav class="mt-6">
        <div class="px-6 py-3">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Menu Utama</p>
        </div>
        
        <a href="dashboard.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Dashboard
        </a>
        
        <a href="users.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-users mr-3"></i>
            Manajemen User
        </a>
        
        <a href="events.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-calendar mr-3"></i>
            Manajemen Event
        </a>
        
        <a href="reports.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-chart-bar mr-3"></i>
            Laporan
        </a>
        
        <div class="px-6 py-3 mt-6">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Akun</p>
        </div>
        
        <a href="../logout.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-sign-out-alt mr-3"></i>
            Logout
        </a>
    </nav>
</aside>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
});
</script>