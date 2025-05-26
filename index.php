<?php
session_start();

// Jika user sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}

$page_title = "Beranda - Sistem Event Ekstrakurikuler";
include 'includes/header.php';
?>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
    
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>SISTEM PENGAJUAN EVENT EKSTRAKURIKULER</h1>
        </header>

        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Daftar</a></li>
                <li><a href="#tentang">Tentang</a></li>
        </nav>

        <main>
            <section class="hero-section">
                <div class="hero-content">
                    <h2>Selamat Datang di Sistem Pengajuan Event Ekstrakurikuler</h2>
                    <p>Platform digital untuk memudahkan pengajuan dan manajemen kegiatan ekstrakurikuler di SMA Negeri 1 Contoh Kota</p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn btn-primary">Login Sekarang</a>
                        <a href="register.php" class="btn btn-secondary">Daftar Akun</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/hero-image.png" alt="Kegiatan Ekstrakurikuler">
                </div>
            </section>

            <section class="features-section" id="fitur">
                <h2>Fitur Unggulan</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Pengajuan Online</h3>
                        <p>Ajukan kegiatan ekstrakurikuler kapan saja dan dari mana saja secara online tanpa perlu tatap muka.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Proses Cepat</h3>
                        <p>Proses persetujuan yang lebih cepat dengan sistem notifikasi real-time.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3>Upload Proposal</h3>
                        <p>Lampirkan proposal kegiatan dalam format PDF untuk proses evaluasi yang lebih baik.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notifikasi</h3>
                        <p>Dapatkan pemberitahuan status pengajuan event Anda secara real-time.</p>
                    </div>
                </div>
            </section>

            <section class="about-section" id="tentang">
                <div class="about-content">
                    <h2>Tentang Sistem Ini</h2>
                    <p>Sistem Pengajuan Event Ekstrakurikuler SMA Negeri 1 Contoh Kota dikembangkan untuk memenuhi kebutuhan digitalisasi proses administrasi kegiatan ekstrakurikuler di sekolah.</p>
                    <p>Dengan sistem ini, seluruh proses pengajuan kegiatan yang sebelumnya dilakukan secara manual dengan banyak dokumen kertas, kini dapat dilakukan secara digital dengan lebih efisien dan transparan.</p>
                    <p>Sistem ini mendukung berbagai jenis ekstrakurikuler yang ada di sekolah kami, termasuk Pramuka, PMR, Paskibra, KIR, Olahraga, Seni dan Budaya, serta ekskul lainnya.</p>
                </div>
                <div class="about-image">
                    <img src="assets/images/about-image.jpg" alt="Tentang Sistem">
                </div>
            </section>

            <section class="testimonial-section">
                <h2>Apa Kata Mereka?</h2>
                <div class="testimonials">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Sistem ini sangat membantu kami dalam mengajukan kegiatan Pramuka. Prosesnya jadi lebih cepat dan mudah dilacak."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-name">Budi Santoso</div>
                            <div class="author-role">Ketua Ekskul Pramuka</div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Sebagai pembina, saya bisa memantau semua pengajuan kegiatan dari berbagai ekskul dengan lebih terorganisir."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-name">Ibu Siti Aminah</div>
                            <div class="author-role">Pembina Ekstrakurikuler</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- Modal Kebijakan Privasi -->
    <div id="privacy-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35);">
      <div style="background:#fff; max-width:480px; margin:60px auto; padding:28px 22px; border-radius:10px; box-shadow:0 2px 16px rgba(44,62,80,0.13); position:relative;">
        <h2 style="margin-top:0; color:#2563eb;">Kebijakan Privasi</h2>
        <p style="font-size:1.05em; color:#333;">Kami menjaga kerahasiaan data pribadi Anda. Data yang dikumpulkan hanya digunakan untuk keperluan pengajuan event dan tidak akan dibagikan ke pihak ketiga tanpa izin. Untuk info lebih lanjut, hubungi admin sekolah.</p>
        <button onclick="document.getElementById('privacy-modal').style.display='none'" style="margin-top:18px; padding:8px 18px; border:none; background:#2563eb; color:#fff; border-radius:6px; cursor:pointer;">Tutup</button>
      </div>
    </div>
    <script>
    document.getElementById('privacy-link').onclick = function(e) {
      e.preventDefault();
      document.getElementById('privacy-modal').style.display = 'block';
    };
    </script>
</body>
</html>