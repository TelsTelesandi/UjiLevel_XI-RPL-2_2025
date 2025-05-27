<?php
include 'config.php';
include 'session.php';

// Redirect if not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle status updates
if (isset($_POST['action']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $action = $_POST['action'];
    $status = ($action == 'approve') ? 'Disetujui' : 'Ditolak';
    
    $stmt = $conn->prepare("UPDATE event SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $event_id);
    $stmt->execute();
}

// Handle close request
if (isset($_POST['close']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $stmt = $conn->prepare("UPDATE event SET status = 'Selesai' WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
}

// Handle user deletion if requested
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND username != ?");
    $stmt->bind_param("is", $user_id, $_SESSION['username']);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM event ORDER BY tanggal_event DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        .card-body {
            padding: 20px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #2196F3;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container dashboard-container">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Admin Dashboard</h2>
                <p class="text-muted">Manage users and system settings</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">User Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("SELECT id, username, role FROM users ORDER BY id");
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><span class="badge bg-<?php echo $row['role'] == 'admin' ? 'primary' : 'secondary'; ?>"><?php echo $row['role']; ?></span></td>
                                        <td>
                                            <?php if ($row['username'] != $_SESSION['username']): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
