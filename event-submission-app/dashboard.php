<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get statistics
if (isAdmin()) {
    // Admin dashboard stats
    $total_users_query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
    $total_events_query = "SELECT COUNT(*) as count FROM event_pengajuan";
    $pending_events_query = "SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'menunggu'";
    $approved_events_query = "SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'disetujui'";
    $closed_events_query = "SELECT COUNT(*) as count FROM verifikasi_event WHERE status = 'closed'";
} else {
    // User dashboard stats
    $user_id = getUserId();
    $total_events_query = "SELECT COUNT(*) as count FROM event_pengajuan WHERE user_id = $user_id";
    $pending_events_query = "SELECT COUNT(*) as count FROM event_pengajuan WHERE user_id = $user_id AND status = 'menunggu'";
    $approved_events_query = "SELECT COUNT(*) as count FROM event_pengajuan WHERE user_id = $user_id AND status = 'disetujui'";
    $closed_events_query = "SELECT COUNT(*) as count FROM verifikasi_event v JOIN event_pengajuan e ON v.event_id = e.event_id WHERE e.user_id = $user_id AND v.status = 'closed'";
}

// Fetch statistics
$total_users = isAdmin() ? $db->query($total_users_query)->fetch()['count'] : 0;
$total_events = $db->query($total_events_query)->fetch()['count'];
$pending_events = $db->query($pending_events_query)->fetch()['count'];
$approved_events = $db->query($approved_events_query)->fetch()['count'];
$closed_events = $db->query($closed_events_query)->fetch()['count'];

// Get recent events
if (isAdmin()) {
    $recent_events_query = "SELECT e.*, u.nama_lengkap, u.eskul FROM event_pengajuan e 
                           JOIN users u ON e.user_id = u.user_id 
                           ORDER BY e.created_at DESC LIMIT 5";
} else {
    $user_id = getUserId();
    $recent_events_query = "SELECT e.*, u.nama_lengkap, u.eskul FROM event_pengajuan e 
                           JOIN users u ON e.user_id = u.user_id 
                           WHERE e.user_id = $user_id 
                           ORDER BY e.created_at DESC LIMIT 5";
}

$recent_events = $db->query($recent_events_query)->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Dashboard - Event Submission System';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="bg-blue-500 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white">
                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
            </h1>
            <p class="text-gray-200 mt-2">
                Selamat datang, <?php echo getUserName(); ?>! 
                <?php echo isAdmin() ? '(Administrator)' : '(User)'; ?>
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php if (isAdmin()): ?>
            <div class="bg-white rounded-lg shadow-md p-6 border border-blue-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-red-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Events</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_events; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-yellow-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $pending_events; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-green-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $approved_events; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                        <i class="fas fa-archive text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Closed</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $closed_events; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-blue-600">
                    <i class="fas fa-history mr-2"></i>Recent Events
                </h2>
            </div>
            <div class="p-6">
                <?php if (empty($recent_events)): ?>
                <p class="text-gray-500 text-center py-8">Belum ada event yang diajukan.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <?php if (isAdmin()): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                                <?php endif; ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_events as $event): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                </td>
                                <?php if (isAdmin()): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($event['nama_lengkap']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['eskul']); ?></div>
                                </td>
                                <?php endif; ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($event['jenis_kegiatan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    switch ($event['status']) {
                                        case 'menunggu':
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'disetujui':
                                            $status_class = 'bg-green-100 text-green-800';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'bg-red-100 text-red-800';
                                            break;
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

