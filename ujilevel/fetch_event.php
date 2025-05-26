<?php
session_start();
require 'database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if event ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No event ID provided']);
    exit();
}

// Fetch event details
$event_id = intval($_GET['id']);
$event_query = mysqli_query($conn, "SELECT e.*, u.nama_lengkap, u.ekskul 
                                   FROM event_pengajuan e 
                                   JOIN users u ON e.user_id = u.user_id 
                                   WHERE e.event_id = $event_id");

if (!$event_query) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching event: ' . mysqli_error($conn)]);
    exit();
}

$event = mysqli_fetch_assoc($event_query);
if (!$event) {
    http_response_code(404);
    echo json_encode(['error' => 'Event not found']);
    exit();
}

// Fetch previous verification if exists
$verification_query = mysqli_query($conn, "SELECT * FROM verifikasi_event 
                                         WHERE event_id = $event_id 
                                         ORDER BY verifikasi_id DESC LIMIT 1");
$verification = mysqli_fetch_assoc($verification_query);

// Prepare response
$response = [
    'event_id' => $event['event_id'],
    'judul_event' => htmlspecialchars($event['judul_event']),
    'ekskul' => htmlspecialchars($event['ekskul']),
    'jenis_kegiatan' => htmlspecialchars($event['jenis_kegiatan']),
    'total_pembiayaan' => number_format($event['total_pembiayaan'], 0, ',', '.'),
    'deskripsi' => htmlspecialchars($event['deskripsi']),
    'proposal' => $event['proposal'],
    'status' => $event['status'],
    'catatan_admin' => isset($verification['catatan_admin']) ? htmlspecialchars($verification['catatan_admin']) : ''
];

echo json_encode($response);
exit();