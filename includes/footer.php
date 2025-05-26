<footer class="app-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>Tentang Sistem</h4>
            <p>Sistem Pengajuan Event Ekstrakurikuler SMK Telesandi Bekasi merupakan platform digital untuk memudahkan pengajuan dan manajemen kegiatan ekstrakurikuler.</p>
        </div>
        <div class="footer-section">
            <h4>Kontak</h4>
            <ul class="contact-info">
                <li><i class="fas fa-map-marker-alt"></i> Jl. Pendidikan No. 123, kota bekasi</li>
                <li><i class="fas fa-phone"></i> (021) 12345678</li>
                <li><i class="fas fa-envelope"></i> ekskul@smktelesandi.sch.id</li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Link Cepat</h4>
            <ul class="quick-links">
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="../register.php"><i class="fas fa-user-plus"></i> Registrasi</a></li>
                <li><a href="#"><i class="fas fa-file-alt"></i> Kebijakan Privasi</a></li>
            </ul>
            <div class="footer-social">
                <a href="https://instagram.com/akun_sekolah" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://facebook.com/akun_sekolah" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/akun_sekolah" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://youtube.com/akun_sekolah" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
    <nav class="footer-navbar">
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
            <li><a href="../login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="../register.php"><i class="fas fa-user-plus"></i> Registrasi</a></li>
            <li><a href="kebijakan_privasi.php"><i class="fas fa-file-alt"></i> Kebijakan Privasi</a></li>
        </ul>
    </nav>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> SMK Telekomunikasi Telesandi Bekasi. All Rights Reserved.</p>
    </div>
</footer>

<!-- Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Tambahkan sebelum tag </body> -->
<script src="../assets/js/script.js"></script>

<!-- Modal Kebijakan Privasi -->
<div id="privacy-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35);">
  <div style="background:#fff; max-width:480px; margin:60px auto; padding:28px 22px; border-radius:10px; box-shadow:0 2px 16px rgba(44,62,80,0.13); position:relative;">
    <h2 style="margin-top:0; color:#2563eb;">Kebijakan Privasi</h2>
    <p style="font-size:1.05em; color:#333;">
      Kami menjaga kerahasiaan data pribadi Anda. Data yang dikumpulkan hanya digunakan untuk keperluan pengajuan event dan tidak akan dibagikan ke pihak ketiga tanpa izin. Untuk info lebih lanjut, hubungi admin sekolah.
    </p>
    <button onclick="document.getElementById('privacy-modal').style.display='none'" style="margin-top:18px; padding:8px 18px; border:none; background:#2563eb; color:#fff; border-radius:6px; cursor:pointer;">Tutup</button>
  </div>
</div>
<script>
var privacyLink = document.getElementById('privacy-link');
if (privacyLink) {
  privacyLink.onclick = function(e) {
    e.preventDefault();
    document.getElementById('privacy-modal').style.display = 'block';
  };
}
</script>
</body>
</html>