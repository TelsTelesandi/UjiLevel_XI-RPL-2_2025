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

// Initialize variables
$success_msg = '';
$error_msg = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = str_replace(['Rp ', '.', ','], '', $_POST['Total_pembiayaan']); // Remove 'Rp ', '.', and ','
    $total_pembiayaan = intval($total_pembiayaan); // Convert to integer

    // File upload handling
    $upload_dir = 'uploads/event_documents/'; // Define upload directory
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $proposal_path = '';
    $dokumen_pendukung_path = '';

    // Handle proposal upload
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == UPLOAD_ERR_OK) {
        $proposal_name = uniqid() . '_' . basename($_FILES['proposal']['name']);
        $proposal_target = $upload_dir . $proposal_name;
        if (move_uploaded_file($_FILES['proposal']['tmp_name'], $proposal_target)) {
            $proposal_path = $proposal_target;
        } else {
            $error_msg .= "Gagal mengunggah proposal.<br>";
        }
    }

    // Handle dokumen pendukung upload
    if (isset($_FILES['dokumen_pendukung']) && $_FILES['dokumen_pendukung']['error'] == UPLOAD_ERR_OK) {
        $dokumen_pendukung_name = uniqid() . '_' . basename($_FILES['dokumen_pendukung']['name']);
        $dokumen_pendukung_target = $upload_dir . $dokumen_pendukung_name;
        if (move_uploaded_file($_FILES['dokumen_pendukung']['tmp_name'], $dokumen_pendukung_target)) {
            $dokumen_pendukung_path = $dokumen_pendukung_target;
        } else {
            $error_msg .= "Gagal mengunggah dokumen pendukung.<br>";
        }
    }

    // Check if files were uploaded successfully before inserting into database
    if (empty($error_msg)) {
        // Prepare an insert statement
        $sql = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, dokumen_pendukung) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ississ", $loggedInUserId, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_path, $dokumen_pendukung_path);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to success page
                header("location: submission_success.php");
                exit(); // Stop further script execution
            } else {
                $error_msg .= "Terjadi kesalahan saat menyimpan data ke database: " . mysqli_error($link) . "<br>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
             $error_msg .= "Terjadi kesalahan saat menyiapkan query: " . mysqli_error($link) . "<br>";
        }
    } else {
         $error_msg .= "Terjadi kesalahan saat mengunggah file.";
    }
} else {
    // This block will be executed if the form is not submitted via POST
}

// Close database connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event - Event Eskul</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom right, #e2e8f0, #cbd5e0);
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41;
            color: #b8c7ce;
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
            padding: 20px;
            flex-grow: 1;
        }
        .main-content h1 {
            margin-top: 0;
            color: #333;
            margin-bottom: 20px;
        }
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background-color: #e9f7ef; /* Softer green background */
            color: #237d3c; /* Darker green text */
            border: 1px solid #d0e9c6; /* Consistent border */
            padding: 15px;
            margin-bottom: 25px; /* Increased margin bottom */
            border-radius: 8px; /* More rounded corners */
            text-align: center;
            font-size: 1.1em; /* Slightly larger font */
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
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
            <li class="active"><a href="submit_event.php">Ajukan Event</a></li>
            <li><a href="participate_event.php">Ikuti Event</a></li>
        </ul>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Ajukan Event Baru</h1>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul_event">Judul Event:</label>
                    <input type="text" id="judul_event" name="judul_event" required>
                </div>

                <div class="form-group">
                    <label for="jenis_kegiatan">Jenis Kegiatan:</label>
                    <select id="jenis_kegiatan" name="jenis_kegiatan" required>
                        <option value="">Pilih Jenis Kegiatan</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Pelatihan">Pelatihan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Total_pembiayaan">Total Pembiayaan (Rp):</label>
                    <input type="number" id="Total_pembiayaan" name="Total_pembiayaan" required>
                </div>

                <div class="form-group">
                    <label for="proposal">Proposal:</label>
                    <input type="file" id="proposal" name="proposal" required>
                </div>

                <div class="form-group">
                    <label for="dokumen_pendukung">Dokumen Pendukung:</label>
                    <input type="file" id="dokumen_pendukung" name="dokumen_pendukung" required>
                </div>

                <button type="submit" class="submit-button">Ajukan Event</button>
            </form>
        </div>
    </div>
</body>
</html>
