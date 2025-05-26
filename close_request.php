<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    header("Location: dashboard_user.php");
    exit;
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];

// Verify event belongs to user and is approved
$stmt = $conn->prepare("SELECT * FROM event_pengajuan WHERE event_id = ? AND user_id = ? AND status = 'disetujui'");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard_user.php");
    exit;
}

$event = $result->fetch_assoc();

// Create closed directory if it doesn't exist
$closed_dir = "uploads/closed/";
if (!file_exists($closed_dir)) {
    mkdir($closed_dir, 0777, true);
}

// Handle form submission
if (isset($_POST['submit'])) {
    $laporan = $_FILES['laporan'];
    $dokumentasi = $_FILES['dokumentasi'];
    $keterangan = $_POST['keterangan'];

    // Upload laporan
    $laporan_name = time() . '_laporan_' . basename($laporan['name']);
    $dokumentasi_name = time() . '_dokumentasi_' . basename($dokumentasi['name']);

    if (
        move_uploaded_file($laporan["tmp_name"], $closed_dir . $laporan_name) &&
        move_uploaded_file($dokumentasi["tmp_name"], $closed_dir . $dokumentasi_name)
    ) {

        $stmt = $conn->prepare("UPDATE event_pengajuan SET 
            status = 'selesai',
            laporan = ?,
            dokumentasi = ?,
            keterangan_selesai = ?,
            tanggal_selesai = NOW()
            WHERE event_id = ?");
        $stmt->bind_param("sssi", $laporan_name, $dokumentasi_name, $keterangan, $event_id);

        if ($stmt->execute()) {
            header("Location: dashboard_user.php?success=Event berhasil ditutup");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database";
        }
    } else {
        $error = "Gagal mengupload file";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutup Event - Sistem Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-rose-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-rose-600 mb-2">Tutup Event</h1>
                <p class="text-purple-600">Lengkapi dokumen penutupan event berikut</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Event Info -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Event</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Judul Event</p>
                        <p class="font-medium"><?= htmlspecialchars($event['judul_event']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jenis Kegiatan</p>
                        <p class="font-medium"><?= htmlspecialchars($event['jenis_kegiatan']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Pengajuan</p>
                        <p class="font-medium"><?= date('d M Y', strtotime($event['tanggal_pengajuan'])) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Pembiayaan</p>
                        <p class="font-medium">Rp<?= number_format($event['total_pembiayaan'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <!-- Close Form -->
            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-6">
                <div class="space-y-6">
                    <!-- Upload Laporan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Laporan Kegiatan (PDF)
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col w-full h-32 border-2 border-rose-200 border-dashed hover:bg-rose-50 hover:border-rose-300 rounded-lg cursor-pointer transition">
                                <div class="flex flex-col items-center justify-center pt-7">
                                    <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600" id="laporan-name">
                                        Upload Laporan
                                    </p>
                                </div>
                                <input type="file" name="laporan" class="opacity-0" accept=".pdf" required />
                            </label>
                        </div>
                    </div>

                    <!-- Upload Dokumentasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Dokumentasi (ZIP)
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col w-full h-32 border-2 border-rose-200 border-dashed hover:bg-rose-50 hover:border-rose-300 rounded-lg cursor-pointer transition">
                                <div class="flex flex-col items-center justify-center pt-7">
                                    <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600" id="dokumentasi-name">
                                        Upload Dokumentasi
                                    </p>
                                </div>
                                <input type="file" name="dokumentasi" class="opacity-0" accept=".zip" required />
                            </label>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan Tambahan
                        </label>
                        <textarea name="keterangan" rows="4" required
                            class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                            placeholder="Tuliskan keterangan tambahan atau ringkasan hasil kegiatan..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="dashboard_user.php"
                            class="px-4 py-2 border border-rose-200 text-rose-600 rounded-lg hover:bg-rose-50 transition">
                            Batal
                        </a>
                        <button type="submit" name="submit"
                            class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition">
                            Tutup Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview file names
        document.querySelector('input[name="laporan"]').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('laporan-name').textContent = fileName;
            }
        });

        document.querySelector('input[name="dokumentasi"]').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('dokumentasi-name').textContent = fileName;
            }
        });
    </script>
</body>

</html>