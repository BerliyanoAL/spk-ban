<?php
// pages/hitung.php - VERSI FINAL: BOBOT 0.1-1.0 & HASIL AKHIR NORMALISASI KE 1.00

// --- LOGIKA PEMROSESAN FORM ---
 $error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $ukuran_dipilih = $_POST['ukuran'] ?? '';
    $bobot_input = $_POST['bobot'] ?? [];

    // Validasi
    if (empty($ukuran_dipilih)) {
        $error_message = "Silakan pilih ukuran ban.";
    } elseif (empty($bobot_input)) {
        $error_message = "Silakan masukkan bobot untuk semua kriteria.";
    } else {
        // Validasi setiap bobot harus antara 0.1 dan 1.0
        foreach ($bobot_input as $bobot) {
            if (!is_numeric($bobot) || $bobot < 0.1 || $bobot > 1.0) {
                $error_message = "Bobot harus berupa angka antara 0.1 dan 1.0.";
                break;
            }
        }
    }

    // Jika tidak ada error, lakukan perhitungan
    if (!$error_message) {
        // --- AMBIL DATA MASTER ---
        $kriteria_query = $koneksi->query("SELECT * FROM kriteria");
        $kriteria_list = [];
        while ($k = $kriteria_query->fetch_assoc()) {
            $kriteria_list[$k['id_kriteria']] = $k;
        }

        // --- AMBIL DATA BAN (ALTERNATIF) BERDASARKAN UKURAN ---
        $stmt = $koneksi->prepare("
            SELECT b.id_ban, b.harga, b.ukuran, m.nama_merek, m.keunggulan, m.kelemahan
            FROM ban b
            JOIN merek m ON b.id_merek = m.id_merek
            WHERE b.ukuran = ?
        ");
        $stmt->bind_param("s", $ukuran_dipilih);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $alternatif_data = [];
        while ($ban = $result->fetch_assoc()) {
            $alternatif_data[$ban['id_ban']] = $ban;
        }

        if (empty($alternatif_data)) {
            $error_message = "Tidak ada data ban untuk ukuran: " . htmlspecialchars($ukuran_dipilih);
        } else {
            // --- LOGIKA PERHITUNGAN SAW (LANGKAH 1: MENGHITUNG SKOR AWAL) ---
            $id_bans = array_keys($alternatif_data);
            $id_kriterias = array_keys($kriteria_list);
            $nilai_asli = [];

            foreach ($id_bans as $id_ban) {
                foreach ($id_kriterias as $id_kriteria) {
                    $nilai_asli[$id_ban][$id_kriteria] = 0;
                }
                // Asumsi ID kriteria Harga adalah 1. Sesuaikan jika berbeda.
                if (isset($alternatif_data[$id_ban]['harga'])) {
                    $nilai_asli[$id_ban][1] = $alternatif_data[$id_ban]['harga']; 
                }
                
                $stmt_nilai = $koneksi->prepare("SELECT id_kriteria, nilai FROM penilaian WHERE id_ban = ?");
                $stmt_nilai->bind_param("i", $id_ban);
                $stmt_nilai->execute();
                $penilaian_result = $stmt_nilai->get_result();
                while ($p = $penilaian_result->fetch_assoc()) {
                    $nilai_asli[$id_ban][$p['id_kriteria']] = $p['nilai'];
                }
            }

            // Normalisasi matriks kriteria
            $normalisasi = [];
            foreach ($id_kriterias as $id_kriteria) {
                $atribut = $kriteria_list[$id_kriteria]['atribut'];
                $nilai_kolom = array_column($nilai_asli, $id_kriteria);
                
                if (array_sum($nilai_kolom) == 0) continue;

                if ($atribut == 'benefit') {
                    $max = max($nilai_kolom);
                    if ($max > 0) {
                        foreach ($id_bans as $id_ban) {
                            $normalisasi[$id_ban][$id_kriteria] = $nilai_asli[$id_ban][$id_kriteria] / $max;
                        }
                    }
                } else { // 'cost'
                    $min = min(array_filter($nilai_kolom));
                    if ($min > 0) {
                        foreach ($id_bans as $id_ban) {
                             $normalisasi[$id_ban][$id_kriteria] = ($nilai_asli[$id_ban][$id_kriteria] > 0) ? $min / $nilai_asli[$id_ban][$id_kriteria] : 0;
                        }
                    }
                }
            }

            // Hitung bobot normalisasi dari input user
            $total_bobot_input = array_sum($bobot_input);
            $bobot_normalisasi = [];
            foreach ($bobot_input as $id_kriteria => $nilai) {
                $bobot_normalisasi[$id_kriteria] = $nilai / $total_bobot_input;
            }

            // Hitung skor awal (preferensi) sebelum dinormalisasi ke 1.00
            $hasil_tampung = [];
            foreach ($id_bans as $id_ban) {
                $total = 0;
                foreach ($id_kriterias as $id_kriteria) {
                    $total += $normalisasi[$id_ban][$id_kriteria] * $bobot_normalisasi[$id_kriteria];
                }
                $hasil_tampung[$id_ban] = [
                    'nilai_akhir' => $total, 
                    'data' => $alternatif_data[$id_ban]
                ];
            }

            // Urutkan hasil dari nilai terbesar ke terkecil
            uasort($hasil_tampung, function ($a, $b) {
                return $b['nilai_akhir'] <=> $a['nilai_akhir'];
            });

            // --- LANGKAH 2: NORMALISASI SKOR AKHIR AGAR YANG TERBAIK MENJADI 1.00 ---
            if (!empty($hasil_tampung)) {
                // Ambil skor tertinggi dari hasil yang sudah diurutkan
                $skor_tertinggi = reset($hasil_tampung)['nilai_akhir'];
                
                // Pastikan skor tertinggi tidak nol untuk menghindari pembagian dengan nol
                if ($skor_tertinggi > 0) {
                    // Loop melalui setiap hasil dan bagi dengan skor tertinggi
                    foreach ($hasil_tampung as $id_ban => &$data) {
                        // Gunakan reference (&) untuk mengubah nilai langsung di dalam array
                        $data['nilai_akhir'] = $data['nilai_akhir'] / $skor_tertinggi;
                    }
                    unset($data); // Lepaskan reference setelah selesai
                }
            }

            // --- SIMPAN HASIL KE SESSION ---
            $_SESSION['hasil_akhir'] = $hasil_tampung;
            $_SESSION['rekomendasi_terbaik'] = reset($hasil_tampung);
            $_SESSION['ukuran_dipilih'] = $ukuran_dipilih;
            
            header("Location: index.php?page=hasil");
            exit();
        }
    }
}

