<?php
   require_once(__DIR__ . '/../config/session.php');

?>
<nav class="bg-white shadow-lg border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <i class="fas fa-calendar-alt text-primary text-2xl"></i>
                <h1 class="text-xl font-bold text-gray-800">Event Submission System</h1>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="flex items-center space-x-6">
                <a href="dashboard.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                
                <?php if (getUserRole() === 'user'): ?>
                <a href="submit_event.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-plus mr-2"></i>Submit Event
                </a>
                <a href="my_events.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-list mr-2"></i>My Events
                </a>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                <a href="admin/users.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="admin/approvals.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-check-circle mr-2"></i>Approvals
                </a>
                <a href="admin/reports.php" class="text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
                <?php endif; ?>
                
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600">
                        <i class="fas fa-user mr-1"></i><?php echo getUserName(); ?>
                    </span>
                    <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
