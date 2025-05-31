<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Event Management System' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .animated-gradient {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="animated-gradient min-h-screen text-white">
    <div class="min-h-screen flex transition-all duration-300">
        <!-- Sidebar -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="w-64 bg-indigo-900 min-h-screen p-4">
            <div class="flex items-center space-x-4 mb-6">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
                <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php?action=admin_dashboard" 
                   class="flex items-center space-x-2 <?php echo strpos($current_page, 'admin_dashboard') !== false ? 'text-white bg-indigo-800' : 'text-gray-300 hover:bg-indigo-800'; ?> p-2 rounded-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_verifications" 
                   class="flex items-center space-x-2 <?php echo strpos($current_page, 'admin_verifications') !== false ? 'text-white bg-indigo-800' : 'text-gray-300 hover:bg-indigo-800'; ?> p-2 rounded-lg">
                    <i class="fas fa-check-circle"></i>
                    <span>Verifikasi Event</span>
                </a>
                <a href="index.php?action=manage_users" 
                   class="flex items-center space-x-2 <?php echo strpos($current_page, 'manage_users') !== false ? 'text-white bg-indigo-800' : 'text-gray-300 hover:bg-indigo-800'; ?> p-2 rounded-lg">
                    <i class="fas fa-users"></i>
                    <span>Kelola Users</span>
                </a>
            </nav>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="flex-1 transition-all duration-300">
            <!-- Header -->
            <?php if (isset($_SESSION['user_id'])): ?>
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
            <?php endif; ?>

            <!-- Content Container -->
            <div class="p-6"> 