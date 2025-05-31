<?php
include __DIR__ . '/../../../config/koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php?action=login");
    exit;
}

// Ambil ID event dari parameter URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data event
$stmt = $pdo->prepare("SELECT * FROM event_pengajuan WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika event tidak ditemukan, redirect ke halaman kelola event
if (!$event) {
    $_SESSION['error'] = "Event tidak ditemukan.";
    header("Location: index.php?action=admin_events");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .content-area {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="content-area rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl text-white font-semibold">Edit Event</h2>
                <a href="index.php?action=admin_events" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-500 bg-opacity-20 text-red-100 px-4 py-2 rounded mb-4">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=update_event" method="POST" enctype="multipart/form-data" 
                  class="space-y-6" onsubmit="return validateForm(this);">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                
                <div>
                    <label class="block text-white text-sm font-bold mb-2">Judul Event</label>
                    <input type="text" name="judul_event" 
                           value="<?= htmlspecialchars($event['judul_event']) ?>"
                           class="w-full px-3 py-2 bg-white bg-opacity-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white"
                           required>
                </div>

                <div>
                    <label class="block text-white text-sm font-bold mb-2">Jenis Kegiatan</label>
                    <select name="jenis_kegiatan" required
                            class="w-full px-3 py-2 bg-white bg-opacity-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white [&>option]:text-gray-800">
                        <option value="">Pilih Jenis Kegiatan</option>
                        <?php
                        $jenis_kegiatan = ['lomba', 'seminar', 'workshop', 'pelatihan', 'lainnya'];
                        foreach ($jenis_kegiatan as $jenis) {
                            $selected = ($event['jenis_kegiatan'] == $jenis) ? 'selected' : '';
                            echo "<option value=\"$jenis\" $selected>" . ucfirst($jenis) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-white text-sm font-bold mb-2">Total Pembiayaan</label>
                    <input type="number" name="total_pembiayaan" 
                           value="<?= (int)$event['total_pembiayaan'] ?>"
                           class="w-full px-3 py-2 bg-white bg-opacity-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white"
                           required>
                </div>

                <div>
                    <label class="block text-white text-sm font-bold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" required
                              class="w-full px-3 py-2 bg-white bg-opacity-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white"><?= htmlspecialchars($event['deskripsi'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-white text-sm font-bold mb-2">Proposal Saat Ini</label>
                    <?php if (!empty($event['proposal'])): ?>
                        <div class="flex items-center space-x-4 mb-2">
                            <a href="../../../public/uploads/<?= htmlspecialchars($event['proposal']) ?>" 
                               download="<?= htmlspecialchars($event['proposal']) ?>"
                               class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-file-pdf mr-2"></i><?= htmlspecialchars($event['proposal']) ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-400">Tidak ada file proposal</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-white text-sm font-bold mb-2">Upload Proposal Baru (Opsional)</label>
                    <input type="file" name="file_proposal" accept=".pdf"
                           class="w-full px-3 py-2 bg-white bg-opacity-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white">
                    <p class="text-sm text-gray-300 mt-1">Biarkan kosong jika tidak ingin mengubah proposal</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="index.php?action=admin_events" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm(form) {
            // Validasi judul
            if (!form.judul_event.value.trim()) {
                alert('Judul event harus diisi!');
                return false;
            }

            // Validasi jenis kegiatan
            if (!form.jenis_kegiatan.value) {
                alert('Jenis kegiatan harus dipilih!');
                return false;
            }

            // Validasi total pembiayaan
            const biaya = parseInt(form.total_pembiayaan.value);
            if (isNaN(biaya) || biaya <= 0) {
                alert('Total pembiayaan harus diisi dengan nilai lebih dari 0!');
                return false;
            }

            // Validasi deskripsi
            if (!form.deskripsi.value.trim()) {
                alert('Deskripsi harus diisi!');
                return false;
            }

            // Validasi file jika diupload
            if (form.file_proposal.files.length > 0) {
                const file = form.file_proposal.files[0];
                
                // Validasi tipe file
                if (!file.type.includes('pdf')) {
                    alert('File harus berformat PDF!');
                    return false;
                }

                // Validasi ukuran file (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    return false;
                }
            }

            return true;
        }
    </script>
</body>
</html> 