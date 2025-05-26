<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

if (isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

$success = '';
$error = '';

if ($_POST) {
    $judul_event = $_POST['judul_event'] ?? '';
    $jenis_kegiatan = $_POST['jenis_kegiatan'] ?? '';
    $total_pembiayaan = $_POST['total_pembiayaan'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'] ?? '';
    
    // Handle file upload
    $proposal = '';
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == 0) {
        $upload_dir = 'uploads/proposals/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION);
        $proposal = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $proposal;
        
        if (!move_uploaded_file($_FILES['proposal']['tmp_name'], $upload_path)) {
            $error = 'Gagal mengupload file proposal!';
        }
    }
    
    if (!$error && $judul_event && $jenis_kegiatan && $total_pembiayaan && $deskripsi && $tanggal_pengajuan) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([getUserId(), $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal, $deskripsi, $tanggal_pengajuan])) {
            $success = 'Event berhasil diajukan!';
            // Clear form
            $_POST = array();
        } else {
            $error = 'Gagal mengajukan event!';
        }
    } else if (!$error) {
        $error = 'Mohon isi semua field!';
    }
}

$page_title = 'Submit Event - Event Submission System';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-plus mr-3"></i>Submit New Event
        </h1>
        <p class="text-gray-600 mt-2">Ajukan event ekstrakurikuler baru</p>
    </div>

    <div class="bg-blue-100 rounded-lg shadow-md p-6">
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

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="judul_event" class="block text-sm font-semibold text-blue-700">Judul Event</label>
                    <input type="text" id="judul_event" name="judul_event" required
                           value="<?php echo htmlspecialchars($_POST['judul_event'] ?? ''); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div>
                    <label for="jenis_kegiatan" class="block text-sm font-semibold text-blue-700">Jenis Kegiatan</label>
                    <select id="jenis_kegiatan" name="jenis_kegiatan" required
                            class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Pilih Jenis Kegiatan</option>
                        <option value="Kompetisi" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Kompetisi' ? 'selected' : ''; ?>>Kompetisi</option>
                        <option value="Workshop" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                        <option value="Seminar" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Seminar' ? 'selected' : ''; ?>>Seminar</option>
                        <option value="Pameran" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Pameran' ? 'selected' : ''; ?>>Pameran</option>
                        <option value="Pertunjukan" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Pertunjukan' ? 'selected' : ''; ?>>Pertunjukan</option>
                        <option value="Lainnya" <?php echo ($_POST['jenis_kegiatan'] ?? '') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="total_pembiayaan" class="block text-sm font-semibold text-blue-700">Total Pembiayaan</label>
                    <input type="text" id="total_pembiayaan" name="total_pembiayaan" required
                           value="<?php echo htmlspecialchars($_POST['total_pembiayaan'] ?? ''); ?>"
                           placeholder="Rp 1.000.000"
                           class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div>
                    <label for="tanggal_pengajuan" class="block text-sm font-semibold text-blue-700">Tanggal Pengajuan</label>
                    <input type="date" id="tanggal_pengajuan" name="tanggal_pengajuan" required
                           value="<?php echo $_POST['tanggal_pengajuan'] ?? date('Y-m-d'); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>
            
            <div>
                <label for="proposal" class="block text-sm font-semibold text-blue-700">File Proposal</label>
                <input type="file" id="proposal" name="proposal" accept=".pdf,.doc,.docx"
                       class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                <p class="mt-1 text-sm text-blue-500 italic">Format yang didukung: PDF, DOC, DOCX (Max: 5MB)</p>
            </div>
            
            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-blue-700">Deskripsi Kegiatan</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required
                          class="mt-1 block w-full px-4 py-2 border border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                          placeholder="Jelaskan detail kegiatan yang akan dilaksanakan..."><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="dashboard.php" 
                   class="px-6 py-2 border border-blue-400 text-blue-600 rounded-lg hover:bg-blue-50 transition font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Event
                </button>
            </div>
        </form>
    </div>
</div>

