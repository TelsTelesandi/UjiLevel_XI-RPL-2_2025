<?php
session_start();
require_once 'db_connect.php';

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$event_id = null;
$pengajuan = null;
$error_message = '';
$success_message = '';

// Ambil ID pengajuan dari URL
if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    
    // Ambil data pengajuan
    $sql = "SELECT * FROM event_pengajuan WHERE event_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pengajuan = $result->fetch_assoc();
    $stmt->close();
    
    // Jika pengajuan tidak ditemukan
    if (!$pengajuan) {
        // Arahkan kembali atau tampilkan error
        header("Location: admin_view_requests.php");
        exit();
    }
} else {
    // Jika ID tidak ada di URL
    header("Location: admin_view_requests.php");
    exit();
}

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request'])) {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];

    // Validasi dan konversi total_pembiayaan
    if (empty($total_pembiayaan) && $total_pembiayaan !== '0') { // Cek jika kosong atau hanya whitespace, tapi izinkan '0'
        $total_pembiayaan = 0; // Set nilai default 0 jika kosong
    } else {
        // Hapus karakter non-angka dan konversi ke integer
        $total_pembiayaan = intval(preg_replace('/[^0-9]/', '', $total_pembiayaan));
    }
    
    $update_sql = "UPDATE event_pengajuan SET judul_event = ?, jenis_kegiatan = ?, total_pembiayaan = ?, deskripsi = ?";
    $params = ["ssss", $judul_event, $jenis_kegiatan, $total_pembiayaan, $deskripsi];

    // Handle file proposal jika diupload baru
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/proposals/'; // Sesuaikan dengan folder upload proposal Anda
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx']; // Sesuaikan tipe file yang diizinkan
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $error_message = 'Hanya file PDF, DOC, atau DOCX yang diizinkan.';
        } else {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['proposal']['tmp_name'], $target_file)) {
                // Hapus proposal lama jika ada dan bukan file default
                if (!empty($pengajuan['Proposal']) && file_exists($pengajuan['Proposal']) && strpos($pengajuan['Proposal'], $upload_dir) !== false) {
                    unlink($pengajuan['Proposal']);
                }
                
                $update_sql .= ", Proposal = ?";
                $params[0] .= "s"; // Tambah tipe string untuk Proposal
                $params[] = $target_file; // Tambahkan path file baru ke parameter
            } else {
                $error_message = 'Gagal mengupload file proposal.';
            }
        }
    }
    
    // Lanjutkan update jika tidak ada error upload
    if (empty($error_message)) {
        $update_sql .= " WHERE event_id = ?";
        $params[0] .= "i"; // Tambah tipe integer untuk event_id
        $params[] = $event_id; // Tambahkan event_id ke parameter

        $stmt_update = $conn->prepare($update_sql);
        
        if ($stmt_update) {
             // Bind parameter secara dinamis
             $bind_names[] = $params[0];
             for ($i = 1; $i < count($params); $i++) {
                 $bind_name = 'param' . $i;
                 $$bind_name = $params[$i];
                 $bind_names[] = &$$bind_name;
             }
             call_user_func_array(array($stmt_update, 'bind_param'), $bind_names);

            if ($stmt_update->execute()) {
                $success_message = "Pengajuan event berhasil diperbarui!";
                // Refresh data pengajuan setelah update berhasil
                $sql_refresh = "SELECT * FROM event_pengajuan WHERE event_id = ? LIMIT 1";
                $stmt_refresh = $conn->prepare($sql_refresh);
                $stmt_refresh->bind_param("i", $event_id);
                $stmt_refresh->execute();
                $result_refresh = $stmt_refresh->get_result();
                $pengajuan = $result_refresh->fetch_assoc();
                $stmt_refresh->close();
            } else {
                $error_message = "Gagal memperbarui pengajuan event: " . $conn->error; // Tambahkan error database
            }
            $stmt_update->close();
        } else {
            $error_message = "Gagal menyiapkan statement update: " . $conn->error; // Tambahkan error database
        }
    }
}

$conn->close();

