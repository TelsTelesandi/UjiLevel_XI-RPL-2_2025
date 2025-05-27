<?php
// Periksa apakah sesi sudah aktif sebelum memulai sesi baru
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center">
            <button id="sidebarToggle" class="lg:hidden mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="relative">
                <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span class="hidden md:block"><?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?></span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                
                <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i>Pengaturan
                    </a>
                    <hr class="my-1">
                    <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.getElementById('profileDropdown').addEventListener('click', function() {
    document.getElementById('profileMenu').classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const menu = document.getElementById('profileMenu');
    
    if (!dropdown.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script>