// --- JIKA BUKAN POST, TAMPILKAN FORM ---
// Ambil data master untuk form
 $kriteria_query = $koneksi->query("SELECT * FROM kriteria");
 $kriteria_list = [];
while ($k = $kriteria_query->fetch_assoc()) {
    $kriteria_list[$k['id_kriteria']] = $k;
}
 $ukuran_query = $koneksi->query("SELECT DISTINCT ukuran FROM ban ORDER BY ukuran");
 $ukuran_list = $ukuran_query->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-content">
    <h1>Hitung Rekomendasi Ban</h1>
    <?php if ($error_message): ?>
        <div class="alert"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="index.php?page=hitung" method="post">
        <h2>Masukkan Kriteria Pilihan</h2>
        <div class="form-group">
            <label for="ukuran">Pilih Ukuran Ban:</label>
            <select name="ukuran" id="ukuran" required>
                <option value="">-- Pilih Ukuran --</option>
                <?php foreach ($ukuran_list as $ukuran): ?>
                    <option value="<?= htmlspecialchars($ukuran['ukuran']) ?>"><?= htmlspecialchars($ukuran['ukuran']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
             <label><strong>Masukkan Bobot Prioritas (Rentang: 0.1 - 1.0)</strong></label>
             <p><em>Masukkan angka prioritas. Semakin besar angka, semakin penting kriteria tersebut. Contoh: Grip=1.0, Harga=0.8, Kenyamanan=0.5.</em></p>
        </div>

        <?php foreach ($kriteria_list as $kriteria): ?>
            <div class="form-group">
                <label for="bobot_<?= $kriteria['id_kriteria'] ?>">Bobot Prioritas <?= htmlspecialchars($kriteria['nama_kriteria']) ?>:</label>
                <input type="number" name="bobot[<?= $kriteria['id_kriteria'] ?>]" id="bobot_<?= $kriteria['id_kriteria'] ?>" 
                       value="<?= htmlspecialchars($kriteria['bobot_default']) ?>" 
                       min="0.1" max="1.0" step="0.1" required>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="hitung" class="btn">Hitung Rekomendasi</button>
    </form>
</div>