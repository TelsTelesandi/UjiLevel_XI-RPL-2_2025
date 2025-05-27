<?php
session_start();
require_once 'config/db.php';
require_once 'vendor/autoload.php'; // Pastikan sudah install TCPDF via composer

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Function to export data to Excel
function exportToExcel($conn) {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="laporan_ekskul.xls"');
    header('Cache-Control: max-age=0');

    // Query to get data
    $query = "SELECT e.nama_ekskul, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai, 
                     e.lokasi, e.kuota, e.status, u.nama_lengkap as pembina
              FROM ekskul e
              LEFT JOIN users u ON e.pembina_id = u.user_id
              ORDER BY e.tanggal_mulai DESC";
    
    $result = mysqli_query($conn, $query);

    // Start Excel content
    echo '<table border="1">';
    
    // Add headers
    echo '<tr>
            <th>Nama Ekskul</th>
            <th>Deskripsi</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Lokasi</th>
            <th>Kuota</th>
            <th>Status</th>
            <th>Pembina</th>
          </tr>';

    // Add data rows
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['nama_ekskul'] . '</td>';
        echo '<td>' . $row['deskripsi'] . '</td>';
        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_mulai'])) . '</td>';
        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
        echo '<td>' . $row['lokasi'] . '</td>';
        echo '<td>' . $row['kuota'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . $row['pembina'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    exit();
}

// Function to export data to PDF
function exportToPDF($conn) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Ekskul');
    $pdf->SetTitle('Laporan Ekskul');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'Laporan Ekskul', 'Dicetak pada: ' . date('d/m/Y H:i:s'));

    // Set header and footer fonts
    $pdf->setHeaderFont(Array('helvetica', '', 12));
    $pdf->setFooterFont(Array('helvetica', '', 8));

    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 10);

    // Query to get data
    $query = "SELECT e.nama_ekskul, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai, 
                     e.lokasi, e.kuota, e.status, u.nama_lengkap as pembina
              FROM ekskul e
              LEFT JOIN users u ON e.pembina_id = u.user_id
              ORDER BY e.tanggal_mulai DESC";
    
    $result = mysqli_query($conn, $query);

    // Create the table
    $html = '<table border="1" cellpadding="4">
        <thead>
            <tr style="background-color: #f5f6fa;">
                <th>Nama Ekskul</th>
                <th>Deskripsi</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Lokasi</th>
                <th>Kuota</th>
                <th>Status</th>
                <th>Pembina</th>
            </tr>
        </thead>
        <tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
            <td>' . $row['nama_ekskul'] . '</td>
            <td>' . $row['deskripsi'] . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal_mulai'])) . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>
            <td>' . $row['lokasi'] . '</td>
            <td>' . $row['kuota'] . '</td>
            <td>' . $row['status'] . '</td>
            <td>' . $row['pembina'] . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    // Print the table
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('laporan_ekskul.pdf', 'D');
    exit();
}

// Handle export request
if (isset($_GET['export'])) {
    if ($_GET['export'] == 'excel') {
        exportToExcel($conn);
    } elseif ($_GET['export'] == 'pdf') {
        exportToPDF($conn);
    }
}

// Get data for display
$query = "SELECT e.*, u.nama_lengkap as pembina 
          FROM ekskul e 
          LEFT JOIN users u ON e.pembina_id = u.user_id 
          ORDER BY e.tanggal_mulai DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ekskul</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        .export-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .export-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1000;
            overflow: hidden;
        }
        .export-dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .dropdown-item:hover {
            background-color: #f5f6fa;
        }
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        .dropdown-item.excel {
            color: #27ae60;
        }
        .dropdown-item.pdf {
            color: #e74c3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f6fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f6fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="report-header">
            <h1>Laporan Ekskul</h1>
            <div class="export-dropdown">
                <button class="export-btn">
                    <i class="fas fa-download"></i> Export
                </button>
                <div class="dropdown-content">
                    <a href="?export=excel" class="dropdown-item excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="?export=pdf" class="dropdown-item pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Ekskul</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Lokasi</th>
                    <th>Kuota</th>
                    <th>Status</th>
                    <th>Pembina</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_ekskul']); ?></td>
                    <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                    <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                    <td><?php echo htmlspecialchars($row['kuota']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['pembina']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html> 