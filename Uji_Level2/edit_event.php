<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get event details
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    // Get event data and verify ownership
    $stmt = $pdo->prepare("
        SELECT ep.*, u.nama_lengkap as pengaju 
        FROM event_pengajuan ep 
        JOIN users u ON ep.user_id = u.user_id 
        WHERE ep.event_id = ? AND ep.user_id = ? AND ep.status = 'ditolak'
    ");
    $stmt->execute([$event_id, $user_id]);
    $event = $stmt->fetch();

    // If event doesn't exist or user doesn't have permission
    if (!$event) {
        header("Location: dashboard.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_edit'])) {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    
    try {
        $pdo->beginTransaction();
        
        // Update event details
        $update_sql = "UPDATE event_pengajuan SET 
                      judul_event = ?, 
                      jenis_kegiatan = ?, 
                      Total_pembiayaan = ?, 
                      deskripsi = ?,
                      status = 'menunggu',
                      tanggal_pengajuan = CURDATE()";
        $params = [$judul_event, $jenis_kegiatan, $total_pembiayaan, $deskripsi];
        
        // Handle new proposal file if uploaded
        if (!empty($_FILES['proposal']['name'])) {
            $proposal = $_FILES['proposal']['name'];
            $target_dir = "uploads/";
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . basename($_FILES["proposal"]["name"]);
            
            if (move_uploaded_file($_FILES["proposal"]["tmp_name"], $target_file)) {
                $update_sql .= ", Proposal = ?";
                $params[] = $proposal;
            }
        }
        
        $update_sql .= " WHERE event_id = ? AND user_id = ?";
        $params[] = $event_id;
        $params[] = $user_id;
        
        $stmt = $pdo->prepare($update_sql);
        $stmt->execute($params);
        
        $pdo->commit();
        header("Location: view_event.php?id=" . $event_id . "&edit_success=1");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Terjadi kesalahan saat memperbarui event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white !important;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-calendar-alt me-2"></i>
                Event Management System
            </a>
            <a href="view_event.php?id=<?php echo $event_id; ?>" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Event Details
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit and Resubmit Event</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-heading me-1"></i>Event Title
                                </label>
                                <input type="text" class="form-control" name="judul_event" required
                                       value="<?php echo htmlspecialchars($event['judul_event']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1"></i>Event Type
                                </label>
                                <input type="text" class="form-control" name="jenis_kegiatan" required
                                       value="<?php echo htmlspecialchars($event['jenis_kegiatan']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill me-1"></i>Budget
                                </label>
                                <input type="text" class="form-control" name="total_pembiayaan" required
                                       value="<?php echo htmlspecialchars($event['Total_pembiayaan']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-file-pdf me-1"></i>Proposal (PDF)
                                </label>
                                <input type="file" class="form-control" name="proposal" accept=".pdf">
                                <small class="text-muted">Current file: <?php echo htmlspecialchars($event['Proposal']); ?></small>
                                <small class="text-muted d-block">Upload new file only if you want to change the current proposal</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description
                                </label>
                                <textarea class="form-control" name="deskripsi" rows="3" required><?php echo htmlspecialchars($event['deskripsi']); ?></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="submit_edit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Updated Proposal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 