<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';
?>
<main>
    <section class="dashboard-section" style="max-width:600px; margin:40px auto;">
        <h2 style="color:#2563eb; margin-bottom:18px;"><i class="fas fa-file-alt"></i> Kebijakan Privasi</h2>
        <p style="font-size:1.08em; color:#333;">Kami sangat menjaga kerahasiaan dan keamanan data pribadi Anda. Data yang dikumpulkan melalui sistem ini hanya digunakan untuk keperluan pengajuan dan manajemen event ekstrakurikuler di lingkungan SMA Negeri 1 Contoh Kota.</p>
        <ul style="margin-bottom:18px; color:#444;">
            <li>Data pribadi seperti nama, email, dan kontak hanya digunakan untuk identifikasi dan komunikasi terkait event.</li>
            <li>Data tidak akan dibagikan ke pihak ketiga tanpa izin tertulis dari pemilik data.</li>
            <li>Setiap pengguna bertanggung jawab menjaga kerahasiaan akun dan password masing-masing.</li>
            <li>Jika ada pertanyaan atau permintaan penghapusan data, silakan hubungi admin sekolah.</li>
        </ul>
        <p style="color:#555;">Dengan menggunakan sistem ini, Anda menyetujui kebijakan privasi yang berlaku.</p>
        <div style="margin-top:28px; text-align:right;">
            <a href="dashboard.php" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
        </div>
    </section>
</main>
<?php include '../includes/footer.php'; ?> 