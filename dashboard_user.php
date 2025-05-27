<?php
// Include shared data
require_once 'data.php';

// Simulasi session - dalam implementasi nyata, ini akan diambil dari session login
$currentUserId = 2; // ID user Jamal Ikhsan

// Sample data for user
$currentUser = [
    'user_id' => 2,
    'username' => 'Jamal',
    'password' => '1',
    'nama_lengkap' => 'Jamal Ikhsan',
    'role' => 'user',
    'ekskul' => 'Futsal',
];

// Filter data for current user
$userPengajuanData = array_filter($pengajuanData, function($item) use ($currentUserId) {
    return $item['user_id'] == $currentUserId;
}, ARRAY_FILTER_USE_BOTH);

// Calculate statistics
$stats = [
    'total' => count($userPengajuanData),
    'pending' => count(array_filter($userPengajuanData, function($item) { return $item['status'] === 'menunggu'; })),
    'approved' => count(array_filter($userPengajuanData, function($item) { 
        return $item['status'] === 'disetujui' && empty($item['tanggal_closed']); 
    })),
    'closed' => count(array_filter($userPengajuanData, function($item) { 
        return $item['status'] === 'disetujui' && !empty($item['tanggal_closed']); 
    })),
    'rejected' => count(array_filter($userPengajuanData, function($item) { return $item['status'] === 'ditolak'; }))
];

// Handle form submission for new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_event') {
        // Validate and sanitize inputs
        $judulEvent = htmlspecialchars($_POST['judul_event']);
        $jenisKegiatan = htmlspecialchars($_POST['jenis_kegiatan']);
        $totalPembiayaan = (int)$_POST['total_pembiayaan'];
        $proposal = htmlspecialchars($_POST['proposal_file_name']);
        $deskripsi = htmlspecialchars($_POST['deskripsi']);

        // Simulate adding a new event
        $newEvent = [
            'event_id' => count($pengajuanData) + 1,
            'user_id' => $currentUserId,
            'judul_event' => $judulEvent,
            'jenis_kegiatan' => $jenisKegiatan,
            'total_pembiayaan' => $totalPembiayaan,
            'proposal' => $proposal,
            'deskripsi' => $deskripsi,
            'tanggal_pengajuan' => date('Y-m-d'),
            'status' => 'menunggu',
            'catatan_admin' => '',
            'tanggal_diproses' => '',
            'tanggal_closed' => ''
        ];
        
        $pengajuanData[] = $newEvent;
        // Save to file
        savePengajuanData($pengajuanData);
        
        $response = [
            'success' => true,
            'message' => 'Event berhasil diajukan!'
        ];

        // Redirect to avoid form resubmission
        header('Location: dashboard_user.php');
        exit;
    }
    elseif ($_POST['action'] === 'close_event' && isset($_POST['event_id'])) {
        // Simulate closing an event
        foreach ($pengajuanData as &$item) {
            if ($item['event_id'] == $_POST['event_id']) {
                $item['tanggal_closed'] = date('Y-m-d');
                if (!empty($_POST['close_notes'])) {
                    $item['catatan_admin'] .= ' | Catatan penutupan: ' . htmlspecialchars($_POST['close_notes']);
                }
                break;
            }
        }
        
        // Save updated data
        savePengajuanData($pengajuanData);
        
        $response = [
            'success' => true,
            'message' => 'Status event berhasil diubah menjadi closed!'
        ];

        // Redirect to avoid form resubmission
        header('Location: dashboard_user.php');
        exit;
    }
}