// Determine current page for active link highlighting (copy from admin_view_requests.php)
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengajuan Event - Admin</title>
    <!-- Link Tailwind CSS CDN (Copy from admin_view_requests.php) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
         /* Custom scrollbar for content */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Optional: Simple slide-in animation for sidebar items (copy from dashboard) */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-slide-in-left {
             animation: slideInLeft 0.4s ease-out;
        }
         /* Delay for staggered animation */
        .animate-slide-in-left.delay-100 { animation-delay: 0.1s; }
        .animate-slide-in-left.delay-200 { animation-delay: 0.2s; }
        .animate-slide-in-left.delay-300 { animation-delay: 0.3s; }
        .animate-slide-in-left.delay-400 { animation-delay: 0.4s; }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal flex h-screen overflow-hidden">

     <!-- Sidebar (Copy from admin_view_requests.php) -->
    <div class="bg-gray-900 text-gray-300 w-64 flex flex-col justify-between shadow-lg">
        <div class="py-6 px-4">
            <h1 class="text-3xl font-extrabold text-white mb-8">Admin Panel</h1>
            <nav class="space-y-3">
                <a href="admin_dashboard.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_dashboard.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left">
                    Dashboard
                </a>
                <a href="admin_view_requests.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_view_requests.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-100">
                    Pengajuan Event
                </a>
                <a href="admin_manage_users.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_manage_users.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-200">
                    Manajemen Pengguna
                </a>
                <a href="admin_reports.php" class="block py-2.5 px-4 rounded-lg transition duration-200 <?php echo ($current_page == 'admin_reports.php') ? 'bg-gray-700 text-white shadow-md' : 'hover:bg-gray-700 hover:text-white'; ?> animate-slide-in-left delay-300">
                    Laporan
                </a>
            </nav>
        </div>
        <div class="py-4 px-6 border-t border-gray-700">
             <a href="logout.php" class="block py-2.5 px-4 text-red-400 rounded-lg transition duration-200 hover:bg-gray-700 hover:text-red-500 animate-slide-in-left delay-400">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Edit Pengajuan Event</h1>
            <p class="text-gray-600">Formulir untuk mengedit detail pengajuan event.</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($pengajuan): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="admin_edit_request.php?id=<?php echo $event_id; ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_request" value="1"> <!-- Marker untuk proses update -->

                    <div class="mb-4">
                        <label for="judul_event" class="block text-gray-700 text-sm font-bold mb-2">Judul Event:</label>
                        <input type="text" name="judul_event" id="judul_event" value="<?php echo htmlspecialchars($pengajuan['judul_event']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="jenis_kegiatan" class="block text-gray-700 text-sm font-bold mb-2">Jenis Kegiatan:</label>
                        <input type="text" name="jenis_kegiatan" id="jenis_kegiatan" value="<?php echo htmlspecialchars($pengajuan['jenis_kegiatan']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="total_pembiayaan" class="block text-gray-700 text-sm font-bold mb-2">Total Pembiayaan:</label>
                        <input type="number" name="total_pembiayaan" id="total_pembiayaan" value="<?php echo htmlspecialchars($pengajuan['total_pembiayaan']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($pengajuan['deskripsi']); ?></textarea>
                    </div>

                     <div class="mb-4">
                        <label for="proposal" class="block text-gray-700 text-sm font-bold mb-2">Proposal (Ubah jika perlu):</label>
                        <?php if (!empty($pengajuan['Proposal'])): ?>
                             <p class="text-gray-600 text-sm mb-2">File saat ini: <a href="<?php echo htmlspecialchars($pengajuan['Proposal']); ?>" target="_blank" class="text-blue-600 hover:underline">Lihat Proposal</a></p>
                        <?php endif; ?>
                        <input type="file" name="proposal" id="proposal" accept=".pdf,.doc,.docx" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                         <p class="text-gray-600 text-xs mt-1">Biarkan kosong jika tidak ingin mengubah proposal. Diizinkan: .pdf, .doc, .docx</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="admin_view_requests.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Kembali</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-700 text-center">Pengajuan event tidak ditemukan.</p>
            </div>
        <?php endif; ?>


    </div>

</body>
</html> 