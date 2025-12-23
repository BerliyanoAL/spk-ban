<!-- pages/home.php -->
<div class="page-content">
    <div class="hero">
        <h1>Sistem Pendukung Keputusan Pemilihan Ban</h1>
        <p>Temukan ban sepeda motor terbaik sesuai kebutuhan Anda dengan metode perhitungan yang akurat andal.</p>
    </div>

    <div class="feature-cards">
        <a href="index.php?page=data-ban" class="card">
            <div class="card-icon">📊</div>
            <h3>Data Ban</h3>
            <p>Lihat daftar lengkap ban yang tersedia dalam sistem kami, lengkap dengan merek, ukuran, dan harga.</p>
        </a>
        <a href="index.php?page=hitung" class="card">
            <div class="card-icon">🧮</div>
            <h3>Hitung Rekomendasi</h3>
            <p>Masukkan preferensi Anda dan dapatkan rekomendasi ban yang dipersonalisasi melalui perhitungan SAW.</p>
        </a>
        <?php if(isset($_SESSION['hasil_akhir'])): ?>
        <a href="index.php?page=hasil" class="card">
            <div class="card-icon">📈</div>
            <h3>Lihat Hasil</h3>
            <p>Lihat peringkat lengkap dan grafik perbandingan dari hasil perhitungan terakhir Anda.</p>
        </a>
        <?php endif; ?>
    </div>
</div>