// PDF Generation Function
function generateEventsPDF($data, $currentUser) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Pengajuan Kegiatan</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.4; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #059669; padding-bottom: 15px; }
            .header h1 { color: #059669; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
            .header p { color: #666; margin: 5px 0; font-size: 14px; }
            .info { margin-bottom: 25px; background-color: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #059669; }
            .info-item { margin: 8px 0; display: flex; justify-content: space-between; }
            .info-label { font-weight: bold; color: #374151; }
            .info-value { color: #059669; font-weight: 500; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            th, td { border: 1px solid #e5e7eb; padding: 12px 8px; text-align: left; font-size: 11px; }
            th { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; font-weight: bold; text-align: center; }
            tr:nth-child(even) { background-color: #f9fafb; }
            tr:hover { background-color: #f3f4f6; }
            .status-menunggu { background-color: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 500; text-align: center; display: inline-block; min-width: 70px; }
            .status-disetujui { background-color: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-weight: 500; text-align: center; display: inline-block; min-width: 70px; }
            .status-ditolak { background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 500; text-align: center; display: inline-block; min-width: 70px; }
            .status-closed { background-color: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-weight: 500; text-align: center; display: inline-block; min-width: 70px; }
            .footer { margin-top: 40px; text-align: center; color: #6b7280; font-size: 12px; border-top: 2px solid #e5e7eb; padding-top: 20px; }
            .summary { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #cbd5e1; }
            .summary h3 { color: #059669; margin-bottom: 15px; font-size: 16px; text-align: center; }
            .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
            .summary-item { background-color: white; padding: 12px; border-radius: 6px; border-left: 4px solid #059669; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .summary-label { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
            .summary-value { font-size: 18px; font-weight: bold; color: #059669; }
            .currency { color: #059669; font-weight: 600; }
            .no-data { text-align: center; color: #6b7280; font-style: italic; padding: 40px; }
            .user-info-box { background-color: #e0f2fe; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0284c7; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Laporan Pengajuan Kegiatan</h1>
            <p>Sistem Manajemen Ekstrakurikuler</p>
            <p>Tanggal Cetak: ' . date('d F Y H:i:s') . '</p>
        </div>
        
        <div class="user-info-box">
            <h4 style="margin: 0 0 10px 0; color: #0284c7;">Informasi Pengaju</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div><strong>Nama:</strong> ' . htmlspecialchars($currentUser['nama_lengkap']) . '</div>
                <div><strong>Username:</strong> ' . htmlspecialchars($currentUser['username']) . '</div>
                <div><strong>Ekstrakurikuler:</strong> ' . htmlspecialchars($currentUser['ekskul']) . '</div>
                <div><strong>Role:</strong> ' . htmlspecialchars($currentUser['role']) . '</div>
            </div>
        </div>
        
        <div class="info">
            <div class="info-item">
                <span class="info-label">Jenis Laporan:</span>
                <span class="info-value">Pengajuan Kegiatan Saya</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Data:</span>
                <span class="info-value">' . count($data) . ' pengajuan</span>
            </div>
            <div class="info-item">
                <span class="info-label">Periode:</span>
                <span class="info-value">Semua periode</span>
            </div>
        </div>';
    
    if (count($data) > 0) {
        $pending = count(array_filter($data, function($item) { return $item['status'] === 'menunggu'; }));
        $approved = count(array_filter($data, function($item) { return $item['status'] === 'disetujui' && empty($item['tanggal_closed']); }));
        $closed = count(array_filter($data, function($item) { return $item['status'] === 'disetujui' && !empty($item['tanggal_closed']); }));
        $rejected = count(array_filter($data, function($item) { return $item['status'] === 'ditolak'; }));
        $totalBudget = array_sum(array_column($data, 'total_pembiayaan'));
        
        $html .= '
        <div class="summary">
            <h3>📊 RINGKASAN STATISTIK</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Menunggu Persetujuan</div>
                    <div class="summary-value">' . $pending . '</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Disetujui (Aktif)</div>
                    <div class="summary-value">' . $approved . '</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Closed (Selesai)</div>
                    <div class="summary-value">' . $closed . '</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Ditolak</div>
                    <div class="summary-value">' . $rejected . '</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Anggaran</div>
                    <div class="summary-value currency">Rp ' . number_format($totalBudget, 0, ',', '.') . '</div>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 8%;">Event ID</th>
                    <th style="width: 20%;">Judul Event</th>
                    <th style="width: 12%;">Jenis Kegiatan</th>
                    <th style="width: 12%;">Total Pembiayaan</th>
                    <th style="width: 12%;">Proposal</th>
                    <th style="width: 10%;">Tanggal Pengajuan</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 15%;">Catatan Admin</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        foreach ($data as $item) {
            $statusClass = '';
            $statusText = '';
            
            if (!empty($item['tanggal_closed']) && $item['status'] === 'disetujui') {
                $statusClass = 'status-closed';
                $statusText = 'Closed';
            } else {
                switch($item['status']) {
                    case 'menunggu': 
                        $statusClass = 'status-menunggu';
                        $statusText = 'Menunggu'; 
                        break;
                    case 'disetujui': 
                        $statusClass = 'status-disetujui';
                        $statusText = 'Disetujui'; 
                        break;
                    case 'ditolak': 
                        $statusClass = 'status-ditolak';
                        $statusText = 'Ditolak'; 
                        break;
                }
            }
            
            $html .= '
                    <tr>
                        <td style="text-align: center; font-weight: bold;">' . $no++ . '</td>
                        <td style="text-align: center;">' . $item['event_id'] . '</td>
                        <td><strong>' . htmlspecialchars($item['judul_event']) . '</strong></td>
                        <td>' . htmlspecialchars($item['jenis_kegiatan']) . '</td>
                        <td style="text-align: right;" class="currency">Rp ' . number_format($item['total_pembiayaan'], 0, ',', '.') . '</td>
                        <td>' . htmlspecialchars($item['proposal']) . '</td>
                        <td style="text-align: center;">' . date('d/m/Y', strtotime($item['tanggal_pengajuan'])) . '</td>
                        <td style="text-align: center;"><span class="' . $statusClass . '">' . $statusText . '</span></td>
                        <td>' . htmlspecialchars($item['catatan_admin']) . '</td>
                    </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
    } else {
        $html .= '<div class="no-data">📋 Tidak ada data pengajuan untuk ditampilkan</div>';
    }
    
    $html .= '
        <div class="footer">
            <p><strong>© 2025 Sistem Manajemen Ekstrakurikuler</strong></p>
            <p>Laporan ini digenerate secara otomatis pada ' . date('d F Y H:i:s') . '</p>
            <p>Data yang ditampilkan adalah data real-time dari sistem</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

// Handle PDF Export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $html = generateEventsPDF($userPengajuanData, $currentUser);
    $filename = 'Laporan_Pengajuan_Kegiatan_' . $currentUser['nama_lengkap'] . '_' . date('Y-m-d_H-i-s') . '.html';
    
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $html;
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Manajemen Ekstrakurikuler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f0f4f8; color: #1e293b; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; }
        .header-title h1 { font-size: 28px; color: #059669; margin-bottom: 5px; }
        .header-title p { color: #64748b; font-size: 16px; }
        .user-info { display: flex; align-items: center; gap: 15px; background-color: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .user-avatar { width: 50px; height: 50px; border-radius: 50%; background-color: #059669; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; font-weight: bold; }
        .user-details h3 { color: #1e293b; margin-bottom: 5px; }
        .user-details p { color: #64748b; font-size: 14px; }
        .stats-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .stat-title { font-size: 16px; font-weight: 500; color: #475569; }
        .stat-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
        .icon-total { background-color: #0ea5e9; }
        .icon-pending { background-color: #f59e0b; }
        .icon-approved { background-color: #10b981; }
        .icon-closed { background-color: #6366f1; }
        .icon-rejected { background-color: #ef4444; }
        .stat-value { font-size: 32px; font-weight: bold; margin: 10px 0; color: #1e293b; }
        .stat-description { font-size: 14px; color: #64748b; }
        .action-buttons { display: flex; gap: 15px; margin-bottom: 30px; }
        .btn { padding: 12px 20px; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 10px; border: none; transition: all 0.2s; }
        .btn-primary { background-color: #059669; color: white; }
        .btn-primary:hover { background-color: #047857; }
        .btn-secondary { background-color: #e2e8f0; color: #475569; }
        .btn-secondary:hover { background-color: #cbd5e1; }
        .btn-export { background-color: #dc2626; color: white; }
        .btn-export:hover { background-color: #b91c1c; }
        .btn-logout { background-color: #6b7280; color: white; }
        .btn-logout:hover { background-color: #4b5563; }
        .section-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #059669; display: flex; align-items: center; gap: 10px; }
        .card { background-color: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 30px; overflow: hidden; }
        .card-header { padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 18px; font-weight: 600; color: #1e293b; }
        .card-body { padding: 20px; }
        .table-container { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background-color: #f8fafc; padding: 12px 15px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover { background-color: #f8fafc; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; text-align: center; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-approved { background-color: #d1fae5; color: #065f46; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-closed { background-color: #e0f2fe; color: #0369a1; }
        .action-btn { padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; border: none; transition: all 0.2s; }
        .action-btn-view { background-color: #e0f2fe; color: #0369a1; }
        .action-btn-view:hover { background-color: #bae6fd; }
        .action-btn-close { background-color: #e0e7ff; color: #4338ca; }
        .action-btn-close:hover { background-color: #c7d2fe; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; overflow: auto; padding: 20px; }
        .modal-content { background-color: white; margin: 50px auto; padding: 20px; border-radius: 8px; max-width: 600px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: relative; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .modal-title { font-size: 18px; font-weight: bold; color: #1e293b; }
        .close-button { background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-size: 14px; font-weight: 500; color: #374151; }
        .form-input { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.2s; }
        .form-input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1); }
        .form-select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background-color: white; transition: border-color 0.2s; }
        .form-select:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1); }
        .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; min-height: 100px; resize: vertical; transition: border-color 0.2s; }
        .form-textarea:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1); }
        .form-file { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background-color: white; transition: border-color 0.2s; }
        .form-file:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1); }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .file-info { font-size: 12px; color: #64748b; margin-top: 5px; }
        .toast { position: fixed; top: 20px; right: 20px; background-color: #10b981; color: white; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 2000; display: flex; align-items: center; gap: 10px; opacity: 0; transform: translateY(-20px); transition: opacity 0.3s, transform 0.3s; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast i { font-size: 18px; }
        .event-detail-modal .modal-content { max-width: 800px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .detail-item { margin-bottom: 15px; }
        .detail-label { font-weight: 600; color: #374151; margin-bottom: 5px; }
        .detail-value { color: #6b7280; padding: 8px 12px; background-color: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; }
        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state-icon { font-size: 64px; color: #d1d5db; margin-bottom: 20px; }
        .empty-state-title { font-size: 20px; font-weight: 600; color: #6b7280; margin-bottom: 10px; }
        .empty-state-description { color: #9ca3af; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; border-top: 1px solid #e2e8f0; margin-top: 40px; }
        @media (max-width: 768px) {
            .stats-container { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Berhasil!</span>
    </div>

    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Dashboard User</h1>
                <p>Sistem Manajemen Ekstrakurikuler</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">J</div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($currentUser['nama_lengkap']); ?></h3>
                    <p>Ekstrakurikuler: <?php echo htmlspecialchars($currentUser['ekskul']); ?> | Status: User Aktif</p>
                </div>
                <a href="login.php?logout=1" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Pengajuan</div>
                    <div class="stat-icon icon-total"><i class="fas fa-file-alt"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-description">Semua pengajuan yang dibuat</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Menunggu</div>
                    <div class="stat-icon icon-pending"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['pending']; ?></div>
                <div class="stat-description">Sedang diproses admin</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Disetujui</div>
                    <div class="stat-icon icon-approved"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['approved']; ?></div>
                <div class="stat-description">Pengajuan yang aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Closed</div>
                    <div class="stat-icon icon-closed"><i class="fas fa-check-double"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['closed']; ?></div>
                <div class="stat-description">Pengajuan yang selesai</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Ditolak</div>
                    <div class="stat-icon icon-rejected"><i class="fas fa-times-circle"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['rejected']; ?></div>
                <div class="stat-description">Perlu perbaikan</div>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" id="openEventModalBtn">
                <i class="fas fa-plus"></i> Request Event Baru
            </button>
            <a href="?export=pdf" class="btn btn-export" target="_blank">
                <i class="fas fa-file-pdf"></i> Export Laporan PDF
            </a>
        </div>

        <h2 class="section-title"><i class="fas fa-list"></i> Daftar Pengajuan Event</h2>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Pengajuan Event Saya</div>
            </div>
            <div class="card-body">
                <?php if (count($userPengajuanData) > 0): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul Event</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Total Pembiayaan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userPengajuanData as $event): ?>
                                    <tr>
                                        <td><?php echo $event['event_id']; ?></td>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                        <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                        <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                        <td><?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($event['tanggal_closed']) && $event['status'] === 'disetujui'): ?>
                                                <span class="badge badge-closed">Closed</span>
                                            <?php else: 
                                                switch($event['status']):
                                                    case 'menunggu': ?>
                                                        <span class="badge badge-pending">Menunggu</span>
                                                        <?php break;
                                                    case 'disetujui': ?>
                                                        <span class="badge badge-approved">Disetujui</span>
                                                        <?php break;
                                                    case 'ditolak': ?>
                                                        <span class="badge badge-rejected">Ditolak</span>
                                                        <?php break;
                                                endswitch;
                                            endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
                                                <button class="action-btn action-btn-view" onclick="viewEventDetail(<?php echo $event['event_id']; ?>)">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                                <?php if ($event['status'] === 'disetujui' && empty($event['tanggal_closed'])): ?>
                                                    <button class="action-btn action-btn-close" onclick="closeEvent(<?php echo $event['event_id']; ?>)">
                                                        <i class="fas fa-check-double"></i> Close
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-file-alt"></i></div>
                        <h3 class="empty-state-title">Belum Ada Pengajuan</h3>
                        <p class="empty-state-description">Anda belum membuat pengajuan kegiatan apapun. Mulai ajukan kegiatan ekstrakurikuler Anda!</p>
                        <button class="btn btn-primary" id="openEventModalBtn2">
                            <i class="fas fa-plus"></i> Buat Pengajuan Pertama
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 Sistem Manajemen Ekstrakurikuler. All rights reserved.</p>
        </div>
    </div>

    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Request Event Baru</h3>
                <button class="close-button" id="closeEventModalBtn">×</button>
            </div>
            <form id="addEventForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_event">
                <div class="form-group">
                    <label for="judul_event" class="form-label">Judul Event</label>
                    <input type="text" id="judul_event" name="judul_event" class="form-input" required placeholder="Masukkan judul event">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan</label>
                        <select id="jenis_kegiatan" name="jenis_kegiatan" class="form-select" required>
                            <option value="">Pilih Jenis Kegiatan</option>
                            <?php foreach ($jenisKegiatanOptions as $jenis): ?>
                                <option value="<?php echo $jenis; ?>"><?php echo $jenis; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_pembiayaan" class="form-label">Total Pembiayaan</label>
                        <input type="number" id="total_pembiayaan" name="total_pembiayaan" class="form-input" required placeholder="0" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="proposal_file" class="form-label">Upload Proposal</label>
                    <input type="file" id="proposal_file" name="proposal_file" class="form-file" accept=".pdf,.doc,.docx" required>
                    <input type="hidden" name="proposal_file_name" id="proposal_file_name">
                    <div class="file-info">Format yang diizinkan: PDF, DOC, DOCX (Maksimal 5MB)</div>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-textarea" required placeholder="Jelaskan detail kegiatan yang akan dilaksanakan"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelEventBtn">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveEventBtn">Ajukan Event</button>
                </div>
            </form>
        </div>
    </div>

    <div id="eventDetailModal" class="modal event-detail-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Pengajuan Event</h3>
                <button class="close-button" id="closeDetailModalBtn">×</button>
            </div>
            <div id="eventDetailContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <div id="closeEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tutup Pengajuan Event</h3>
                <button class="close-button" id="closeEventModalBtn2">×</button>
            </div>
            <form id="closeEventForm" method="post">
                <input type="hidden" name="action" value="close_event">
                <input type="hidden" name="event_id" id="closeEventId">
                <div class="form-group">
                    <label for="close_notes" class="form-label">Catatan Penutupan (Opsional)</label>
                    <textarea id="close_notes" name="close_notes" class="form-textarea" placeholder="Masukkan catatan penutupan jika ada"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelCloseBtn">Batal</button>
                    <button type="submit" class="btn btn-primary">Tutup Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Open Add Event Modal
        const openEventModalBtn = document.getElementById('openEventModalBtn');
        const openEventModalBtn2 = document.getElementById('openEventModalBtn2');
        const addEventModal = document.getElementById('addEventModal');
        const closeEventModalBtn = document.getElementById('closeEventModalBtn');
        const cancelEventBtn = document.getElementById('cancelEventBtn');

        if (openEventModalBtn) {
            openEventModalBtn.addEventListener('click', () => {
                addEventModal.style.display = 'block';
            });
        }

        if (openEventModalBtn2) {
            openEventModalBtn2.addEventListener('click', () => {
                addEventModal.style.display = 'block';
            });
        }

        if (closeEventModalBtn) {
            closeEventModalBtn.addEventListener('click', () => {
                addEventModal.style.display = 'none';
            });
        }

        if (cancelEventBtn) {
            cancelEventBtn.addEventListener('click', () => {
                addEventModal.style.display = 'none';
            });
        }

        // Handle event form submission
        const addEventForm = document.getElementById('addEventForm');
        if (addEventForm) {
            addEventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const judulEvent = document.getElementById('judul_event').value;
                const jenisKegiatan = document.getElementById('jenis_kegiatan').value;
                const totalPembiayaan = document.getElementById('total_pembiayaan').value;
                const deskripsi = document.getElementById('deskripsi').value;
                const proposalFile = document.getElementById('proposal_file').files[0];
                
                if (!judulEvent || !jenisKegiatan || !totalPembiayaan || !deskripsi || !proposalFile) {
                    alert('Semua field harus diisi');
                    return;
                }
                
                // Check file size (5MB limit)
                if (proposalFile.size > 5 * 1024 * 1024) {
                    alert('Ukuran file tidak boleh lebih dari 5MB');
                    return;
                }
                
                // Set hidden input for file name
                document.getElementById('proposal_file_name').value = proposalFile.name;
                
                // Submit the form
                this.submit();
            });
        }

        // View event details
        function viewEventDetail(eventId) {
            const events = <?php echo json_encode($pengajuanData); ?>;
            const event = events.find(e => e.event_id == eventId);
            
            if (event) {
                const content = `
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Judul Event</div>
                            <div class="detail-value">${event.judul_event}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Jenis Kegiatan</div>
                            <div class="detail-value">${event.jenis_kegiatan}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total Pembiayaan</div>
                            <div class="detail-value">Rp ${new Intl.NumberFormat('id-ID').format(event.total_pembiayaan)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tanggal Pengajuan</div>
                            <div class="detail-value">${new Date(event.tanggal_pengajuan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">${event.tanggal_closed && event.status === 'disetujui' ? 'Closed' : event.status.charAt(0).toUpperCase() + event.status.slice(1)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Proposal</div>
                            <div class="detail-value">${event.proposal}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Deskripsi</div>
                        <div class="detail-value">${event.deskripsi}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Catatan Admin</div>
                        <div class="detail-value">${event.catatan_admin || '-'}</div>
                    </div>
                `;
                document.getElementById('eventDetailContent').innerHTML = content;
                document.getElementById('eventDetailModal').style.display = 'block';
            }
        }

        const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
        if (closeDetailModalBtn) {
            closeDetailModalBtn.addEventListener('click', () => {
                document.getElementById('eventDetailModal').style.display = 'none';
            });
        }

        // Close event
        function closeEvent(eventId) {
            document.getElementById('closeEventId').value = eventId;
            document.getElementById('closeEventModal').style.display = 'block';
        }

        const closeEventModalBtn2 = document.getElementById('closeEventModalBtn2');
        const cancelCloseBtn = document.getElementById('cancelCloseBtn');
        
        if (closeEventModalBtn2) {
            closeEventModalBtn2.addEventListener('click', () => {
                document.getElementById('closeEventModal').style.display = 'none';
            });
        }
        
        if (cancelCloseBtn) {
            cancelCloseBtn.addEventListener('click', () => {
                document.getElementById('closeEventModal').style.display = 'none';
            });
        }

        // Show toast if success
        <?php if (isset($response) && $response['success']): ?>
            showToast('<?php echo $response['message']; ?>');
        <?php endif; ?>
    </script>
</body>
</html>