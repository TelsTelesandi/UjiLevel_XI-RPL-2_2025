<?php if ($_SESSION['role'] === 'user'): ?>
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
            <a href="index.php?action=request_event" class="flex items-center text-gray-300 hover:text-white">
                <i class="fas fa-calendar-plus w-6"></i>
                <span>Request Event</span>
            </a>
            <a href="index.php?action=my_events" class="flex items-center text-gray-300 hover:text-white">
                <i class="fas fa-calendar-check w-6"></i>
                <span>My Events</span>
            </a>
        </nav>
    </div>
<?php endif; ?> 