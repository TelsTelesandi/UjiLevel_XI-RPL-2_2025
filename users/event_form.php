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

    // Validasi tanggal minimal 1 minggu dari sekarang
    $today = new DateTime();
    $event_date = new DateTime($tanggal);
    $interval = $today->diff($event_date);
    if ($interval->invert || $interval->days < 7) {
        $error = "Pengajuan event harus minimal 1 minggu sebelum tanggal pelaksanaan.";
    } else {
        if (empty($error)) {
            try {
                $stmt = $conn->prepare("INSERT INTO event_pengajuan (user_id, nama_event, ekskul, tanggal) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $nama_event,
                    $ekskul,
                    $tanggal
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
        <h2>Formulir Pengajuan Event Ekstrakurikuler</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="event_form.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_event">Nama Event*</label>
                <input type="text" id="nama_event" name="nama_event" required>
            </div>
            
            <div class="form-group">
                <label for="ekskul">Ekstrakurikuler*</label>
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
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal">Tanggal Pelaksanaan*</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Ajukan Event</button>
                <button type="reset" class="btn-secondary">Reset Form</button>
            </div>
            
            <p class="form-note">* Wajib diisi</p>
        </form>
    </section>
</main>

<?php include '../includes/footer.php'; ?>