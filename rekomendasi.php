<?php
// pages/rekomendasi.php
if (!isset($_SESSION['rekomendasi_terbaik'])) {
    header("Location: index.php?page=hitung");
    exit();
}
 $terbaik = $_SESSION['rekomendasi_terbaik'];
 $ukuran_dipilih = $_SESSION['ukuran_dipilih'];
?>

<div class="page-content">
    <div class="best-rec-card">
        <h1>Rekomendasi Terbaik Untuk Anda</h1>
        <h2><?= htmlspecialchars($terbaik['data']['nama_merek']) ?></h2>
        <p>Untuk ukuran <strong><?= htmlspecialchars($ukuran_dipilih) ?></strong>, berdasarkan perhitungan metode SAW, ban ini adalah pilihan yang paling direkomendasikan dengan nilai akhir <strong><?= number_format($terbaik['nilai_akhir'], 2) ?></strong>.</p>
    </div>

    <div class="result-info" style="margin-top: 40px;">
        <h3>Keunggulan</h3>
        <p><?= htmlspecialchars($terbaik['data']['keunggulan']) ?></p>
        <h3>Kelemahan</h3>
        <p><?= htmlspecialchars($terbaik['data']['kelemahan']) ?></p>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php?page=hitung" class="btn">Hitung Ulang</a>
    </div>
</div>