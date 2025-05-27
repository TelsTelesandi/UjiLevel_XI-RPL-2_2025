<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config/db.php';
require '../vendor/autoload.php'; // Pastikan sudah install PhpSpreadsheet via composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Query untuk mengambil data event
$query = "SELECT e.*, u.nama_lengkap 
          FROM event_pengajuan e 
          JOIN users u ON e.user_id = u.user_id 
          ORDER BY e.event_id DESC";
$result = mysqli_query($conn, $query);

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Judul Event');
$sheet->setCellValue('C1', 'Ketua Ekskul');
$sheet->setCellValue('D1', 'Jenis Kegiatan');
$sheet->setCellValue('E1', 'Tanggal Pengajuan');
$sheet->setCellValue('F1', 'Total Pembiayaan');
$sheet->setCellValue('G1', 'Status');
$sheet->setCellValue('H1', 'Deskripsi');

// Style header
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E2E8F0']
    ]
];
$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

// Isi data
$row = 2;
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $data['judul_event']);
    $sheet->setCellValue('C' . $row, $data['nama_lengkap']);
    $sheet->setCellValue('D' . $row, $data['jenis_kegiatan']);
    $sheet->setCellValue('E' . $row, $data['tanggal_pengajuan']);
    $sheet->setCellValue('F' . $row, 'Rp ' . number_format($data['total_pembiayaan'], 0, ',', '.'));
    $sheet->setCellValue('G' . $row, ucfirst($data['status']));
    $sheet->setCellValue('H' . $row, $data['deskripsi']);
    $row++;
    $no++;
}

// Auto size kolom
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Event_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Output file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 