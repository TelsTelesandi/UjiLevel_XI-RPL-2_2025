<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID event tidak ditemukan.</div>';
    include '../includes/footer.php';
    exit();
}

$id = intval($_GET['id']);
$success = '';
$error = '';

// Ambil data event
$stmt = $conn->prepare("SELECT * FROM event_pengajuan WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$event = $stmt->fetch();

if (!$event) {
    echo '<div class="alert alert-danger">Data event tidak ditemukan atau Anda tidak berhak mengedit event ini.</div>';
    include '../includes/footer.php';
    exit();
}

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
        $error = "Tanggal event harus minimal 1 minggu dari hari ini.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE event_pengajuan SET nama_event=?, ekskul=?, tanggal=?, deskripsi=?, kebutuhan=? WHERE id=? AND user_id=?");
            $stmt->execute([$nama_event, $ekskul, $tanggal, $deskripsi, $kebutuhan, $id, $_SESSION['user_id']]);
            $success = "Event berhasil diupdate.";
            header("Location: my_events.php?success=1");
            exit();
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<main>
    <section class="event-form-section">
        <h2>Edit Event Ekskul</h2>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama_event">Nama Event*</label>
                <input type="text" id="nama_event" name="nama_event" required value="<?php echo htmlspecialchars($event['nama_event']); ?>">
            </div>
            <div class="form-group">
                <label for="ekskul">Ekstrakurikuler*</label>
                <select id="ekskul" name="ekskul" required>
                    <option value="">-- Pilih Ekskul --</option>
                    <option value="Pramuka" <?php if($event['ekskul']==='Pramuka') echo 'selected'; ?>>Pramuka</option>
                    <option value="PMR" <?php if($event['ekskul']==='PMR') echo 'selected'; ?>>PMR</option>
                    <option value="Paskibra" <?php if($event['ekskul']==='Paskibra') echo 'selected'; ?>>Paskibra</option>
                    <option value="KIR" <?php if($event['ekskul']==='KIR') echo 'selected'; ?>>Karya Ilmiah Remaja</option>
                    <option value="Olahraga" <?php if($event['ekskul']==='Olahraga') echo 'selected'; ?>>Olahraga</option>
                    <option value="Seni" <?php if($event['ekskul']==='Seni') echo 'selected'; ?>>Seni dan Budaya</option>
                    <option value="Lainnya" <?php if($event['ekskul']==='Lainnya') echo 'selected'; ?>>Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal Pelaksanaan*</label>
                <input type="date" id="tanggal" name="tanggal" required value="<?php echo htmlspecialchars($event['tanggal']); ?>">
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi Kegiatan*</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($event['deskripsi'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="kebutuhan">Kebutuhan Fasilitas</label>
                <textarea id="kebutuhan" name="kebutuhan" rows="3"><?php echo htmlspecialchars($event['kebutuhan'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Event</button>
                <a href="my_events.php" class="btn btn-secondary">Batal</a>
            </div>
            <p class="form-note">* Wajib diisi</p>
        </form>
    </section>
</main>
<?php include '../includes/footer.php'; ?> 