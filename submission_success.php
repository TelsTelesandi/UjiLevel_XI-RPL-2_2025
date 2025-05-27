<?php
session_start();

// Include database connection file
// require_once 'db_connect.php'; // Database connection not strictly needed on success page unless fetching dynamic data

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// submission_success content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Berhasil - Event Eskul</title>
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
            padding: 40px; /* Increased padding */
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .success-container {
            background-color: white;
            padding: 50px; /* More padding */
            border-radius: 12px; /* More rounded corners */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); /* Softer and larger shadow */
            text-align: center;
            max-width: 550px; /* Increased max width */
            width: 90%;
            border: 1px solid #e0e0e0; /* Add subtle border */
        }
        .success-container h1 {
            color: #28a745; /* Green color for success */
            font-size: 2.8em; /* Larger font size */
            margin-bottom: 20px; /* Increased margin */
            font-weight: 700; /* Bold font */
        }
        .success-container p {
            color: #555;
            font-size: 1.2em; /* Slightly larger font */
            margin-bottom: 40px; /* Increased margin */
            line-height: 1.6;
        }
        .success-container a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px; /* Adjusted padding */
            border-radius: 6px; /* Slightly more rounded */
            text-decoration: none;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out; /* Add transform transition */
            font-size: 1.1em; /* Slightly larger font */
            font-weight: 500; /* Medium font weight */
        }
        .success-container a:hover {
            background-color: #0056b3;
            transform: translateY(-2px); /* Lift effect on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'submit_event.php' || basename($_SERVER['PHP_SELF']) == 'submission_success.php') ? 'active' : ''; ?>"><a href="submit_event.php">Ajukan Event</a></li>
            <li><a href="participate_event.php">Ikuti Event</a></li>
        </ul>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <div class="success-container">
            <h1>Pengajuan Berhasil!</h1>
            <p>Event Anda telah berhasil diajukan dan akan segera ditinjau oleh admin <strong>Rafli Adi</strong>.</p>
            <a href="dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html> 