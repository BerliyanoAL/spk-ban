<?php
// pages/hasil.php - Halaman untuk menampilkan tabel peringkat lengkap dan grafik

// Periksa apakah hasil ada di session
if (!isset($_SESSION['hasil_akhir']) || empty($_SESSION['hasil_akhir'])) {
    // Jika tidak ada, redirect ke halaman hitung
    header("Location: index.php?page=hitung");
    exit();
}

 $hasil_akhir = $_SESSION['hasil_akhir'];
 $ukuran_dipilih = $_SESSION['ukuran_dipilih'];

// --- Siapkan data untuk grafik ---
// Inisialisasi array untuk mencegah error jika data kosong
 $chart_labels = [];
 $chart_data = [];

if (!empty($hasil_akhir)) {
    foreach ($hasil_akhir as $hasil) {
        // Pastikan kunci 'data' dan 'nama_merek' ada untuk menghindari error
        if (isset($hasil['data']['nama_merek'])) {
            $chart_labels[] = htmlspecialchars($hasil['data']['nama_merek']);
        }
        // Pastikan kunci 'nilai_akhir' ada dan merupakan angka
        if (isset($hasil['nilai_akhir'])) {
            $chart_data[] = (float) $hasil['nilai_akhir']; 
        }
    }
}
?>

<div class="page-content">
    <h1>Hasil Peringkat Lengkap</h1>
    <p>Berikut adalah peringkat lengkap ban motor untuk ukuran <strong><?= htmlspecialchars($ukuran_dipilih) ?></strong> berdasarkan kriteria yang Anda masukkan.</p>

    <a href="index.php?page=rekomendasi_terbaik" class="btn btn-success mb-3">Lihat Rekomendasi Terbaik</a>

    <!-- Tampilkan Semua Hasil Peringkat -->
    <div class="card all-rankings mb-4">
        <div class="card-header">
            <h2>Daftar Peringkat</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Merek</th>
                        <th>Ukuran</th>
                        <th>Harga</th>
                        <th>Skor Kecocokan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($hasil_akhir as $hasil): 
                    ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($hasil['data']['nama_merek']) ?></td>
                        <td><?= htmlspecialchars($hasil['data']['ukuran']) ?></td>
                        <td>Rp <?= number_format($hasil['data']['harga'], 0, ',', '.') ?></td>
                        <td>
                            <strong>
                                <?= number_format($hasil['nilai_akhir'], 1) ?>
                            </strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <hr>

    <!-- GRAFIK -->
    <div class="card">
        <div class="card-header">
            <h2>Grafik Peringkat Ban</h2>
        </div>
        <div class="card-body">
            <div style="height: 400px; position: relative;">
                <canvas id="peringkatChart"></canvas>
            </div>
        </div>
    </div>
    
    <hr>
    <a href="index.php?page=hitung" class="btn btn-secondary">Hitung Ulang</a>
</div>

<!-- Kode JavaScript untuk membuat grafik -->
<script>
    // Tunggu hingga seluruh konten HTML halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        
        // Ambil elemen canvas
        const ctx = document.getElementById('peringkatChart');
        if (!ctx) {
            console.error("Elemen canvas dengan ID 'peringkatChart' tidak ditemukan.");
            return; // Hentikan eksekusi jika canvas tidak ada
        }

        // Ambil data dari PHP dan konversi ke format JavaScript
        // Jika data kosong, beri tahu pengguna melalui console
        const chartLabels = <?php echo !empty($chart_labels) ? json_encode($chart_labels) : '[]'; ?>;
        const chartScores = <?php echo !empty($chart_data) ? json_encode($chart_data) : '[]'; ?>;
        
        if(chartLabels.length === 0 || chartScores.length === 0) {
            console.error("Data untuk grafik kosong. Periksa kembali perhitungan.");
            // Mungkin tampilkan pesan di halaman
            const chartContainer = ctx.parentElement;
            chartContainer.innerHTML = '<p class="text-center text-muted">Tidak cukup data untuk menampilkan grafik.</p>';
            return; // Hentikan eksekusi
        }

        // Buat grafik baru
        const peringkatChart = new Chart(ctx, {
            type: 'bar', // Tipe grafik: batang
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Skor Kecocokan',
                    data: chartScores,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1.1,
                        title: {
                            display: true,
                            text: 'Skor Kecocokan'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Merek Ban'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Perbandingan Skor Kecocokan Ban',
                        font: {
                            size: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(1);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>