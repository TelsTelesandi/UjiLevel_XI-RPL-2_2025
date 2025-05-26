<?php
// Tambahkan link Font Awesome dan Tailwind CSS di sini
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle approval actions
if ($_POST) {
    if (isset($_POST['approve_event']) || isset($_POST['reject_event'])) {
        $event_id = $_POST['event_id'];
        $catatan_admin = $_POST['catatan_admin'] ?? '';
        $status = isset($_POST['approve_event']) ? 'disetujui' : 'ditolak';
        
        // Update event status
        $update_event_query = "UPDATE event_pengajuan SET status = ? WHERE event_id = ?";
        $update_event_stmt = $db->prepare($update_event_query);
        
        if ($update_event_stmt->execute([$status, $event_id])) {
            // Insert or update verification record
            $check_verification_query = "SELECT verifikasi_id FROM verifikasi_event WHERE event_id = ?";
            $check_verification_stmt = $db->prepare($check_verification_query);
            $check_verification_stmt->execute([$event_id]);
            
            if ($verification = $check_verification_stmt->fetch()) {
                // Update existing verification
                $update_verification_query = "UPDATE verifikasi_event SET admin_id = ?, tanggal_verifikasi = ?, catatan_admin = ? WHERE verifikasi_id = ?";
                $update_verification_stmt = $db->prepare($update_verification_query);
                $update_verification_stmt->execute([getUserId(), date('Y-m-d H:i:s'), $catatan_admin, $verification['verifikasi_id']]);
            } else {
                // Create new verification
                $insert_verification_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin) VALUES (?, ?, ?, ?)";
                $insert_verification_stmt = $db->prepare($insert_verification_query);
                $insert_verification_stmt->execute([$event_id, getUserId(), date('Y-m-d H:i:s'), $catatan_admin]);
            }
            
            $success = 'Event berhasil ' . ($status == 'disetujui' ? 'disetujui' : 'ditolak') . '!';
        } else {
            $error = 'Gagal memproses approval!';
        }
    }
}

// Get pending events
$pending_events_query = "SELECT e.*, u.nama_lengkap, u.eskul FROM event_pengajuan e 
                        JOIN users u ON e.user_id = u.user_id 
                        WHERE e.status = 'menunggu' 
                        ORDER BY e.created_at ASC";
$pending_events = $db->query($pending_events_query)->fetchAll(PDO::FETCH_ASSOC);

// Get processed events
$processed_events_query = "SELECT e.*, u.nama_lengkap, u.eskul, v.catatan_admin, v.tanggal_verifikasi 
                          FROM event_pengajuan e 
                          JOIN users u ON e.user_id = u.user_id 
                          LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
                          WHERE e.status IN ('disetujui', 'ditolak') 
                          ORDER BY e.created_at DESC";
$processed_events = $db->query($processed_events_query)->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Event Approvals - Event Submission System';
include '../includes/header.php';
?>

<nav class="bg-white border-b border-blue-600 shadow-md px-6 py-8 flex items-center justify-between">
    <div class="flex items-center space-x-2">
        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        <span class="text-lg font-semibold text-gray-800">Event Submission System</span>
    </div>
    <div class="flex items-center space-x-6 text-sm text-gray-700">
        <a href="../dashboard.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="users.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-users mr-1"></i> Users
        </a>
        <a href="approvals.php" class="flex items-center text-blue-600 font-semibold">
            <i class="fas fa-check-circle mr-1"></i> Approvals
        </a>
        <a href="reports.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-file-alt mr-1"></i> Reports
        </a>
        <span class="flex items-center text-gray-700">
            <i class="fas fa-user mr-1"></i> Administrator
        </span>
        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center transition-colors">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
    </div>
</nav>


<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-check-circle mr-3"></i>Event Approvals
        </h1>
        <p class="text-gray-600 mt-2">Kelola persetujuan event ekstrakurikuler</p>
    </div>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $success; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <!-- Pending Approvals -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-clock mr-2"></i>Pending Approvals (<?php echo count($pending_events); ?>)
            </h2>
        </div>
        <div class="p-6">
            <?php if (empty($pending_events)): ?>
            <p class="text-gray-500 text-center py-8">Tidak ada event yang menunggu persetujuan.</p>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($pending_events as $event): ?>
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></h3>
                            <p class="text-gray-600">
                                Oleh: <?php echo htmlspecialchars($event['nama_lengkap']); ?> 
                                (<?php echo htmlspecialchars($event['eskul']); ?>)
                            </p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Menunggu
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Jenis Kegiatan</p>
                            <p class="font-medium"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Pembiayaan</p>
                            <p class="font-medium"><?php echo htmlspecialchars($event['total_pembiayaan']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Pengajuan</p>
                            <p class="font-medium"><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Deskripsi</p>
                        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></p>
                    </div>
                    
                    <?php if ($event['proposal']): ?>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Proposal</p>
                        <a href="../uploads/proposals/<?php echo $event['proposal']; ?>" target="_blank" 
                           class="text-primary hover:underline">
                            <i class="fas fa-file-pdf mr-1"></i>Download Proposal
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                        
                        <div>
                            <label for="catatan_admin_<?php echo $event['event_id']; ?>" class="block text-sm font-medium text-gray-700">Catatan Admin</label>
                            <textarea id="catatan_admin_<?php echo $event['event_id']; ?>" name="catatan_admin" rows="3"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                                      placeholder="Berikan catatan untuk pengaju..."></textarea>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="submit" name="approve_event" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Setujui
                            </button>
                            <button type="submit" name="reject_event" 
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>Tolak
                            </button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Processed Events -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-history mr-2"></i>Processed Events
            </h2>
        </div>
        <div class="p-6">
            <?php if (empty($processed_events)): ?>
            <p class="text-gray-500 text-center py-8">Belum ada event yang diproses.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Verifikasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($processed_events as $event): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($event['nama_lengkap']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['eskul']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $status_class = $event['status'] == 'disetujui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $event['tanggal_verifikasi'] ? date('d/m/Y H:i', strtotime($event['tanggal_verifikasi'])) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo $event['catatan_admin'] ? htmlspecialchars($event['catatan_admin']) : '-'; ?>
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
