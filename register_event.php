<?php
session_start();

// Include database connection file
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['user_id'];

$message = '';
$message_class = '';

// Check if event_id is provided in the URL
if (isset($_GET['event_id']) && !empty(trim($_GET['event_id']))) {
    $eventId = intval($_GET['event_id']); // Sanitize and get integer value

    // Ensure eventId is a positive integer
    if ($eventId > 0) {
        // Check if the user is already registered for this event
        $sql_check = "SELECT COUNT(*) FROM event_participants WHERE event_id = ? AND user_id = ?";
        if ($stmt_check = mysqli_prepare($link, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "ii", $eventId, $loggedInUserId);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_bind_result($stmt_check, $count);
            mysqli_stmt_fetch($stmt_check);
            mysqli_stmt_close($stmt_check);

            if ($count > 0) {
                $message = "Anda sudah terdaftar untuk event ini.";
                $message_class = 'alert-warning'; // Or alert-info
            } else {
                // Insert participation record
                // !! PASTIKAN TABEL `event_participants` SUDAH ADA DENGAN KOLOM `event_id` DAN `user_id` !!
                $sql_insert = "INSERT INTO event_participants (event_id, user_id) VALUES (?, ?)";
                if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt_insert, "ii", $eventId, $loggedInUserId);
                    if (mysqli_stmt_execute($stmt_insert)) {
                        $message = "Berhasil mendaftar untuk event!";
                        $message_class = 'alert-success';
                    } else {
                        $message = "Gagal mendaftar untuk event: " . mysqli_error($link);
                        $message_class = 'alert-danger';
                    }
                    mysqli_stmt_close($stmt_insert);
                } else {
                    $message = "Error preparing insert statement: " . mysqli_error($link);
                    $message_class = 'alert-danger';
                }
            }
        } else {
            $message = "Error preparing check statement: " . mysqli_error($link);
            $message_class = 'alert-danger';
        }
    } else {
        $message = "ID Event tidak valid.";
        $message_class = 'alert-danger';
    }
} else {
    $message = "ID Event tidak ditemukan.";
    $message_class = 'alert-danger';
}

// Close database connection
mysqli_close($link);

// register_event content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom right, #e2e8f0, #cbd5e0); /* Consistent background */
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41; /* Consistent sidebar color */
            color: #b8c7ce; /* Consistent text color */
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 1.8em;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }
        .sidebar ul li {
            padding: 12px 20px;
            border-bottom: 1px solid #3a4b54;
        }
        .sidebar ul li a {
            color: #b8c7ce;
            text-decoration: none;
            display: block;
            font-size: 1.1em;
        }
        .sidebar ul li a:hover {
            background-color: #3a4b54;
            color: #ffffff;
        }
        .sidebar ul li.active a {
            background-color: #007bff;
            color: white;
        }
        .logout-link {
            display: block;
            padding: 15px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 1.1em;
            margin-top: auto;
        }
        .logout-link:hover {
            background-color: #c82333;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px; /* Increased padding */
            flex-grow: 1;
        }
         .message-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center; /* Center message */
        }
         .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d0e9c6;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        /* Add basic styling for the main content area */
        .content-area {
             background-color: white;
             padding: 20px;
             border-radius: 8px;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="submit_event.php">Ajukan Event</a></li>
            <li class="active"><a href="participate_event.php">Ikuti Event</a></li>
        </ul>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Status Pendaftaran Event</h1>

        <div class="message-container">
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- You could add more content here, e.g., details of the event -->
        <div class="content-area">
            <p>Kembali ke halaman <a href="participate_event.php">Ikuti Event</a>.</p>
        </div>

    </div>
</body>
</html> 