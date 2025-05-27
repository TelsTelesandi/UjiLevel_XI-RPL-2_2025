<?php
session_start();

// Include database connection file
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Get the logged-in user's ID (might be needed for participation logic later)
$loggedInUserId = $_SESSION['user_id'];

// Fetch list of available events
$events = [];
// Select events that have been Approved
$sql_events = "SELECT ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.Total_pembiayaan, ve.status
               FROM event_pengajuan ep
               JOIN verifikasi_event ve ON ep.event_id = ve.event_id
               WHERE ve.status = 'Approved' 
               ORDER BY ep.event_id DESC";

$error = ''; // Initialize error variable

if ($result_events = mysqli_query($link, $sql_events)) {
    while ($row = mysqli_fetch_assoc($result_events)) {
        $events[] = $row;
    }
    mysqli_free_result($result_events);
} else {
    $error = "Error fetching events: " . mysqli_error($link);
}

// Close database connection
mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ikuti Event - Event Eskul</title>
    <style>
        body {
            font-family: 'Arial', sans-serif; /* Consistent font */
            margin: 0;
            /* Consistent background */
            background: linear-gradient(to bottom right, #e2e8f0, #cbd5e0);
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
            color: #ffffff; /* Consistent title color */
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
            margin-left: 250px; /* Consistent margin */
            padding: 20px;
            flex-grow: 1;
        }
        .main-content h1 {
             margin-top: 0;
             color: #333; /* Consistent color */
             margin-bottom: 20px;
        }
        .events-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Responsive grid */
            gap: 20px;
        }
        .event-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee; /* Subtle border */
            display: flex; /* Flexbox for internal layout */
            flex-direction: column;
            justify-content: space-between; /* Push button to bottom */
        }
        .event-card h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.4em;
            margin-bottom: 10px;
             border-bottom: 1px solid #eee; /* Separator */
             padding-bottom: 10px;
        }
        .event-card p {
            margin-bottom: 10px;
            color: #555;
            line-height: 1.5;
        }
        .event-card .meta {
             font-size: 0.9em;
             color: #777;
             margin-bottom: 10px;
        }
        .event-card .actions {
             margin-top: auto; /* Push actions to the bottom */
             padding-top: 15px; /* Space above actions */
             border-top: 1px solid #eee; /* Separator */
        }
         .action-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745; /* Green for 'Ikuti' */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1em;
            text-align: center;
        }
        .action-button:hover {
            background-color: #218838; /* Darker green */
        }
          .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
         }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <h1>Event yang Tersedia</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <div class="events-list">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h3><?php echo htmlspecialchars($event['judul_event']); ?></h3>
                        <p class="meta">Jenis: <?php echo htmlspecialchars($event['jenis_kegiatan']); ?></p>
                        <p class="meta">Pembiayaan: Rp <?php echo number_format($event['Total_pembiayaan'] ?? 0, 0, ',', '.'); ?></p>
                        <div class="actions">
                            <a href="register_event.php?event_id=<?php echo $event['event_id']; ?>" class="action-button">Ikuti Event Ini</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada event yang tersedia saat ini.</p>
            <?php endif; ?>
        </div>

    </div>
</body>
</html> 