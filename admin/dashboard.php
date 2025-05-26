<?php
$page_title = "Admin Dashboard";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';
?>

<body class="dashboard-bg">
<main>
    <section class="dashboard-section">
        <h2><i class="fas fa-tachometer-alt" style="color:#2563eb"></i> Dashboard Admin</h2>
        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Total Event</h3>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM event_pengajuan");
                $total_events = $stmt->fetchColumn();
                ?>
                <p><?php echo $total_events; ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-hourglass-half"></i> Event Pending</h3>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM event_pengajuan WHERE status = 'pending'");
                $pending_events = $stmt->fetchColumn();
                ?>
                <p><?php echo $pending_events; ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-users"></i> Total User</h3>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
                $total_users = $stmt->fetchColumn();
                ?>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>
        <div class="recent-events">
            <h3><i class="fas fa-list"></i> Event Terbaru</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Event</th>
                        <th>Ekskul</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT e.*, u.nama_lengkap 
                                         FROM event_pengajuan e 
                                         JOIN users u ON e.user_id = u.id 
                                         ORDER BY e.created_at DESC LIMIT 5");
                    $events = $stmt->fetchAll();
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
                                <td><span class='{$status_class}'>{$event['status']}</span></td>";
                        ?>
                        <td>
                            <form action='../proses/event_proses.php' method='post' style='display:inline;' onsubmit="return confirm('Yakin ingin approve event ini?');">
                                <input type='hidden' name='action' value='approve'>
                                <input type='hidden' name='id' value='<?php echo $event['id']; ?>'>
                                <button type='submit' class='btn btn-approved' <?php if($event['status'] != 'pending') echo 'disabled'; ?>>Approved</button>
                            </form>
                            <form action='../proses/event_proses.php' method='post' style='display:inline;' onsubmit="return confirm('Yakin ingin reject event ini?');">
                                <input type='hidden' name='action' value='reject'>
                                <input type='hidden' name='id' value='<?php echo $event['id']; ?>'>
                                <button type='submit' class='btn btn-rejected' <?php if($event['status'] != 'pending') echo 'disabled'; ?>>Rejected</button>
                            </form>
                        </td>
                        <?php
                        echo "</tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>