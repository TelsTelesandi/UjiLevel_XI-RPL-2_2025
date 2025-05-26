<?php
$page_title = "Event Saya";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';
?>

<main>
    <section class="dashboard-section">
        <h2>Daftar Event yang Anda Ajukan</h2>
        <?php
        $stmt = $conn->prepare("SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $events = $stmt->fetchAll();
        if (count($events) > 0) {
            echo '<table class="table">';
            echo '<thead><tr><th>No</th><th>Nama Event</th><th>Ekskul</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>';
            $no = 1;
            foreach ($events as $event) {
                $status_class = '';
                if ($event['status'] == 'approved') $status_class = 'status-approved';
                elseif ($event['status'] == 'rejected') $status_class = 'status-rejected';
                else $status_class = 'status-pending';
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$event['nama_event']}</td>
                    <td>{$event['ekskul']}</td>
                    <td>{$event['tanggal']}</td>
                    <td><span class='{$status_class}'>{$event['status']}</span></td>
                    <td>
                        <a href='edit_event.php?id={$event['id']}' class='btn btn-secondary' style='margin-right:6px;'>Edit</a>
                        <form action='hapus_event.php?id={$event['id']}' method='POST' style='display:inline;' onsubmit=\"return confirm('Yakin ingin menghapus event ini?');\">
                            <button type='submit' class='btn btn-danger'>Hapus</button>
                        </form>
                    </td>
                </tr>";
                $no++;
            }
            echo '</tbody></table>';
        } else {
            echo '<p>Anda belum mengajukan event apapun.</p>';
        }
        ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?> 