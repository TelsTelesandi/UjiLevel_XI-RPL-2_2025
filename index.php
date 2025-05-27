<?php
// Include database connection file
require_once 'db_connect.php';

// Query to fetch events - REMOVED
// $sql = "SELECT event_id, judul_event FROM event_pengajuan ORDER BY event_id DESC";
// $result = mysqli_query($link, $sql);

// $events = [];
// if ($result && mysqli_num_rows($result) > 0) {
//     while($row = mysqli_fetch_assoc($result)) {
//         $events[] = $row;
//     }
// }

// Close connection - REMOVED
// mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Eskul</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .logo-effect {
            /* Adding a subtle shadow for a lifted effect */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s ease-in-out;
        }
        .logo-effect:hover {
             box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-white to-black text-gray-800 pb-8">

    <header class="w-full bg-gradient-to-r from-white to-blue-600 text-gray-800 shadow-md fixed top-0 left-0 z-10">
        <div class="container mx-auto flex items-center justify-center py-4">
            <img src="telk-removebg-preview.png" alt="Logo Telesandi" class="h-10 mr-3">
            <h1 class="text-2xl font-bold">Event Eskul Website</h1>
        </div>
    </header>

    <main class="container mx-auto pt-20 p-8 bg-gray-100 rounded-lg shadow-xl text-center max-w-md w-full">
        <!-- Add logo above the welcome message -->
        <img src="telk-removebg-preview.png" alt="Logo" class="mx-auto h-24 mb-6 logo-effect">

        <h2 class="text-2xl font-semibold mb-4 text-blue-600">Selamat Datang!</h2>
        <p class="text-gray-600 mb-8">Temukan dan ajukan kegiatan ekstrakurikuler di sini.</p>
        
        <div class="flex flex-col space-y-4 mb-8">
            <button onclick="window.location.href='login_user.php'" style="width:100%;background:#28a745;color:#fff;padding:14px 0;border:none;border-radius:5px;font-size:1.1em;margin-bottom:16px;cursor:pointer;">Login User</button>
            <button onclick="window.location.href='register.php'" style="width:100%;background:#007bff;color:#fff;padding:14px 0;border:none;border-radius:5px;font-size:1.1em;margin-bottom:16px;cursor:pointer;">Register User</button>
            <button onclick="window.location.href='login.php'" style="width:100%;background:#dc3545;color:#fff;padding:14px 0;border:none;border-radius:5px;font-size:1.1em;">Login Admin</button>
        </div>

        <!-- Removed Daftar Event section -->
        <!--
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Daftar Event</h3>

        <div class="flex flex-col space-y-4">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="bg-gray-100 p-4 rounded-md text-left shadow-sm">
                        <h4 class="text-lg font-semibold text-blue-700"><?php echo htmlspecialchars($event['judul_event']); ?></h4>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada event yang tersedia saat ini.</p>
            <?php endif; ?>
        </div>
        -->

    </main>

    <footer class="mt-8 text-gray-300 text-sm">
         <p>&copy; <?php echo date('Y'); ?> Event Eskul. All rights reserved.</p>
     </footer>

</body>
</html> 