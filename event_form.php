<?php
include 'config.php';

// Check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
  header("Location: login.php");
  exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if (isset($_POST['submit'])) {
  $user_id = $_SESSION['user_id'];
  $judul_event = $_POST['judul_event'];
  $jenis_kegiatan = $_POST['jenis_kegiatan'];
  $total_pembiayaan = $_POST['total_pembiayaan'];
  $deskripsi = $_POST['deskripsi'];

  // Handle file upload
  $proposal = $_FILES['proposal'];
  $proposal_name = time() . '_' . basename($proposal['name']);
  $target_dir = "uploads/";
  $target_file = $target_dir . $proposal_name;

  if (move_uploaded_file($proposal["tmp_name"], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'menunggu')");
    $stmt->bind_param("isssss", $user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_name, $deskripsi);

    if ($stmt->execute()) {
      header("Location: dashboard_user.php?success=Event berhasil diajukan!");
      exit;
    } else {
      $error_message = "Gagal mengajukan event!";
    }
  } else {
    $error_message = "Gagal mengupload file!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajukan Event - Sistem Event</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-rose-50 to-purple-50 min-h-screen">
  <?php if ($error_message): ?>
    <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-rose-600 mb-2">Ajukan Event</h1>
        <p class="text-purple-600">Silakan lengkapi form pengajuan event di bawah ini</p>
      </div>

      <!-- Form Pengajuan -->
      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-6">
        <div class="space-y-6">
          <!-- Judul Event -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Judul Event
            </label>
            <input type="text" name="judul_event" required
              class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400">
          </div>

          <!-- Jenis Kegiatan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Jenis Kegiatan
            </label>
            <input type="text" name="jenis_kegiatan" required
              class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400">
          </div>

          <!-- Total Pembiayaan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Total Pembiayaan (Rp)
            </label>
            <input type="number" name="total_pembiayaan" required
              class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400">
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi Kegiatan
            </label>
            <textarea name="deskripsi" rows="4" required
              class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
              placeholder="Jelaskan detail kegiatan yang akan dilaksanakan..."></textarea>
          </div>

          <!-- Upload Proposal -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Upload Proposal (PDF)
            </label>
            <div class="flex items-center justify-center w-full">
              <label class="flex flex-col w-full h-32 border-2 border-rose-200 border-dashed hover:bg-rose-50 hover:border-rose-300 rounded-lg cursor-pointer transition">
                <div class="flex flex-col items-center justify-center pt-7">
                  <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600" id="file-name">
                    Upload Proposal
                  </p>
                </div>
                <input type="file" name="proposal" class="opacity-0" accept=".pdf" required />
              </label>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-4">
            <a href="dashboard_user.php"
              class="px-4 py-2 border border-rose-200 text-rose-600 rounded-lg hover:bg-rose-50 transition">
              Batal
            </a>
            <button type="submit" name="submit"
              class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition">
              Ajukan Event
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Preview file name
    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
      const fileName = e.target.files[0]?.name;
      if (fileName) {
        document.getElementById('file-name').textContent = fileName;
      }
    });
  </script>
</body>

</html>