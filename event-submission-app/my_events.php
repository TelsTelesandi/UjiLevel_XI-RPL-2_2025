<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

if (isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle close event
if (isset($_POST['close_event'])) {
    $event_id = $_POST['event_id'];
    
    // Check if event is approved and not already closed
    $check_query = "SELECT e.*, v.verifikasi_id FROM event_pengajuan e 
                    LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
                    WHERE e.event_id = ? AND e.user_id = ? AND e.status = 'disetujui'";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$event_id, getUserId()]);
    
    if ($event = $check_stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($event['verifikasi_id']) {
            // Update existing verification
            $update_query = "UPDATE verifikasi_event SET status = 'closed' WHERE verifikasi_id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$event['verifikasi_id']]);
        } else {
            // Create new verification record
            $insert_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, status) 
                            VALUES (?, ?, ?, 'closed')";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([$event_id, getUserId(), date('Y-m-d H:i:s')]);
        }
        
        $success = 'Event berhasil ditutup!';
    }
}

// Get user events
$user_id = getUserId();
$query = "SELECT e.*, v.status as verification_status, v.catatan_admin, v.tanggal_verifikasi 
          FROM event_pengajuan e 
          LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
          WHERE e.user_id = ? 
          ORDER BY e.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'My Events - Event Submission System';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-6xl mx-auto px-4 py-10">
    <!-- Judul Halaman -->
    <div class="mb-6 text-center">
        <h1 class="text-4xl font-extrabold text-blue-800 flex items-center justify-center gap-3">
            <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
            My Events
        </h1>
        <p class="text-gray-600 mt-1 text-md">Daftar event yang telah Anda ajukan</p>
    </div>

    <!-- Card Event List -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-400 text-white">
            <h2 class="text-lg font-semibold tracking-wide flex items-center gap-2">
                <i class="fas fa-list text-white"></i>
                Event List
            </h2>
            <a href="submit_event.php"
               class="inline-flex items-center gap-2 bg-white text-blue-600 font-semibold px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition-all duration-200">
                <i class="fas fa-plus-circle"></i> Submit New Event
            </a>
        </div>

        <!-- Daftar Event -->
        <div class="p-6">
            <?php if (empty($events)): ?>
            <div class="text-center px-10 py-16">
                <img src="https://www.svgrepo.com/show/421261/event-calendar.svg" alt="No Events" class="mx-auto w-32 opacity-60 mb-6">
                <p class="text-gray-500 text-lg italic">Belum ada event yang diajukan.</p>
                <p class="text-sm text-gray-400 mt-1">Silakan klik tombol di atas untuk mengajukan event baru.</p>
            </div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($events as $event): ?>
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($event['judul_event']); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></p>
                        </div>
                        <div class="flex space-x-2">
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
                            <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $status_class; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                            
                            <?php if ($event['verification_status']): ?>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $event['verification_status'] == 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($event['verification_status']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Pembiayaan</p>
                            <p class="font-medium"><?php echo htmlspecialchars($event['total_pembiayaan']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Pengajuan</p>
                            <p class="font-medium"><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Proposal</p>
                            <?php if ($event['proposal']): ?>
                            <a href="uploads/proposals/<?php echo $event['proposal']; ?>" target="_blank" 
                               class="text-primary hover:underline">
                                <i class="fas fa-file-pdf mr-1"></i>Download
                            </a>
                            <button onclick="window.open('uploads/proposals/<?php echo $event['proposal']; ?>', '_blank'); window.print();" 
                                    class="text-primary hover:underline ml-2">
                                <i class="fas fa-print mr-1"></i>Cetak
                            </button>
                            <?php else: ?>
                            <p class="text-gray-400">Tidak ada file</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Deskripsi</p>
                        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></p>
                    </div>
                    
                    <?php if ($event['catatan_admin']): ?>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-2">Catatan Admin</p>
                        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['catatan_admin'])); ?></p>
                        <?php if ($event['tanggal_verifikasi']): ?>
                        <p class="text-xs text-gray-500 mt-2">
                            Diverifikasi pada: <?php echo date('d/m/Y H:i', strtotime($event['tanggal_verifikasi'])); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($event['status'] == 'disetujui' && (!$event['verification_status'] || $event['verification_status'] != 'closed')): ?>
                    <div class="flex justify-end">
                        <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup event ini?')">
                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                            <button type="submit" name="close_event" 
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>Close Event
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>




 
