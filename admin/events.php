<?php
$page_title = "Kelola Event";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';
?>

<body class="dashboard-bg">
    <main>
        <section class="manage-events">
            <h2>Kelola Pengajuan Event</h2>
            
            <div class="filter-section">
                <form method="get">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Event</th>
                        <th>Ekskul</th>
                        <th>Pengaju</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
                    $query = "SELECT e.*, u.nama_lengkap 
                              FROM event_pengajuan e 
                              JOIN users u ON e.user_id = u.id";
                    
                    if ($status_filter) {
                        $query .= " WHERE e.status = :status";
                    }
                    
                    $query .= " ORDER BY e.created_at DESC";
                    
                    $stmt = $conn->prepare($query);
                    
                    if ($status_filter) {
                        $stmt->bindParam(':status', $status_filter);
                    }
                    
                    $stmt->execute();
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
                                <td>{$event['nama_lengkap']}</td>
                                <td>{$event['tanggal']}</td>
                                <td><span class='{$status_class}'>{$event['status']}</span></td>";
                        ?>
                        <td>
                            <a href='event_detail.php?id=<?php echo $event['id']; ?>' class='btn btn-view'>Detail</a>
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
        </section>
    </main>
</body>

<?php include '../includes/footer.php'; ?>