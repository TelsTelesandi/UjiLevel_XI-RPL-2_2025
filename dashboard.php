<?php
session_start();

// Include database connection file
require_once 'db_connect.php';

// Check if the user is logged in and has the correct role (assuming 'user' role for this dashboard)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true /* || $_SESSION['role'] !== 'user' */ ) { // Role check might need adjustment based on your roles
    header("location: login.php");
    exit;
}

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['user_id'];

// --- Fetch User's Pengajuan Data ---
$user_pengajuan = [];
// Select pengajuan data for the current user and join with verifikasi_event for status
$sql_user_pengajuan = "SELECT ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.Total_pembiayaan, ve.status 
                      FROM event_pengajuan ep 
                      LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id 
                      WHERE ep.user_id = ? 
                      ORDER BY ep.event_id DESC";

$error = ''; // Initialize error variable

if ($stmt_user_pengajuan = mysqli_prepare($link, $sql_user_pengajuan)) {
     mysqli_stmt_bind_param($stmt_user_pengajuan, "i", $loggedInUserId);
     if (mysqli_stmt_execute($stmt_user_pengajuan)) {
        $result_user_pengajuan = mysqli_stmt_get_result($stmt_user_pengajuan);
        while ($row = mysqli_fetch_assoc($result_user_pengajuan)) {
            $user_pengajuan[] = $row;
        }
        mysqli_free_result($result_user_pengajuan);
     } else {
        // Handle error
        $error = "Error fetching your submissions: " . mysqli_error($link);
     }
     mysqli_stmt_close($stmt_user_pengajuan);
} else {
     $error = "Error preparing statement: " . mysqli_error($link);
}

// Close database connection
mysqli_close($link);

// User dashboard content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Event Eskul</title>
    <style>
        body {
            font-family: 'Arial', sans-serif; /* Consistent font */
            margin: 0;
            background-color: #f4f7f6; /* Consistent background */
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
            padding: 12px 20px; /* Consistent padding */
            border-bottom: 1px solid #3a4b54; /* Consistent border */
        }
        .sidebar ul li a {
            color: #b8c7ce; /* Consistent text color */
            text-decoration: none;
            display: block;
            font-size: 1.1em;
        }
        .sidebar ul li a:hover {
            background-color: #3a4b54; /* Consistent hover background */
            color: #ffffff; /* Consistent hover text */
        }
         .sidebar ul li.active a {
            background-color: #007bff; /* Consistent highlight */
            color: white;
        }
         .logout-link {
            display: block;
            padding: 15px 20px; /* Consistent padding */
            background-color: #dc3545; /* Consistent red color */
            color: white;
            text-decoration: none;
            text-align: center;
             font-size: 1.1em;
            margin-top: auto;
        }
         .logout-link:hover {
            background-color: #c82333; /* Consistent darker red */
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
        /* Styles for user-specific content sections */
        .user-actions {
             margin-bottom: 20px;
        }
         .action-button {
            display: inline-block;
            margin-right: 10px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
             font-size: 1em;
        }
        .action-button-primary { /* Style for Submit Event */
             background-color: #007bff; /* Blue */
        }
         .action-button-primary:hover {
            background-color: #0056b3; /* Darker blue */
         }
         .action-button-secondary { /* Style for Participate Event */
            background-color: #17a2b8; /* Cyan/Teal */
         }
         .action-button-secondary:hover {
             background-color: #138496; /* Darker Cyan/Teal */
         }
        
        /* Table styles (copied from admin dashboard for consistency) */
        .user-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .user-section th,
        .user-section td {
            border: 1px solid #ddd; /* Lighter border */
            padding: 12px 10px; /* Increased padding */
            text-align: left;
             vertical-align: middle;
        }
        .user-section th {
            background-color: #f8f9fa; /* Lighter header background */
            font-weight: bold;
            color: #555;
        }
         .user-section tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Zebra striping */
        }
         .status-tag {
            padding: 5px 10px; /* More padding */
            border-radius: 4px; /* Slightly more rounded */
            font-size: 0.9em;
            color: white;
            display: inline-block;
            text-align: center;
        }
        .status-Pending {
            background-color: #ffc107; /* Yellow */
        }
        .status-Approved {
            background-color: #28a745; /* Green */
        }
        .status-Rejected {
            background-color: #dc3545; /* Red */
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Panel</h2>
        <ul>
            <li class="active"><a href="dashboard.php">Dashboard</a></li>
            <li><a href="submit_event.php">Ajukan Event</a></li>
            <li><a href="participate_event.php">Ikuti Event</a></li>
        </ul>
         <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Dashboard Pengguna</h1>
x
        <?php if (!empty($error)): ?>
            <div class="user-section" style="color: red;">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <div class="user-section">
             <h3>Opsi Kegiatan</h3>
             <div class="user-actions">
                 <a href="submit_event.php" class="action-button action-button-primary">Ajukan Event Baru</a>
                 <a href="participate_event.php" class="action-button action-button-secondary">Ikuti Event</a>
             </div>
        </div>

         <div class="user-section bg-white p-8 rounded-lg shadow-xl max-w-xl mx-auto">
             <h3>Pengajuan Kegiatan Anda</h3>
             <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pembiayaan</th>
                        <th>Status</th>
                        <!-- Add more columns if needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($user_pengajuan)): ?>
                        <?php foreach ($user_pengajuan as $pengajuan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($pengajuan['Total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($pengajuan['status'] ?? 'Belum Diverifikasi'); // Default status
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <!-- Add more columns if needed -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Anda belum mengajukan kegiatan apapun.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
         </div>

         <?php
        // Add other user-specific dashboard content here (e.g., upcoming events they registered for)
        ?>

    </div>
</body>
</html>
