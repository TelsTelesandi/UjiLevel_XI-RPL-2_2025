<?php
$page_title = "Form Pengajuan Event";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_event = clean_input($_POST['nama_event']);
    $ekskul = clean_input($_POST['ekskul']);
    $tanggal = clean_input($_POST['tanggal']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $kebutuhan = clean_input($_POST['kebutuhan']);

    // Validasi tanggal minimal 1 minggu dari sekarang
    $today = new DateTime();
    $event_date = new DateTime($tanggal);
    $interval = $today->diff($event_date);
    if ($interval->invert || $interval->days < 7) {
        $error = "Pengajuan event harus minimal 1 minggu sebelum tanggal pelaksanaan.";
    } else {
        if (empty($error)) {
            try {
                $stmt = $conn->prepare("INSERT INTO event_pengajuan (user_id, nama_event, ekskul, tanggal, deskripsi, kebutuhan) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $nama_event,
                    $ekskul,
                    $tanggal,
                    $deskripsi,
                    $kebutuhan
                ]);
                $success = "Pengajuan event berhasil dikirim!";
            } catch (PDOException $e) {
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}
?>

<main>
    <section class="event-form-section">
        <h2><i class="fas fa-calendar-plus" style="color:#2563eb;"></i> Formulir Pengajuan Event Ekskul</h2>
        <p style="text-align:center; color:#555; margin-bottom:22px;">Silakan isi data event ekskul yang ingin diajukan. Pastikan data yang diisi sudah benar dan lengkap.</p>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="event_form.php" method="post">
            <div class="form-group">
                <label for="nama_event"><i class="fas fa-bullhorn"></i> Nama Event*</label>
                <input type="text" id="nama_event" name="nama_event" required placeholder="Contoh: Lomba Pramuka Tingkat Kota">
            </div>
            <div class="form-group">
                <label for="ekskul"><i class="fas fa-users"></i> Ekstrakurikuler*</label>
                <select id="ekskul" name="ekskul" required>
                    <option value="">-- Pilih Ekskul --</option>
                    <option value="Pramuka">Pramuka</option>
                    <option value="PMR">PMR</option>
                    <option value="Paskibra">Paskibra</option>
                    <option value="KIR">Karya Ilmiah Remaja</option>
                    <option value="Olahraga">Olahraga</option>
                    <option value="Seni">Seni dan Budaya</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal"><i class="fas fa-calendar-day"></i> Tanggal Pelaksanaan*</label>
                <input type="date" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi Kegiatan*</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan secara singkat tujuan, peserta, dan rangkaian kegiatan..."></textarea>
            </div>
            <div class="form-group">
                <label for="kebutuhan"><i class="fas fa-tools"></i> Kebutuhan Fasilitas</label>
                <textarea id="kebutuhan" name="kebutuhan" rows="3" placeholder="Contoh: Sound system, tenda, kursi, dll"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Ajukan Event</button>
                <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset Form</button>
            </div>
            <p class="form-note">* Wajib diisi</p>
        </form>
    </section>
</main>

<?php include '../includes/footer.php'; ?> 