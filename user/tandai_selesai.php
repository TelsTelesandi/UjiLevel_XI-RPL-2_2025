<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get event ID and verify ownership
$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM event_pengajuan WHERE event_id = $event_id AND user_id = $user_id");
$event = mysqli_fetch_assoc($query);

// If event doesn't exist or doesn't belong to user, redirect
if (!$event) {
    header("Location: dashboard_user.php");
    exit();
}

// Handle form submission
if (isset($_POST['complete'])) {
    $completion_notes = mysqli_real_escape_string($conn, $_POST['completion_notes']);
    
    mysqli_query($conn, "UPDATE event_pengajuan 
                        SET status = 'completed', 
                            completion_notes = '$completion_notes',
                            completion_date = NOW() 
                        WHERE event_id = $event_id");
                        
    header("Location: dashboard_user.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mark Event Complete - User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modern Navigation */
        .nav-modern {
            background: linear-gradient(90deg, #2c3e50 0%, #3498db 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .nav-brand {
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-modern .nav-list {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-modern .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-modern .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Completion Form */
        .completion-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .completion-header {
            background: linear-gradient(90deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 1.5rem;
        }

        .completion-body {
            padding: 2rem;
        }

        .event-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #6c757d;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-complete {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }

        .btn-back {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .nav-modern .nav-list {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .completion-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Navigation -->
        <nav class="nav-modern">
            <div class="container">
                <ul class="nav-list">
                    <li><a href="#" class="nav-brand">User Dashboard</a></li>
                    <li><a href="dashboard_user.php" class="nav-link">Dashboard</a></li>
                    <li><a href="../logout.php" class="nav-link">Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container">
            <div class="completion-card">
                <div class="completion-header">
                    <h2>Mark Event as Complete</h2>
                </div>
                <div class="completion-body">
                    <!-- Event Information -->
                    <div class="event-info">
                        <div class="info-group">
                            <div class="info-label">Event Title</div>
                            <div class="info-value"><?= htmlspecialchars($event['judul_event']) ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Status</div>
                            <div class="info-value"><?= htmlspecialchars(ucfirst($event['status'])) ?></div>
                        </div>
                    </div>

                    <!-- Completion Form -->
                    <form method="POST">
                        <div class="form-group">
                            <label>Completion Notes</label>
                            <textarea name="completion_notes" class="form-control" rows="4" placeholder="Enter details about the event completion" required></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="submit" name="complete" class="btn btn-complete">Mark as Complete</button>
                            <a href="dashboard_user.php" class="btn btn-back">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>