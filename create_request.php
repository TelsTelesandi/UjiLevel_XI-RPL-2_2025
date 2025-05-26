<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_pengajuan = date('Y-m-d'); // Tanggal hari ini
    $status = 'menunggu'; // Status awal
    $proposal_path = null;

    // Handle file upload
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['proposal']['tmp_name'];
        $file_name = $_FILES['proposal']['name'];
        $file_size = $_FILES['proposal']['size'];
        $file_type = $_FILES['proposal']['type'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Tentukan direktori upload dan nama file unik
        $upload_dir = 'uploads/proposals/';
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_file_name = md5(time() . $file_name) . '.' . $file_extension; // Buat nama unik
        $dest_path = $upload_dir . $new_file_name;

        // Pindahkan file dari temporary ke direktori tujuan
        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            $proposal_path = $dest_path; // Simpan path di database
        } else {
            $message = "Gagal mengunggah file proposal.";
            $message_type = "error";
        }
    }

    // Jika upload file berhasil atau tidak ada file yang diupload (dan tidak ada error lain), masukkan data ke database
    if ($message_type !== "error") {
         // Siapkan dan bind statement INSERT
        $sql = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiyaan, Proposal, deskripsi, tanggal_pengajuan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("isssssss", $user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_path, $deskripsi, $tanggal_pengajuan, $status);

            if ($stmt->execute()) {
                $message = "Pengajuan event berhasil dikirim!";
                $message_type = "success";
                // Clear form fields after successful submission if needed
                header("Location: user_dashboard.php");
                exit();
            } else {
                $message = "Error saat menyimpan data: " . $stmt->error;
                $message_type = "error";
                 // Hapus file yang sudah terupload jika insert database gagal
                if ($proposal_path && file_exists($proposal_path)) {
                    unlink($proposal_path);
                }
            }
            $stmt->close();
        } else {
            $message = "Error menyiapkan statement database: " . $conn->error;
            $message_type = "error";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event Baru - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center mb-6">Ajukan Event Baru</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form action="create_request.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="judul_event">
                    Judul Event
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="judul_event" type="text" placeholder="Judul Event" name="judul_event" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="jenis_kegiatan">
                    Jenis Kegiatan
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="jenis_kegiatan" type="text" placeholder="Contoh: Seminar, Lomba, Bakti Sosial" name="jenis_kegiatan" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_pembiayaan">
                    Total Pembiayaan (Contoh: 1500000)
                </label>
                 <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="total_pembiayaan" type="number" placeholder="Total Pembiayaan" name="total_pembiayaan" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="deskripsi">
                    Deskripsi Kegiatan
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="deskripsi" placeholder="Jelaskan rincian kegiatan..." name="deskripsi" rows="4" required></textarea>
            </div>

             <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="proposal">
                    Upload File Proposal (PDF)
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="proposal" type="file" name="proposal" accept=".pdf" required>
                <p class="text-gray-600 text-xs mt-1">Hanya file PDF yang diperbolehkan.</p>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Ajukan Event
                </button>
                <a href="user_dashboard.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>
    </div>

</body>
</html> 