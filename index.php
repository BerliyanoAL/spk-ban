<?php
// index.php - Router yang Diperbaiki

// Mulai session di BARIS PALING ATAS
session_start();

require_once __DIR__ . '/koneksi.php';

 $page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Pemilihan Ban</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main>
        <?php
        switch ($page) {
            case 'data-ban': include __DIR__ . '/pages/data-ban.php'; break;
            case 'hitung': include __DIR__ . '/pages/hitung.php'; break;
            case 'hasil': include __DIR__ . '/pages/hasil.php'; break;
            case 'rekomendasi': include __DIR__ . '/pages/rekomendasi.php'; break;
            case 'home':
            default: include __DIR__ . '/pages/home.php'; break;
        }
        ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#banTable tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchText) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>