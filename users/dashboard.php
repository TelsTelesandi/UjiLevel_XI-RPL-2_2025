<?php
$page_title = "User Dashboard";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';
?>

<body class="dashboard-bg">
<main>
    <section class="dashboard-section">
        <h2>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></h2>
        
        <div class="user-stats">
            <div class="stat-card">
                <h3>Total Event Diajukan</h3>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM event_pengajuan WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $total_events = $stmt->fetchColumn();
                ?>
                <p><?php echo $total_events; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Event Disetujui</h3>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM event_pengajuan WHERE user_id = ? AND status = 'approved'");
                $stmt->execute([$_SESSION['user_id']]);
                $approved_events = $stmt->fetchColumn();
                ?>
                <p><?php echo $approved_events; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Event Ditolak</h3>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM event_pengajuan WHERE user_id = ? AND status = 'rejected'");
                $stmt->execute([$_SESSION['user_id']]);
                $rejected_events = $stmt->fetchColumn();
                ?>
                <p><?php echo $rejected_events; ?></p>
            </div>
        </div>
        
        <div class="quick-actions">
            <a href="event_form.php" class="btn btn-primary">Ajukan Event Baru</a>
            <a href="my_events.php" class="btn btn-secondary">Lihat Event Saya</a>
        </div>
        
        <div class="recent-events">
            <h3>Event Terakhir Anda</h3>
            <?php
            $stmt = $conn->prepare("SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$_SESSION['user_id']]);
            $events = $stmt->fetchAll();
            
            if (count($events) > 0) {
                echo '<table class="table">';
                echo '<thead><tr><th>No</th><th>Nama Event</th><th>Ekskul</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>';
                
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
                        </tr>";
                    $no++;
                }
                
                echo '</tbody></table>';
            } else {
                echo '<p>Anda belum mengajukan event apapun.</p>';
            }
            ?>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>