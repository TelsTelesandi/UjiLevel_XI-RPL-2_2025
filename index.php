<?php
include 'config.php';
include 'session.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .stats-card {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
        }
        .stats-icon {
            font-size: 2rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container dashboard-container">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p class="text-muted">Here's your dashboard overview</p>
            </div>
        </div>

        <div class="row">
            <!-- Example Dashboard Cards -->
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total Users</h6>
                                <h3 class="mt-2 mb-0">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM users");
                                    $row = $result->fetch_assoc();
                                    echo $row['count'];
                                    ?>
                                </h3>
                            </div>
                            <div class="stats-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <p>Welcome to your dashboard! This is where you'll see your recent activity and important information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
