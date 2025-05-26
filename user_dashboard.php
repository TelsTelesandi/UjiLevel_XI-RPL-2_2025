<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya sesuai
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

// Data user yang sedang login
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Ambil data pengajuan event milik user dari database
$sql = "SELECT event_id, judul_event, tanggal_pengajuan, status FROM event_pengajuan WHERE user_id = ? ORDER BY tanggal_pengajuan DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$pengajuan_user = [];
$summary = [
    'total' => 0,
    'menunggu' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'closed' => 0,
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pengajuan_user[] = $row;
        $summary['total']++;
        if (isset($summary[$row['status']])) {
            $summary[$row['status']]++;
        }
    }
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Aplikasi Pengajuan Event Ekstrakurikuler</title>
    <!-- Link Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
     <style>
        /* Optional: Custom styles if needed, but try to use Tailwind utility classes */
        /* .some-custom-class { ... } */
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-4xl font-extrabold text-gray-800 text-center mb-8">Dashboard Pengguna</h1>
        <p class="text-lg text-gray-700 mb-6">Selamat datang, <span class="font-semibold"><?php echo htmlspecialchars($username); ?></span>!</p>
        
        <!-- Ringkasan Data Pengajuan -->
        <div class="mt-6 mb-8 p-6 bg-blue-50 rounded-lg border border-blue-200 shadow-sm">
            <h2 class="text-2xl font-bold text-blue-800 mb-6">Ringkasan Pengajuan Event Anda</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600 text-sm mb-1">Total</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $summary['total']; ?></p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600 text-sm mb-1">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $summary['menunggu']; ?></p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600 text-sm mb-1">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $summary['disetujui']; ?></p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600 text-sm mb-1">Ditolak</p>
                    <p class="text-3xl font-bold text-red-600"><?php echo $summary['ditolak']; ?></p>
                </div>
                 <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-gray-600 text-sm mb-1">Closed</p>
                    <p class="text-3xl font-bold text-gray-600"><?php echo $summary['closed']; ?></p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Pengajuan Event Saya</h2>
            
            <a href="create_request.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg mb-6 transition duration-200 ease-in-out">+ Ajukan Event Baru</a>

            <?php if (count($pengajuan_user) > 0): ?>
                <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Event</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pengajuan_user as $pengajuan): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($pengajuan['tanggal_pengajuan']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                         <?php
                                            $status_class = '';
                                            $status_text = ucfirst($pengajuan['status']);
                                            switch ($pengajuan['status']) {
                                                case 'menunggu':
                                                    $status_class = 'text-yellow-600 font-semibold';
                                                    break;
                                                case 'disetujui':
                                                    $status_class = 'text-green-600 font-semibold';
                                                    break;
                                                case 'ditolak':
                                                    $status_class = 'text-red-600 font-semibold';
                                                    break;
                                                case 'closed':
                                                    $status_class = 'text-gray-600 font-semibold';
                                                    break;
                                                default:
                                                     $status_class = 'text-gray-600';
                                            }
                                         ?>
                                         <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($status_text); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if ($pengajuan['status'] !== 'closed' && $pengajuan['status'] !== 'ditolak'): ?>
                                            <a href="close_request.php?event_id=<?php echo $pengajuan['event_id']; ?>" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-200 ease-in-out" onclick="return confirm('Anda yakin ingin menandai event ini selesai (Closed)?');">
                                                Tandai Selesai
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-700 italic">Anda belum mengajukan event apapun. Klik tombol "+ Ajukan Event Baru" di atas untuk membuat pengajuan pertama Anda.</p>
            <?php endif; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="logout.php" class="inline-block bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-200 ease-in-out">Logout</a>
        </div>
    </div>

</body>
</html> 