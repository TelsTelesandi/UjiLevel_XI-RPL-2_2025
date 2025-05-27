<aside id="sidebar" class="w-64 bg-blue-800 min-h-screen fixed lg:relative lg:translate-x-0 transform -translate-x-full transition-transform duration-200 ease-in-out z-30">
    <div class="p-6">
        <h2 class="text-white text-lg font-semibold">Portal Ekskul</h2>
        <p class="text-blue-200 text-sm"><?php echo $_SESSION['ekskul']; ?></p>
    </div>
    
    <nav class="mt-6">
        <div class="px-6 py-3">
            <p class="text-blue-300 text-xs uppercase tracking-wider">Menu Utama</p>
        </div>
        
        <a href="dashboard.php" class="flex items-center px-6 py-3 text-blue-200 hover:bg-blue-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Dashboard
        </a>
        
        <a href="events.php" class="flex items-center px-6 py-3 text-blue-200 hover:bg-blue-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-calendar mr-3"></i>
            Event Saya
        </a>
        
        <a href="history.php" class="flex items-center px-6 py-3 text-blue-200 hover:bg-blue-700 hover:text-white transition-colors duration-200">
            <i class="fas fa-history mr-3"></i>
            Riwayat
        </a>
        
        <div class="px-6 py-3 mt-6">
            <p class="text-blue-300 text-xs uppercase tracking-wider">Akun</p>
        </div>
        
        <a href="../logout.php" class="flex items-center px-6 py-3 text-blue-200 hover:bg-blue-700 hover:text-white transition-colors duration-200">
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